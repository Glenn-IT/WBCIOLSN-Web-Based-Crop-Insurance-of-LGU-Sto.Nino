<?php
// ============================================================
// Auth Controller
// Handles: register, login, logout, forgot-password,
//          reset-password
// Web-Based Crop Insurance System
// ============================================================

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/OtpModel.php';
require_once __DIR__ . '/../helpers/mailer.php';
require_once __DIR__ . '/../middleware/RateLimitMiddleware.php';

class AuthController extends BaseController {

    private UserModel $users;
    private OtpModel $otp;

    public function __construct() {
        $this->users = new UserModel();
        $this->otp   = new OtpModel();
    }

    // ----------------------------------------------------------
    // POST /api/auth/register
    // ----------------------------------------------------------
    public function register(array $params): void {
        rateLimit(10, 60, 'register'); // 10 registrations/min per IP

        $raw  = $this->body();
        guardSqlInjection($raw);
        
        // IMPORTANT: Do NOT sanitize password and security_answer
        $data = [
            'first_name'        => sanitize($raw['first_name'] ?? ''),
            'last_name'         => sanitize($raw['last_name'] ?? ''),
            'email'             => trim($raw['email'] ?? ''),
            'password'          => $raw['password'] ?? '',
            'phone'             => sanitize($raw['phone'] ?? ''),
            'security_question' => sanitize($raw['security_question'] ?? ''),
            'security_answer'   => $raw['security_answer'] ?? '',
        ];

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

        $email = strtolower(trim($data['email']));
        if ($this->users->emailExists($email)) {
            sendError('This email address is already registered.', 409);
        }

        $userId = $this->users->createUser([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'email'      => $email,
            'password'   => $data['password'],
            'phone'      => $data['phone'],
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

        $email = strtolower(trim($data['email']));
        $user  = $this->users->findByEmail($email);

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
    // Step 1: Generate and email a 6-digit OTP for password reset
    // ----------------------------------------------------------
    public function forgotPassword(array $params): void {
        rateLimit(5, 60, 'forgot'); // 5 requests/min per IP

        $data = $this->body();
        $this->validateOrFail($data, ['email' => 'required|email']);

        $email = strtolower(trim($data['email']));
        $user  = $this->users->findByEmail($email);

        if (!$user) {
            sendError('No account found with that email address.', 404);
        }

        if ($user['status'] === 'pending') {
            sendError('Your account is pending admin approval. You cannot reset your password yet.', 403);
        }

        if ($user['status'] !== 'active') {
            sendError('Your account has been deactivated or suspended. Please contact support.', 403);
        }

        $otp       = $this->otp->create($email, 'password_reset', 10);
        $firstName = $user['first_name'] ?: 'Farmer';
        $sent      = sendPasswordResetOtpEmail($user['email'], $firstName, $otp);

        $this->audit($user['id'], 'request_reset_otp', 'auth', 'Password reset OTP requested.');

        $resData = ['email' => $user['email']];
        // In development mode, include debug_otp if mailer cannot deliver locally
        if (defined('APP_ENV') && APP_ENV === 'development' && !$sent) {
            $resData['debug_otp'] = $otp;
        }

        sendSuccess($resData, 'A 6-digit verification code has been sent to your email.');
    }

    // ----------------------------------------------------------
    // POST /api/auth/verify-otp
    // Step 2: Verify the 6-digit OTP before allowing password change
    // ----------------------------------------------------------
    public function verifyOtp(array $params): void {
        rateLimit(10, 60, 'forgot'); // 10 attempts/min per IP

        $rawData = $this->body();
        $data = [
            'email' => strtolower(trim($rawData['email'] ?? '')),
            'otp'   => trim($rawData['otp'] ?? ''),
        ];

        $this->validateOrFail($data, [
            'email' => 'required|email',
            'otp'   => 'required|min:6|max:6',
        ]);

        $user = $this->users->findByEmail($data['email']);

        if (!$user) {
            sendError('No account found with that email address.', 404);
        }

        if ($user['status'] !== 'active') {
            sendError('Your account is not active.', 403);
        }

        if (!$this->otp->verify($data['email'], $data['otp'], 'password_reset')) {
            sendError('Invalid or expired verification code. Please check the code or request a new one.', 422);
        }

        // Generate a secure, temporary reset token (valid for 15 minutes)
        $resetToken = bin2hex(random_bytes(32));
        $this->users->setResetToken($user['id'], $resetToken);

        $this->audit($user['id'], 'verify_reset_otp', 'auth', 'Password reset OTP successfully verified.');

        sendSuccess([
            'reset_token' => $resetToken,
            'email'       => $user['email'],
        ], 'Verification code confirmed. You may now enter your new password.');
    }

    // ----------------------------------------------------------
    // POST /api/auth/reset-password
    // Step 3: Set new password with the verified reset token
    // ----------------------------------------------------------
    public function resetPassword(array $params): void {
        rateLimit(5, 60, 'forgot'); // shares the forgot-password rate limit bucket

        $rawData = $this->body();
        
        // IMPORTANT: Do NOT sanitize password
        $data = [
            'email'       => strtolower(trim($rawData['email'] ?? '')),
            'reset_token' => trim($rawData['reset_token'] ?? ''),
            'otp'         => trim($rawData['otp'] ?? ''),
            'password'    => $rawData['password'] ?? '',
        ];

        $this->validateOrFail($data, [
            'email'    => 'required|email',
            'password' => 'required|min:8|max:255',
        ]);

        // Enforce strong password
        $pwError = validatePasswordStrength($data['password']);
        if ($pwError) sendValidationError(['password' => [$pwError]]);

        $user = $this->users->findByEmail($data['email']);

        if (!$user) {
            sendError('No account found with that email address.', 404);
        }

        if ($user['status'] !== 'active') {
            sendError('Your account is not active.', 403);
        }

        // Verify either via reset_token or direct OTP fallback
        if (!empty($data['reset_token'])) {
            $userByToken = $this->users->findByResetToken($data['reset_token']);
            if (!$userByToken || $userByToken['id'] !== $user['id']) {
                sendError('Reset session has expired or is invalid. Please request a new verification code.', 422);
            }
        } elseif (!empty($data['otp'])) {
            if (!$this->otp->verify($data['email'], $data['otp'], 'password_reset')) {
                sendError('Invalid or expired verification code. Please check the code or request a new one.', 422);
            }
        } else {
            sendError('Verification code or reset token is required.', 422);
        }

        $this->users->resetPassword($user['id'], $data['password']);
        $this->users->clearFailedAttempts($user['id']);
        $this->audit($user['id'], 'reset_password_completed', 'auth', 'Password reset successfully.');

        sendSuccess(null, 'Password has been reset successfully. You can now log in.');
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
        $rawData = $this->body();
        
        // IMPORTANT: Do NOT sanitize passwords - they need exact verification
        $data = [
            'current_password' => $rawData['current_password'] ?? '',
            'new_password'     => $rawData['new_password'] ?? '',
        ];
        
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
        $rawData = $this->body();
        
        // IMPORTANT: Do NOT sanitize the password field
        $data = [
            'current_password'  => $rawData['current_password'] ?? '',
            'security_question' => sanitize($rawData['security_question'] ?? ''),
            'security_answer'   => sanitize($rawData['security_answer'] ?? ''),
        ];

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