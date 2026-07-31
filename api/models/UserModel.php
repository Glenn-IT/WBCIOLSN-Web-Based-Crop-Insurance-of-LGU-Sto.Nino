<?php
// ============================================================
// User Model
// Web-Based Crop Insurance System
// ============================================================

require_once __DIR__ . '/BaseModel.php';

class UserModel extends BaseModel {
    protected string $table = 'users';

    /**
     * Find user by email (case-sensitive exact match)
     */
    public function findByEmail(string $email): ?array {
        return $this->rawOne(
            "SELECT * FROM `{$this->table}` WHERE BINARY email = ? LIMIT 1",
            [$email]
        );
    }

    /**
     * Create a new user (hashes password automatically)
     */
    public function createUser(array $data): int {
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        return $this->insert($data);
    }

    /**
     * Verify a plain password against stored hash
     */
    public function verifyPassword(string $plain, string $hash): bool {
        return password_verify($plain, $hash);
    }

    /**
     * Get user safe for public response (no password, no reset tokens)
     */
    public function sanitize(array $user): array {
        unset($user['password'], $user['reset_token'], $user['reset_expires'], $user['security_answer_hash']);
        return $user;
    }

    /**
     * Set a password reset token (hashed) with expiry
     */
    public function setResetToken(int $userId, string $token): bool {
        return $this->update($userId, [
            'reset_token'   => hash('sha256', $token),
            'reset_expires' => date('Y-m-d H:i:s', strtotime('+1 hour')),
        ]);
    }

    /**
     * Find user by valid (non-expired) reset token
     */
    public function findByResetToken(string $token): ?array {
        return $this->rawOne(
            "SELECT * FROM users
             WHERE reset_token = ? AND reset_expires > NOW() LIMIT 1",
            [hash('sha256', $token)]
        );
    }

    /**
     * Update password and clear reset token
     */
    public function resetPassword(int $userId, string $newPassword): bool {
        return $this->update($userId, [
            'password'              => password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]),
            'reset_token'           => null,
            'reset_expires'         => null,
            'must_change_password'  => 0,
        ]);
    }

    /**
     * Mark email as verified
     */
    public function verifyEmail(int $userId): bool {
        return $this->update($userId, ['email_verified' => 1]);
    }

    /**
     * Check if email already exists (case-sensitive exact match)
     */
    public function emailExists(string $email): bool {
        return $this->count('BINARY email = ?', [$email]) > 0;
    }

    /**
     * Register a failed login attempt for the given user. Locks the account for
     * $lockSeconds once $maxAttempts consecutive failures are reached.
     * Returns ['locked' => bool, 'attempts_left' => int, 'locked_seconds' => int]
     */
    public function registerFailedAttempt(array $user, int $maxAttempts = 3, int $lockSeconds = 30): array {
        $attempts = (int) $user['failed_attempts'] + 1;

        if ($attempts >= $maxAttempts) {
            $this->update($user['id'], [
                'failed_attempts' => 0,
                'locked_until'    => date('Y-m-d H:i:s', strtotime("+{$lockSeconds} seconds")),
            ]);
            return ['locked' => true, 'attempts_left' => 0, 'locked_seconds' => $lockSeconds];
        }

        $this->update($user['id'], ['failed_attempts' => $attempts]);
        return ['locked' => false, 'attempts_left' => $maxAttempts - $attempts, 'locked_seconds' => 0];
    }

    /**
     * Reset failed-attempt counter and lock on successful login
     */
    public function clearFailedAttempts(int $userId): bool {
        return $this->update($userId, ['failed_attempts' => 0, 'locked_until' => null]);
    }

    /**
     * Seconds remaining on an active lockout (0 if not locked)
     */
    public function lockedSecondsRemaining(array $user): int {
        if (empty($user['locked_until'])) return 0;
        $remaining = strtotime($user['locked_until']) - time();
        return $remaining > 0 ? $remaining : 0;
    }

    /**
     * Set the security question and hashed answer for a user
     */
    public function setSecurityQuestion(int $userId, string $question, string $answer): bool {
        return $this->update($userId, [
            'security_question'    => $question,
            'security_answer_hash' => password_hash(strtolower(trim($answer)), PASSWORD_BCRYPT, ['cost' => 12]),
        ]);
    }

    /**
     * Verify a security answer against the stored hash
     */
    public function verifySecurityAnswer(array $user, string $answer): bool {
        if (empty($user['security_answer_hash'])) return false;
        return password_verify(strtolower(trim($answer)), $user['security_answer_hash']);
    }
}
