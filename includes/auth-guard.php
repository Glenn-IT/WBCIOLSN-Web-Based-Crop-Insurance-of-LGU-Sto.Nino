<?php
// ============================================================
// auth-guard.php — Server-side page protection
// Include at the TOP of every protected view (before any output).
//
// How it works:
//   1. JS login handler calls setSession(user, token) in app.js,
//      which also writes a cookie "lgu_token" so PHP can read it.
//   2. This file reads that cookie, verifies the JWT, and redirects
//      to the login page if the token is missing or expired.
//   3. On success, $authUser is available to the page (array with
//      id, email, role, first_name, last_name, etc.).
//
// Variables to set BEFORE including:
//   $guardRole  (string) — 'user' (default) or 'admin'
//                          'admin' also accepts role 'agent'
// ============================================================

$guardRole = $guardRole ?? 'user';

// Prevent the browser from caching protected pages (blocks bfcache restoration after logout)
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');

// Resolve project root regardless of which view includes this file
$projectRoot = dirname(__DIR__);

// Load .env + JWT helper (reuse existing api helpers — no duplication)
require_once $projectRoot . '/api/config/env.php';
loadEnv($projectRoot . '/.env');

define('JWT_SECRET', $_ENV['JWT_SECRET'] ?? '');
define('JWT_EXPIRY', (int)($_ENV['JWT_EXPIRY'] ?? 86400));

require_once $projectRoot . '/api/helpers/jwt.php';

// Read token from cookie (set by JS login handler via document.cookie)
$token = $_COOKIE['lgu_token'] ?? null;

$loginUrl = ($guardRole === 'admin')
    ? '/web-based-crop-insurance/views/admin/login.php'
    : '/web-based-crop-insurance/index.php';

if (!$token) {
    header('Location: ' . $loginUrl);
    exit;
}

$authUser = jwtDecode($token);

if (!$authUser) {
    // Token invalid or expired — clear the stale cookie and redirect
    setcookie('lgu_token', '', time() - 3600, '/');
    header('Location: ' . $loginUrl);
    exit;
}

// Role enforcement
if ($guardRole === 'admin' && !in_array($authUser['role'] ?? '', ['admin', 'agent'])) {
    header('Location: ' . $loginUrl);
    exit;
}

// Enrich $authUser with full DB record so views can use first_name, last_name, phone, etc.
try {
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
        $_ENV['DB_HOST'] ?? 'localhost',
        $_ENV['DB_PORT'] ?? '3306',
        $_ENV['DB_NAME'] ?? 'crop_insurance_db'
    );
    $_authPdo = new PDO($dsn, $_ENV['DB_USER'] ?? 'root', $_ENV['DB_PASS'] ?? '', [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
    $_authStmt = $_authPdo->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
    $_authStmt->execute([$authUser['id']]);
    $_fullUser = $_authStmt->fetch();
    if ($_fullUser) {
        unset($_fullUser['password'], $_fullUser['reset_token'], $_fullUser['reset_expires']);
        $authUser = array_merge($authUser, $_fullUser);
    }
    unset($_authPdo, $_authStmt, $_fullUser);
} catch (Throwable $_e) {
    // Fallback to JWT data only if DB lookup fails
    unset($_e);
}

// If the account's status changed after the token was issued (e.g. suspended,
// or a pending registration that never should have had a session), kick it out.
if (isset($authUser['status']) && $authUser['status'] !== 'active') {
    setcookie('lgu_token', '', time() - 3600, '/');
    header('Location: ' . $loginUrl);
    exit;
}
