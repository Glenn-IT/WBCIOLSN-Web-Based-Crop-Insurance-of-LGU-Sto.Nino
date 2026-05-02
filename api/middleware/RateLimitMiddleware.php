<?php
// ============================================================
// Rate Limit Middleware
// File-based rate limiting per IP address
// ============================================================

/**
 * Rate limit requests per IP address.
 *
 * @param int $maxRequests  Max allowed requests in the window
 * @param int $windowSeconds Time window in seconds
 * @param string $context   Optional label (e.g. 'auth') for separate buckets
 */
function rateLimit(int $maxRequests = 60, int $windowSeconds = 60, string $context = 'global'): void {
    $ip   = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $key  = preg_replace('/[^a-zA-Z0-9_\-.]/', '_', $ip) . '_' . $context;
    $dir  = sys_get_temp_dir() . '/crop_insurance_rl/';
    $file = $dir . $key . '.json';

    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $now  = time();
    $data = ['count' => 0, 'start' => $now, 'blocked_until' => 0];

    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true) ?? $data;
    }

    // If IP is in a block period, reject immediately
    if (isset($data['blocked_until']) && $data['blocked_until'] > $now) {
        $retryAfter = $data['blocked_until'] - $now;
        header("Retry-After: $retryAfter");
        sendError("Too many requests. Try again in {$retryAfter} seconds.", 429);
    }

    // Reset window if expired
    if (($now - ($data['start'] ?? $now)) > $windowSeconds) {
        $data = ['count' => 0, 'start' => $now, 'blocked_until' => 0];
    }

    $data['count']++;

    // On exceed — apply an escalating block (2× window)
    if ($data['count'] > $maxRequests) {
        $blockDuration        = $windowSeconds * 2;
        $data['blocked_until'] = $now + $blockDuration;
        file_put_contents($file, json_encode($data), LOCK_EX);
        header("Retry-After: $blockDuration");
        sendError("Too many requests. You are temporarily blocked for {$blockDuration} seconds.", 429);
    }

    file_put_contents($file, json_encode($data), LOCK_EX);
}
