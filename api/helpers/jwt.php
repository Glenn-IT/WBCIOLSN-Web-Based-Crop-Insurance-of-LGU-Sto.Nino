<?php
// ============================================================
// JWT Helper
// Web-Based Crop Insurance System
// Lightweight JWT without external dependencies
// ============================================================

/**
 * Generate a JWT token
 */
function jwtEncode(array $payload): string {
    $header  = base64UrlEncode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
    $payload['iat'] = time();
    $payload['exp'] = time() + JWT_EXPIRY;
    $body    = base64UrlEncode(json_encode($payload));
    $sig     = base64UrlEncode(hash_hmac('sha256', "$header.$body", JWT_SECRET, true));
    return "$header.$body.$sig";
}

/**
 * Decode and verify a JWT token
 * Returns the payload array or null if invalid/expired
 */
function jwtDecode(string $token): ?array {
    $parts = explode('.', $token);
    if (count($parts) !== 3) return null;

    [$header, $body, $sig] = $parts;

    $expectedSig = base64UrlEncode(hash_hmac('sha256', "$header.$body", JWT_SECRET, true));
    if (!hash_equals($expectedSig, $sig)) return null;

    $payload = json_decode(base64UrlDecode($body), true);
    if (!$payload) return null;

    if (isset($payload['exp']) && $payload['exp'] < time()) return null;

    return $payload;
}

/**
 * Extract JWT from Authorization header
 */
function getBearerToken(): ?string {
    // Try getallheaders() first (works on most Apache setups)
    $headers = function_exists('getallheaders') ? getallheaders() : [];
    $auth    = $headers['Authorization']
            ?? $headers['authorization']
            // Fall back to $_SERVER entries set by mod_rewrite or CGI/FastCGI
            ?? $_SERVER['HTTP_AUTHORIZATION']
            ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
            ?? '';
    if (preg_match('/Bearer\s+(.+)/i', $auth, $matches)) {
        return trim($matches[1]);
    }
    // Final fallback: cookie set by JS login handler (handles Apache header stripping)
    if (!empty($_COOKIE['lgu_token'])) {
        return trim($_COOKIE['lgu_token']);
    }
    return null;
}

// --- Base64 URL helpers ---

function base64UrlEncode(string $data): string {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64UrlDecode(string $data): string {
    return base64_decode(strtr($data, '-_', '+/'));
}
