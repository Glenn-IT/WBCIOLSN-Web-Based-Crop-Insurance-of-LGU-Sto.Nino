<?php
// ============================================================
// Mail Test — Admin diagnostic page
// Temporary utility to verify Gmail SMTP credentials in .env.
// Safe to delete once mail delivery is confirmed working.
// ============================================================
$pageTitle = 'Mail Test — Admin';
$basePath  = '../../';
$guardRole = 'admin';
require_once '../../includes/auth-guard.php';

require_once '../../vendor/autoload.php';
require_once '../../api/helpers/mailer.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

$result = null;
$debugLog = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $to = trim($_POST['to'] ?? '');

    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
        $result = ['ok' => false, 'error' => 'Enter a valid email address.'];
    } else {
        $mail = new PHPMailer(true);
        $mail->SMTPDebug   = SMTP::DEBUG_SERVER;
        $mail->Debugoutput = function ($str) use (&$debugLog) {
            $debugLog .= htmlspecialchars($str) . "\n";
        };

        try {
            $mail->isSMTP();
            $mail->Host       = getenv('MAIL_HOST')     ?: 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = getenv('MAIL_USERNAME') ?: '';
            $mail->Password   = getenv('MAIL_PASSWORD') ?: '';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = (int)(getenv('MAIL_PORT') ?: 587);

            $fromAddr = getenv('MAIL_FROM')      ?: getenv('MAIL_USERNAME');
            $fromName = getenv('MAIL_FROM_NAME') ?: 'Crop Insurance System';
            $mail->setFrom($fromAddr, $fromName);
            $mail->addAddress($to);

            $mail->isHTML(true);
            $mail->Subject = 'SMTP Test — Crop Insurance System';
            $mail->Body    = '<p>This is a test email confirming Gmail SMTP is configured correctly.</p>'
                            . '<p>Sent at ' . date('Y-m-d H:i:s') . '</p>';

            $mail->send();
            $result = ['ok' => true];
        } catch (PHPMailerException $e) {
            $result = ['ok' => false, 'error' => $mail->ErrorInfo];
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <style>
    body { font-family: system-ui, sans-serif; max-width: 720px; margin: 40px auto; padding: 0 20px; color: #222; }
    h2 { margin-bottom: 4px; }
    .muted { color: #666; font-size: 14px; }
    .config { background: #f4f4f4; border-radius: 8px; padding: 14px 18px; margin: 18px 0; font-size: 13.5px; }
    .config div { margin-bottom: 4px; }
    form { display: flex; gap: 10px; margin: 20px 0; }
    input[type=email] { flex: 1; padding: 10px 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; }
    button { padding: 10px 20px; border: none; border-radius: 6px; background: #2e7d32; color: #fff; font-size: 14px; cursor: pointer; }
    button:hover { background: #256428; }
    .banner { padding: 14px 18px; border-radius: 8px; margin-bottom: 16px; font-size: 14px; }
    .ok { background: #d4edda; color: #155724; }
    .fail { background: #f8d7da; color: #721c24; }
    pre { background: #1e1e1e; color: #d4d4d4; padding: 16px; border-radius: 8px; overflow-x: auto; font-size: 12.5px; line-height: 1.5; }
  </style>
</head>
<body>
  <h2>📧 SMTP Mail Test</h2>
  <p class="muted">Sends a real test email through the credentials configured in <code>.env</code>. Use this to confirm Gmail SMTP is working before relying on OTP / temp-password emails.</p>

  <div class="config">
    <div><strong>Host:</strong> <?= htmlspecialchars(getenv('MAIL_HOST') ?: '(not set)') ?>:<?= htmlspecialchars(getenv('MAIL_PORT') ?: '(not set)') ?></div>
    <div><strong>Username:</strong> <?= htmlspecialchars(getenv('MAIL_USERNAME') ?: '(not set)') ?></div>
    <div><strong>App password length:</strong> <?= strlen(getenv('MAIL_PASSWORD') ?: '') ?> chars</div>
  </div>

  <?php if ($result): ?>
    <?php if ($result['ok']): ?>
      <div class="banner ok">✅ Email sent successfully. Check the recipient's inbox (and spam folder).</div>
    <?php else: ?>
      <div class="banner fail">❌ Failed to send: <?= htmlspecialchars($result['error']) ?></div>
    <?php endif; ?>
  <?php endif; ?>

  <form method="post">
    <input type="email" name="to" placeholder="you@example.com" required
      value="<?= htmlspecialchars($_POST['to'] ?? '') ?>" />
    <button type="submit">Send Test Email</button>
  </form>

  <?php if ($debugLog): ?>
    <h3 style="font-size:15px">SMTP conversation log</h3>
    <pre><?= $debugLog ?></pre>
  <?php endif; ?>
</body>
</html>
