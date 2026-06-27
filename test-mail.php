<?php
// ============================================================
// Mail Test Page — remove this file before going to production
// ============================================================

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}
require_once __DIR__ . '/api/config/env.php';
loadEnv(__DIR__ . '/.env');
require_once __DIR__ . '/api/config/app.php';
require_once __DIR__ . '/api/helpers/mailer.php';

$result  = null;
$error   = null;
$to      = '';
$type    = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $to   = trim($_POST['to']   ?? '');
    $type = trim($_POST['type'] ?? 'test');

    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        try {
            if ($type === 'reset') {
                $fakeToken = bin2hex(random_bytes(32));
                $sent = sendPasswordResetEmail($to, 'Test User', $fakeToken);
            } elseif ($type === 'welcome') {
                $sent = sendWelcomeEmail($to, 'Test User');
            } else {
                $sent = sendMail(
                    $to,
                    'Test Email — Crop Insurance System',
                    emailTemplate('Test Email', '
                        <p>This is a test email from the <strong>Crop Insurance System</strong>.</p>
                        <p>If you received this, Gmail SMTP is configured correctly.</p>
                    ')
                );
            }
            $result = $sent;
        } catch (Throwable $e) {
            $error = $e->getMessage();
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Mail Test — Crop Insurance</title>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: Arial, sans-serif; background: #f0f4f0; min-height: 100vh;
           display: flex; align-items: center; justify-content: center; padding: 24px; }
    .card { background: #fff; border-radius: 12px; padding: 40px 44px;
            max-width: 480px; width: 100%; box-shadow: 0 4px 20px rgba(0,0,0,.08); }
    h2 { color: #2e7d32; margin-bottom: 6px; }
    .subtitle { color: #888; font-size: 13px; margin-bottom: 28px; }
    label { display: block; font-size: 13px; font-weight: 600; color: #444; margin-bottom: 6px; }
    input, select { width: 100%; padding: 10px 14px; border: 1px solid #ddd;
                    border-radius: 7px; font-size: 14px; margin-bottom: 18px;
                    outline: none; transition: border-color .2s; }
    input:focus, select:focus { border-color: #2e7d32; }
    button { width: 100%; padding: 12px; background: #2e7d32; color: #fff;
             border: none; border-radius: 7px; font-size: 15px; font-weight: 600;
             cursor: pointer; transition: background .2s; }
    button:hover { background: #1b5e20; }
    .alert { padding: 14px 16px; border-radius: 8px; font-size: 14px; margin-bottom: 22px; }
    .alert-success { background: #e8f5e9; color: #2e7d32; border: 1px solid #a5d6a7; }
    .alert-error   { background: #fdecea; color: #c62828; border: 1px solid #ef9a9a; }
    .config-box { background: #f5f5f5; border-radius: 8px; padding: 16px 18px;
                  margin-top: 28px; font-size: 12.5px; color: #555; line-height: 1.7; }
    .config-box strong { color: #333; }
    .warn { font-size: 11px; color: #e65100; margin-top: 20px; text-align: center; }
  </style>
</head>
<body>
  <div class="card">
    <h2>Mail Test</h2>
    <p class="subtitle">Send a test email to verify Gmail SMTP is working.</p>

    <?php if ($result === true): ?>
    <div class="alert alert-success">
      Email sent successfully to <strong><?= htmlspecialchars($to) ?></strong>. Check your inbox.
    </div>
    <?php elseif ($result === false): ?>
    <div class="alert alert-error">
      Failed to send. Check your SMTP credentials in <code>.env</code> and the PHP error log.
    </div>
    <?php elseif ($error): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
      <label for="to">Recipient Email</label>
      <input type="email" id="to" name="to"
             value="<?= htmlspecialchars($to ?: getenv('MAIL_USERNAME')) ?>"
             placeholder="recipient@example.com" required />

      <label for="type">Email Type</label>
      <select id="type" name="type">
        <option value="test"    <?= $type === 'test'    ? 'selected' : '' ?>>Plain Test Email</option>
        <option value="reset"   <?= $type === 'reset'   ? 'selected' : '' ?>>Password Reset Email</option>
        <option value="welcome" <?= $type === 'welcome' ? 'selected' : '' ?>>Welcome Email</option>
      </select>

      <button type="submit">Send Test Email</button>
    </form>

    <div class="config-box">
      <strong>Current SMTP Config</strong><br>
      Host: <?= htmlspecialchars(getenv('MAIL_HOST') ?: 'smtp.gmail.com') ?><br>
      Port: <?= htmlspecialchars(getenv('MAIL_PORT') ?: '587') ?><br>
      From: <?= htmlspecialchars(getenv('MAIL_USERNAME') ?: '(not set)') ?><br>
      Encryption: STARTTLS
    </div>

    <p class="warn">Remove <code>test-mail.php</code> before deploying to production.</p>
  </div>
</body>
</html>
