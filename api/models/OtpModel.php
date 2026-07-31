<?php
// ============================================================
// OTP Model
// Handles one-time-password generation/verification for
// admin-side email confirmation before creating a new user.
// ============================================================

require_once __DIR__ . '/BaseModel.php';

class OtpModel extends BaseModel {
    protected string $table = 'otp_verifications';

    /**
     * Generate and store a new 6-digit OTP for the given email/purpose.
     * Returns the plain-text OTP (only ever returned here — only the hash is stored).
     */
    public function create(string $email, string $purpose = 'admin_create_user', int $ttlMinutes = 10): string {
        $otp = (string) random_int(100000, 999999);

        // Invalidate any previous unused OTPs for this email/purpose
        $this->db->prepare(
            "UPDATE `{$this->table}` SET used = 1 WHERE email = ? AND purpose = ? AND used = 0"
        )->execute([$email, $purpose]);

        $this->insert([
            'email'      => $email,
            'otp_hash'   => password_hash($otp, PASSWORD_BCRYPT, ['cost' => 10]),
            'purpose'    => $purpose,
            'expires_at' => date('Y-m-d H:i:s', strtotime("+{$ttlMinutes} minutes")),
        ]);

        return $otp;
    }

    /**
     * Verify a submitted OTP. Consumes it (marks used) on success.
     * Limits to 5 attempts per record before it is invalidated.
     */
    public function verify(string $email, string $otp, string $purpose = 'admin_create_user'): bool {
        $record = $this->rawOne(
            "SELECT * FROM `{$this->table}`
             WHERE email = ? AND purpose = ? AND used = 0 AND expires_at > NOW()
             ORDER BY id DESC LIMIT 1",
            [$email, $purpose]
        );

        if (!$record) return false;

        if ($record['attempts'] >= 5) {
            $this->update($record['id'], ['used' => 1]);
            return false;
        }

        if (!password_verify($otp, $record['otp_hash'])) {
            $this->update($record['id'], ['attempts' => $record['attempts'] + 1]);
            return false;
        }

        $this->update($record['id'], ['used' => 1]);
        return true;
    }
}
