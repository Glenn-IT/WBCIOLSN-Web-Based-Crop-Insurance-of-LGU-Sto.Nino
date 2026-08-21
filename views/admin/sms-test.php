<?php
// ============================================================
// SMS Test — Admin diagnostic page (PhilSMS Gateway)
// Web-Based Crop Insurance System
// ============================================================
$pageTitle = 'SMS Test — PhilSMS Gateway — Admin';
$basePath  = '../../';
$guardRole = 'admin';
require_once '../../includes/auth-guard.php';
require_once '../../api/bootstrap.php';

$result   = null;
$debugLog = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone   = trim($_POST['phone'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($phone)) {
        $result = ['success' => false, 'message' => 'Enter a recipient phone number (e.g. 09171234567).'];
    } elseif (empty($message)) {
        $result = ['success' => false, 'message' => 'Enter a test message.'];
    } else {
        $res = sendPhilSMS($phone, $message);
        $result = $res;
        $debugLog = json_encode($res, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <style>
    body { font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; max-width: 720px; margin: 40px auto; padding: 0 20px; color: #222; background: #f8fafc; }
    .container { background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 28px; box-shadow: 0 4px 12px rgba(0,0,0,0.04); }
    h2 { margin-top: 0; margin-bottom: 4px; color: #1e293b; display: flex; align-items: center; gap: 8px; }
    .muted { color: #64748b; font-size: 14px; line-height: 1.5; margin-bottom: 20px; }
    .config { background: #f1f5f9; border-radius: 8px; padding: 14px 18px; margin: 18px 0; font-size: 13.5px; border-left: 4px solid #0288d1; }
    .config div { margin-bottom: 6px; }
    .config div:last-child { margin-bottom: 0; }
    .form-group { margin-bottom: 14px; }
    label { display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px; }
    input[type=text], textarea { width: 100%; box-sizing: border-box; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; font-family: inherit; }
    input:focus, textarea:focus { outline: none; border-color: #0288d1; }
    button { padding: 10px 22px; border: none; border-radius: 6px; background: #2e7d32; color: #fff; font-size: 14px; font-weight: 600; cursor: pointer; transition: background 0.15s ease; }
    button:hover { background: #256428; }
    .banner { padding: 14px 18px; border-radius: 8px; margin-bottom: 16px; font-size: 14px; font-weight: 500; }
    .ok { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .simulated { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
    .fail { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    pre { background: #1e1e1e; color: #38bdf8; padding: 16px; border-radius: 8px; overflow-x: auto; font-size: 12.5px; line-height: 1.5; font-family: monospace; }
    .nav-link { display: inline-block; margin-bottom: 16px; font-size: 13px; color: #0288d1; text-decoration: none; font-weight: 500; }
    .nav-link:hover { text-decoration: underline; }
  </style>
</head>
<body>
  <div class="container">
    <a href="dashboard.php" class="nav-link">← Back to Admin Dashboard</a>
    <h2>📱 PhilSMS Gateway Test</h2>
    <p class="muted">
      This tool sends a test SMS using the <strong>PhilSMS</strong> credentials configured in <code>.env</code>.
      Once you add your PhilSMS API token in <code>.env</code> (key: <code>PHILSMS_API_KEY</code>), you can test message delivery here.
    </p>

    <div class="config">
      <div><strong>API URL:</strong> <?= htmlspecialchars(getenv('PHILSMS_API_URL') ?: 'https://dashboard.philsms.com/api/v3/sms/send') ?></div>
      <div><strong>Sender ID:</strong> <?= htmlspecialchars(getenv('PHILSMS_SENDER_ID') ?: 'PhilSMS') ?></div>
      <div>
        <strong>API Key:</strong>
        <?php
        $token = getenv('PHILSMS_API_KEY') ?: getenv('PHILSMS_API_TOKEN') ?: getenv('PHILSMS_TOKEN') ?: '';
        if (empty($token)) {
            echo '<span style="color:#d32f2f;font-weight:600">Not configured (messages will be logged)</span>';
        } else {
            echo htmlspecialchars(substr($token, 0, 6) . '...' . substr($token, -4)) . ' (' . strlen($token) . ' chars)';
        }
        ?>
      </div>
      <div><strong>SMS Enabled:</strong> <?= (getenv('SMS_ENABLED') !== 'false') ? '✅ Yes' : '❌ Disabled' ?></div>
    </div>

    <?php if ($result): ?>
      <?php if (!empty($result['success'])): ?>
        <div class="banner ok">✅ SMS sent successfully to recipient! Check delivery on the phone.</div>
      <?php elseif (!empty($result['simulated'])): ?>
        <div class="banner simulated">⚠️ <?= htmlspecialchars($result['message']) ?></div>
      <?php else: ?>
        <div class="banner fail">❌ Failed: <?= htmlspecialchars($result['message'] ?? 'Unknown error') ?></div>
      <?php endif; ?>
    <?php endif; ?>

    <form method="post">
      <div class="form-group">
        <label for="phone">Recipient Mobile Number (PH Format)</label>
        <input type="text" id="phone" name="phone" placeholder="09171234567 or +639171234567" required
          value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" />
      </div>

      <div class="form-group">
        <label for="message">Message Body</label>
        <textarea id="message" name="message" rows="3" required placeholder="Type your SMS test message here..."><?= htmlspecialchars($_POST['message'] ?? 'Sto. Nino Crop Insurance: This is a test SMS message via PhilSMS Gateway.') ?></textarea>
      </div>

      <button type="submit">📱 Send Test SMS</button>
    </form>

    <?php if ($debugLog): ?>
      <h4 style="margin-top:24px;margin-bottom:8px;font-size:14px;color:#334155">PhilSMS Gateway Response:</h4>
      <pre><?= htmlspecialchars($debugLog) ?></pre>
    <?php endif; ?>
  </div>
</body>
</html>
