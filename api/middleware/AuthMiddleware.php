<?php
// ============================================================
// Auth Middleware
// Verifies JWT token and attaches payload to request
// ============================================================

function requireAuth(): array {
    $token = getBearerToken();

    if (!$token) {
        sendUnauthorized('Access denied. No token provided.');
    }

    $payload = jwtDecode($token);

    if (!$payload) {
        sendUnauthorized('Access denied. Invalid or expired token.');
    }

    return $payload;
}
