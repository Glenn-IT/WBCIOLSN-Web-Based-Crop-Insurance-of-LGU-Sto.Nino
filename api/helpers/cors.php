<?php
// ============================================================
// CORS Headers Helper
// Web-Based Crop Insurance System
// ============================================================

$allowedOrigins = [
    'http://localhost',
    'http://localhost:3000',
    'http://127.0.0.1',
    APP_URL,
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

// Also accept any localhost origin (e.g. http://localhost with no path)
$isLocalhost = preg_match('#^https?://(localhost|127\.0\.0\.1)(:\d+)?$#', $origin);

if (in_array($origin, $allowedOrigins, true) || $isLocalhost) {
    header("Access-Control-Allow-Origin: $origin");
} else {
    // No cross-origin request — reflect APP_URL so same-origin requests work
    header('Access-Control-Allow-Origin: ' . APP_URL);
}

header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}
