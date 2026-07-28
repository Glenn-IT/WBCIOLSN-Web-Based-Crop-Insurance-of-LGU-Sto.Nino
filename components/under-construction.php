<?php
define('CURRENT_VERSION', 'v4.00');

// Prevent the browser from caching this page (blocks bfcache restoration after logout)
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Under Construction — LGU Crop Insurance</title>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🌽</text></svg>" />
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #1a237e 0%, #283593 50%, #1565c0 100%);
      font-family: 'Segoe UI', system-ui, sans-serif;
      padding: 24px;
    }
    .card {
      background: #fff;
      border-radius: 20px;
      padding: 48px 40px;
      max-width: 480px;
      width: 100%;
      text-align: center;
      box-shadow: 0 24px 64px rgba(0,0,0,0.25);
    }
    .icon { font-size: 64px; margin-bottom: 20px; line-height: 1; }
    .version-badge {
      display: inline-block;
      background: #fff8e1;
      color: #f57f17;
      border: 1.5px solid #ffe082;
      border-radius: 20px;
      padding: 4px 16px;
      font-size: 13px;
      font-weight: 700;
      letter-spacing: 0.5px;
      margin-bottom: 20px;
    }
    h1 {
      font-size: 26px;
      font-weight: 800;
      color: #1a237e;
      margin-bottom: 12px;
    }
    p {
      color: #546e7a;
      font-size: 15px;
      line-height: 1.6;
      margin-bottom: 32px;
    }
    .btn {
      display: inline-block;
      background: linear-gradient(135deg, #1a237e, #1565c0);
      color: #fff;
      text-decoration: none;
      padding: 12px 32px;
      border-radius: 10px;
      font-size: 15px;
      font-weight: 600;
      border: none;
      cursor: pointer;
      transition: opacity 0.2s;
    }
    .btn:hover { opacity: 0.88; }
  </style>
</head>
<body>
  <div class="card">
    <div class="icon">🚧</div>
    <div class="version-badge">Current Version: <?= CURRENT_VERSION ?></div>
    <h1>Under Construction</h1>
    <p>This feature is not yet available in the current presentation version.
       It will be unlocked in a future release.</p>
    <button class="btn" onclick="logout()">← Back to Login</button>
  </div>
  <script src="/web-based-crop-insurance/assets/js/app.js"></script>
  <script>
    // Defense-in-depth: if Chrome restores this page from bfcache via the
    // Back button after logout, force it to reload so the session check
    // (and no-store header above) apply again instead of showing stale HTML.
    window.addEventListener('pageshow', function (e) {
      if (e.persisted) window.location.reload();
    });
  </script>
</body>
</html>
<?php exit; ?>
