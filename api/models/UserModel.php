<?php
// ============================================================
// User Model
// Web-Based Crop Insurance System
// ============================================================

require_once __DIR__ . '/BaseModel.php';

class UserModel extends BaseModel {
    protected string $table = 'users';

    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?array {
        return $this->findBy('email', $email);
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
        unset($user['password'], $user['reset_token'], $user['reset_expires']);
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
            'password'      => password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]),
            'reset_token'   => null,
            'reset_expires' => null,
        ]);
    }

    /**
     * Mark email as verified
     */
    public function verifyEmail(int $userId): bool {
        return $this->update($userId, ['email_verified' => 1]);
    }

    /**
     * Check if email already exists
     */
    public function emailExists(string $email): bool {
        return $this->count('email = ?', [$email]) > 0;
    }
}
