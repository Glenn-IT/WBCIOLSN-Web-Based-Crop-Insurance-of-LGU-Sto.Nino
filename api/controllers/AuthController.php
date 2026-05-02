<?php
// ============================================================
// Auth Controller
// Handles: register, login, logout, forgot-password,
//          reset-password
// Web-Based Crop Insurance System
// ============================================================

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../helpers/mailer.php';
require_once __DIR__ . '/../middleware/RateLimitMiddleware.php';

class AuthController extends BaseController {

    private UserModel $users;

    public function __construct() {
        $this->users = new UserModel();
    }

    // ----------------------------------------------------------
    // POST /api/auth/register
    // ----------------------------------------------------------
    public function register(array $params): void {
        rateLimit(10, 60, 'register'); // 10 registrations/min per IP

        $data = sanitizeAll($this->body());
        guardSqlInjection($data);

        $this->validateOrFail($data, [
            'first_name' => 'required|max:100',
            'last_name'  => 'required|max:100',
            'email'      => 'required|email|max:191',
            'password'   => 'required|min:8|max:255',
            'phone'      => 'max:20',
        ]);

        // Enforce strong password
        $pwError = validatePasswordStrength($data['password']);
        if ($pwError) sendValidationError(['password' => [$pwError]]);

        if ($this->users->emailExists($data['email'])) {
            sendError('This email address is already registered.', 409);
        }

        $userId = $this->users->createUser([
            'first_name' => sanitize($data['first_name']),
            'last_name'  => sanitize($data['last_name']),
            'email'      => strtolower(trim($data['email'])),
            'password'   => $data['password'],
            'phone'      => sanitize($data['phone'] ?? ''),
            'role'       => 'farmer', // default role
            'status'     => 'active',
        ]);

        $user  = $this->users->find($userId);
        $token = jwtEncode([
            'id'    => $user['id'],
            'email' => $user['email'],
            'role'  => $user['role'],
        ]);

        // Send welcome email (non-blocking — ignore failure)
        @sendWelcomeEmail($user['email'], $user['first_name']);

        $this->audit($userId, 'register', 'auth', 'New farmer registered.');

        sendSuccess([
            'user'  => $this->users->sanitize($user),
            'token' => $token,
        ], 'Registration successful.', 201);
    }

    // ----------------------------------------------------------
    // POST /api/auth/login
    // ----------------------------------------------------------
    public function login(array $params): void {
        rateLimit(20, 60, 'login'); // 20 login attempts/min per IP

        $data = $this->body();

        $this->validateOrFail($data, [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = $this->users->findByEmail(strtolower(trim($data['email'])));

        if (!$user || !$this->users->verifyPassword($data['password'], $user['password'])) {
            sendError('Invalid email or password.', 401);
        }

        if ($user['status'] !== 'active') {
            sendError('Your account has been suspended or deactivated. Please contact support.', 403);
        }

        $token = jwtEncode([
            'id'    => $user['id'],
            'email' => $user['email'],
            'role'  => $user['role'],
        ]);

        $this->audit($user['id'], 'login', 'auth', 'User logged in.');

        sendSuccess([
            'user'  => $this->users->sanitize($user),
            'token' => $token,
        ], 'Login successful.');
    }

    // ----------------------------------------------------------
    // POST /api/auth/logout
    // ----------------------------------------------------------
    public function logout(array $params): void {
        // JWT is stateless — client must discard the token.
        // Optionally implement a token blacklist table here.
        $payload = requireAuth();
        $this->audit($payload['id'], 'logout', 'auth', 'User logged out.');
        sendSuccess(null, 'Logged out successfully.');
    }

    // ----------------------------------------------------------
    // POST /api/auth/forgot-password
    // ----------------------------------------------------------
    public function forgotPassword(array $params): void {
        rateLimit(5, 60, 'forgot'); // 5 requests/min per IP

        $data = $this->body();
        $this->validateOrFail($data, ['email' => 'required|email']);

        $user = $this->users->findByEmail(strtolower(trim($data['email'])));

        // Always return success to prevent email enumeration
        if ($user && $user['status'] === 'active') {
            $token = bin2hex(random_bytes(32));
            $this->users->setResetToken($user['id'], $token);
            @sendPasswordResetEmail($user['email'], $user['first_name'], $token);
            $this->audit($user['id'], 'forgot_password', 'auth', 'Password reset requested.');
        }

        sendSuccess(null, 'If that email exists in our system, a reset link has been sent.');
    }

    // ----------------------------------------------------------
    // POST /api/auth/reset-password
    // ----------------------------------------------------------
    public function resetPassword(array $params): void {
        $data = sanitizeAll($this->body());

        $this->validateOrFail($data, [
            'token'    => 'required',
            'password' => 'required|min:8|max:255',
        ]);

        // Enforce strong password
        $pwError = validatePasswordStrength($data['password']);
        if ($pwError) sendValidationError(['password' => [$pwError]]);
        $user = $this->users->findByResetToken($data['token']);

        if (!$user) {
            sendError('This reset link is invalid or has expired.', 400);
        }

        $this->users->resetPassword($user['id'], $data['password']);
        $this->audit($user['id'], 'reset_password', 'auth', 'Password was reset.');

        sendSuccess(null, 'Password reset successfully. You can now log in.');
    }

    // ----------------------------------------------------------
    // GET /api/auth/me
    // ----------------------------------------------------------
    public function me(array $params): void {
        $payload = requireAuth();
        $user    = $this->users->find($payload['id']);

        if (!$user) sendNotFound('User not found.');

        sendSuccess($this->users->sanitize($user), 'Authenticated user retrieved.');
    }

    // ----------------------------------------------------------
    // POST /api/auth/change-password
    // ----------------------------------------------------------
    public function changePassword(array $params): void {
        $payload = requireAuth();
        $data    = sanitizeAll($this->body());
        $this->validateOrFail($data, [
            'current_password' => 'required',
            'new_password'     => 'required|min:8|max:255',
        ]);
        $pwError = validatePasswordStrength($data['new_password']);
        if ($pwError) sendValidationError(['new_password' => [$pwError]]);
        $user = $this->users->find($payload['id']);
        if (!$user) sendNotFound('User not found.');
        if (!password_verify($data['current_password'], $user['password'])) {
            sendError('Current password is incorrect.', 422);
        }
        $this->users->resetPassword($user['id'], $data['new_password']);
        $this->audit($user['id'], 'change_password', 'auth', 'Password changed by user.');
        sendSuccess(null, 'Password changed successfully.');
    }
}