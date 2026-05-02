<?php
// ============================================================
// Bootstrap - Entry point for all API requests
// Web-Based Crop Insurance System
// ============================================================

// Load .env
require_once __DIR__ . '/config/env.php';
loadEnv(dirname(__DIR__) . '/.env');

// Load configs
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';

// Load helpers
require_once __DIR__ . '/helpers/response.php';
require_once __DIR__ . '/helpers/validation.php';
require_once __DIR__ . '/helpers/jwt.php';
require_once __DIR__ . '/helpers/upload.php';
require_once __DIR__ . '/helpers/mailer.php';
require_once __DIR__ . '/helpers/notification.php';
require_once __DIR__ . '/helpers/security.php';

// CORS headers
require_once __DIR__ . '/helpers/cors.php';

// Security hardening
enforceHttps();
sendSecurityHeaders();
enforceMaxPayload(2097152); // 2 MB max body
assertSecureJwtSecret();

// Load middleware
require_once __DIR__ . '/middleware/AuthMiddleware.php';
require_once __DIR__ . '/middleware/RoleMiddleware.php';
require_once __DIR__ . '/middleware/RateLimitMiddleware.php';

// Load router
require_once __DIR__ . '/router/Router.php';
