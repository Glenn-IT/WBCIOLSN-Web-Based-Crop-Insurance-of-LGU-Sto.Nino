<?php
// ============================================================
// Security Helper
// Web-Based Crop Insurance System
// Centralised security utilities used across the app
// ============================================================

// ----------------------------------------------------------
// 1. Deep-sanitize an entire input array recursively
// ----------------------------------------------------------

/**
 * Recursively sanitize all string values in an array.
 * Safe to call on $_POST, getJsonBody(), etc.
 */
function sanitizeAll(array $data): array {
    foreach ($data as $key => $value) {
        if (is_array($value)) {
            $data[$key] = sanitizeAll($value);
        } elseif (is_string($value)) {
            $data[$key] = htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
        }
    }
    return $data;
}

// ----------------------------------------------------------
// 2. Security headers — call once per response
// ----------------------------------------------------------

function sendSecurityHeaders(): void {
    // Prevent clickjacking
    header('X-Frame-Options: DENY');
    // Prevent MIME-type sniffing
    header('X-Content-Type-Options: nosniff');
    // XSS protection (legacy browsers)
    header('X-XSS-Protection: 1; mode=block');
    // Restrict referrer information
    header('Referrer-Policy: strict-origin-when-cross-origin');
    // Disable caching for API responses
    header('Cache-Control: no-store, must-revalidate, no-cache');
    header('Pragma: no-cache');
    // NOTE: Content-Security-Policy is intentionally omitted here —
    // it only applies to HTML documents, not JSON API responses.
    // Setting it on API responses can interfere with browser fetch() behaviour.

    if (APP_ENV === 'production') {
        // Force HTTPS for 1 year (production only)
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
    }
}

// ----------------------------------------------------------
// 3. Input length guard — stops absurdly large payloads
// ----------------------------------------------------------

/**
 * Abort if the raw request body exceeds $maxBytes.
 * Call early in bootstrap or before getJsonBody().
 */
function enforceMaxPayload(int $maxBytes = 1048576): void { // default 1 MB
    // Skip for multipart file uploads — size is governed by php.ini limits
    $ct = $_SERVER['CONTENT_TYPE'] ?? '';
    if (str_contains($ct, 'multipart/form-data')) return;
    $contentLength = (int)($_SERVER['CONTENT_LENGTH'] ?? 0);
    if ($contentLength > $maxBytes) {
        sendError('Request payload too large.', 413);
    }
}

// ----------------------------------------------------------
// 4. Validate that a value is a safe integer ID (no SQLi)
// ----------------------------------------------------------

function assertPositiveInt(mixed $value, string $field = 'id'): int {
    if (!ctype_digit((string)$value) || (int)$value <= 0) {
        sendError("Invalid $field parameter.", 400);
    }
    return (int)$value;
}

// ----------------------------------------------------------
// 5. Detect suspiciously malicious strings in input
//    (second layer — PDO prepared statements are the first)
// ----------------------------------------------------------

function containsSqlInjection(string $value): bool {
    $patterns = [
        '/(\bUNION\b.*\bSELECT\b)/i',
        '/(\bDROP\b.*\bTABLE\b)/i',
        '/(\bINSERT\b.*\bINTO\b)/i',
        '/(\bDELETE\b.*\bFROM\b)/i',
        '/(\bEXEC\b|\bEXECUTE\b)/i',
        '/(--|\/\*|\*\/|\bxp_)/i',
        '/(\bOR\b\s+[\d\'"]\s*=\s*[\d\'"])/i',
        '/;\s*(DROP|DELETE|INSERT|UPDATE|CREATE|ALTER|TRUNCATE)\b/i',
    ];
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $value)) return true;
    }
    return false;
}

/**
 * Scan all string values in an array for SQL injection patterns.
 * Rejects the request immediately if any match is found.
 */
function guardSqlInjection(array $data): void {
    array_walk_recursive($data, function ($value) {
        if (is_string($value) && containsSqlInjection($value)) {
            sendError('Potentially malicious input detected.', 400);
        }
    });
}

// ----------------------------------------------------------
// 6. HTTPS redirect — enforce in production
// ----------------------------------------------------------

function enforceHttps(): void {
    if (APP_ENV !== 'production') return;

    $isHttps = (
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
        (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
        ($_SERVER['SERVER_PORT'] ?? 80) == 443
    );

    if (!$isHttps) {
        $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        header("Location: $redirect", true, 301);
        exit;
    }
}

// ----------------------------------------------------------
// 7. Password strength validator
// ----------------------------------------------------------

/**
 * Returns an error message if the password is weak, or null if OK.
 * Rules: min 8 chars, at least 1 uppercase, 1 lowercase, 1 digit, 1 special char.
 */
function validatePasswordStrength(string $password): ?string {
    if (strlen($password) < 8) {
        return 'Password must be at least 8 characters long.';
    }
    if (!preg_match('/[A-Z]/', $password)) {
        return 'Password must contain at least one uppercase letter.';
    }
    if (!preg_match('/[a-z]/', $password)) {
        return 'Password must contain at least one lowercase letter.';
    }
    if (!preg_match('/[0-9]/', $password)) {
        return 'Password must contain at least one number.';
    }
    if (!preg_match('/[\W_]/', $password)) {
        return 'Password must contain at least one special character (e.g. @, #, !).';
    }
    return null;
}

// ----------------------------------------------------------
// 8. Token entropy check — reject weak JWT secrets at boot
// ----------------------------------------------------------

function assertSecureJwtSecret(): void {
    if (
        APP_ENV === 'production' &&
        (strlen(JWT_SECRET) < 32 || JWT_SECRET === 'change_this_to_a_long_random_secret_key')
    ) {
        http_response_code(500);
        die(json_encode([
            'success' => false,
            'message' => 'Insecure JWT_SECRET. Set a strong secret (min 32 chars) in .env before deploying.',
        ]));
    }
}
