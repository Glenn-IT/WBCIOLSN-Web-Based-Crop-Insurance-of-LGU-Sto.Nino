<?php
// ============================================================
// Mailer Helper
// Web-Based Crop Insurance System
// Wraps PHP's mail() with simple HTML template support
// (Swap this out for PHPMailer when composer is available)
// ============================================================

/**
 * Send a plain/HTML email
 */
function sendMail(string $to, string $subject, string $htmlBody): bool {
    $from     = getenv('MAIL_FROM')      ?: 'noreply@cropinsurance.ph';
    $fromName = getenv('MAIL_FROM_NAME') ?: 'Crop Insurance System';

    $headers  = implode("\r\n", [
        "MIME-Version: 1.0",
        "Content-Type: text/html; charset=UTF-8",
        "From: $fromName <$from>",
        "Reply-To: $from",
        "X-Mailer: PHP/" . phpversion(),
    ]);

    return mail($to, $subject, $htmlBody, $headers);
}

/**
 * Wrap content in a simple email HTML template
 */
function emailTemplate(string $title, string $body): string {
    return <<<HTML
    <!DOCTYPE html>
    <html>
    <head><meta charset="UTF-8"><title>{$title}</title></head>
    <body style="font-family:Arial,sans-serif;background:#f4f4f4;padding:20px;">
      <div style="max-width:600px;margin:0 auto;background:#fff;border-radius:8px;padding:32px;">
        <h2 style="color:#2e7d32;">{$title}</h2>
        <div style="color:#333;line-height:1.6;">{$body}</div>
        <hr style="margin-top:32px;border:none;border-top:1px solid #eee;">
        <p style="font-size:12px;color:#999;">
          This is an automated message from the Web-Based Crop Insurance System.<br>
          Please do not reply to this email.
        </p>
      </div>
    </body>
    </html>
    HTML;
}

/**
 * Send password reset email
 */
function sendPasswordResetEmail(string $to, string $name, string $token): bool {
    $resetUrl = APP_URL . '/views/user/forgot-password.php?token=' . urlencode($token);
    $body     = emailTemplate('Password Reset Request', "
        <p>Hi <strong>{$name}</strong>,</p>
        <p>We received a request to reset your password. Click the button below to proceed:</p>
        <p style='text-align:center;margin:24px 0;'>
          <a href='{$resetUrl}'
             style='background:#2e7d32;color:#fff;padding:12px 28px;border-radius:5px;text-decoration:none;font-size:15px;'>
             Reset My Password
          </a>
        </p>
        <p>This link will expire in <strong>1 hour</strong>.</p>
        <p>If you did not request this, please ignore this email.</p>
    ");
    return sendMail($to, 'Password Reset – Crop Insurance System', $body);
}

/**
 * Send welcome/registration email
 */
function sendWelcomeEmail(string $to, string $name): bool {
    $body = emailTemplate('Welcome to Crop Insurance System', "
        <p>Hi <strong>{$name}</strong>,</p>
        <p>Your account has been successfully created. You can now log in and apply for crop insurance.</p>
        <p style='text-align:center;margin:24px 0;'>
          <a href='" . APP_URL . "/views/user/dashboard.php'
             style='background:#2e7d32;color:#fff;padding:12px 28px;border-radius:5px;text-decoration:none;font-size:15px;'>
             Go to Dashboard
          </a>
        </p>
    ");
    return sendMail($to, 'Welcome – Crop Insurance System', $body);
}
