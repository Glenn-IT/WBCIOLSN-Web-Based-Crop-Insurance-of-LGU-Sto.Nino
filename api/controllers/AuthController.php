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

        $raw  = $this->body();
        guardSqlInjection($raw);
        $data = sanitizeAll($raw);

        $this->validateOrFail($data, [
            'first_name'        => 'required|max:100',
            'last_name'         => 'required|max:100',
            'email'             => 'required|email|max:191',
            'password'          => 'required|min:8|max:255',
            'phone'             => 'max:20',
            'security_question' => 'required|max:255',
            'security_answer'   => 'required|max:255',
        ]);

        // Enforce strong password
        $pwError = validatePasswordStrength($data['password']);
        if ($pwError) sendValidationError(['password' => [$pwError]]);

        $email = trim($data['email']);
        if ($this->users->emailExists($email)) {
            sendError('This email address is already registered.', 409);
        }

        $userId = $this->users->createUser([
            'first_name' => sanitize($data['first_name']),
            'last_name'  => sanitize($data['last_name']),
            'email'      => $email,
            'password'   => $data['password'],
            'phone'      => sanitize($data['phone'] ?? ''),
            'role'       => 'farmer', // default role
            'status'     => 'pending', // requires admin approval before login
        ]);

        $this->users->setSecurityQuestion($userId, $data['security_question'], $data['security_answer']);

        $user = $this->users->find($userId);

        // Send pending-approval email (non-blocking — ignore failure)
        @sendPendingApprovalEmail($user['email'], $user['first_name']);

        $this->audit($userId, 'register', 'auth', 'New farmer registered (pending approval).');

        // No token is issued — the account cannot log in until an admin approves it.
        sendSuccess([
            'user' => $this->users->sanitize($user),
        ], 'Registration successful. Your account is pending admin approval before you can log in.', 201);
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

        $user = $this->users->findByEmail(trim($data['email']));

        if ($user) {
            $lockedSeconds = $this->users->lockedSecondsRemaining($user);
            if ($lockedSeconds > 0) {
                sendError(
                    "Too many failed attempts. Try again in {$lockedSeconds} seconds.",
                    423,
                    ['locked_seconds' => $lockedSeconds]
                );
            }
        }

        if (!$user || !$this->users->verifyPassword($data['password'], $user['password'])) {
            if ($user) {
                $result = $this->users->registerFailedAttempt($user);
                if ($result['locked']) {
                    sendError(
                        "Too many failed attempts. Your login has been locked for {$result['locked_seconds']} seconds.",
                        423,
                        ['locked_seconds' => $result['locked_seconds']]
                    );
                }
                sendError(
                    "Invalid email or password. {$result['attempts_left']} attempt(s) left before your login is locked.",
                    401,
                    ['attempts_left' => $result['attempts_left']]
                );
            }
            sendError('Invalid email or password.', 401);
        }

        if ($user['status'] === 'pending') {
            sendError('Your account is pending admin approval. Please check back later.', 403);
        }

        if ($user['status'] !== 'active') {
            sendError('Your account has been suspended or deactivated. Please contact support.', 403);
        }

        $this->users->clearFailedAttempts($user['id']);

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
    // Step 1: look up the account's security question by email
    // ----------------------------------------------------------
    public function forgotPassword(array $params): void {
        rateLimit(5, 60, 'forgot'); // 5 requests/min per IP

        $data = $this->body();
        $this->validateOrFail($data, ['email' => 'required|email']);

        $user = $this->users->findByEmail(trim($data['email']));

        if (!$user || $user['status'] !== 'active' || empty($user['security_question'])) {
            sendError('No account found with that email address.', 404);
        }

        sendSuccess(['security_question' => $user['security_question']], 'Security question retrieved.');
    }

    // ----------------------------------------------------------
    // POST /api/auth/reset-password
    // Step 2: verify the security answer and set a new password
    // ----------------------------------------------------------
    public function resetPassword(array $params): void {
        rateLimit(5, 60, 'forgot'); // shares the forgot-password rate limit bucket

        $data = $this->body();

        $this->validateOrFail($data, [
            'email'    => 'required|email',
            'answer'   => 'required',
            'password' => 'required|min:8|max:255',
        ]);

        // Enforce strong password
        $pwError = validatePasswordStrength($data['password']);
        if ($pwError) sendValidationError(['password' => [$pwError]]);

        $user = $this->users->findByEmail(trim($data['email']));

        if (!$user || !$this->users->verifySecurityAnswer($user, $data['answer'])) {
            sendError('Incorrect answer to the security question.', 401);
        }

        $this->users->resetPassword($user['id'], $data['password']);
        $this->users->clearFailedAttempts($user['id']);
        $this->audit($user['id'], 'reset_password', 'auth', 'Password was reset via security question.');

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

    // ----------------------------------------------------------
    // POST /api/auth/security-question
    // Set or update the logged-in user's security question/answer
    // ----------------------------------------------------------
    public function setSecurityQuestion(array $params): void {
        $payload = requireAuth();
        $data    = $this->body();

        $this->validateOrFail($data, [
            'current_password'  => 'required',
            'security_question' => 'required|max:255',
            'security_answer'   => 'required|max:255',
        ]);

        $user = $this->users->find($payload['id']);
        if (!$user) sendNotFound('User not found.');

        if (!password_verify($data['current_password'], $user['password'])) {
            sendError('Current password is incorrect.', 422);
        }

        $this->users->setSecurityQuestion($user['id'], $data['security_question'], $data['security_answer']);
        $this->audit($user['id'], 'set_security_question', 'auth', 'Security question set/updated.');

        sendSuccess(null, 'Security question saved successfully.');
    }
}