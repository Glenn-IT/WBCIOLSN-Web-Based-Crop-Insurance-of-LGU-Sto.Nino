<?php
// ============================================================
// Application Configuration
// Web-Based Crop Insurance System
// ============================================================

define('APP_NAME',     getenv('APP_NAME')     ?: 'Web-Based Crop Insurance');
define('APP_ENV',      getenv('APP_ENV')      ?: 'development');
define('APP_URL',      getenv('APP_URL')      ?: 'http://localhost/web-based-crop-insurance');
define('APP_DEBUG',    getenv('APP_DEBUG')    !== false ? (bool) getenv('APP_DEBUG') : true);
define('APP_TIMEZONE', getenv('APP_TIMEZONE') ?: 'Asia/Manila');

define('JWT_SECRET',   getenv('JWT_SECRET')   ?: 'change_this_secret');
define('JWT_EXPIRY',   (int)(getenv('JWT_EXPIRY') ?: 86400));

define('UPLOAD_MAX_SIZE', (int)(getenv('UPLOAD_MAX_SIZE') ?: 5242880));

// Resolve upload path — if .env gives a relative path, anchor it to the project root.
// Project root is two levels above api/config/ (__DIR__/../../).
$_rawUploadPath = getenv('UPLOAD_PATH') ?: 'uploads/';
if (!preg_match('/^(?:\/|[A-Za-z]:[\/\\\\])/', $_rawUploadPath)) {
    // Relative path — make it absolute from project root
    $_rawUploadPath = realpath(__DIR__ . '/../../') . DIRECTORY_SEPARATOR
                    . ltrim(str_replace('\\', '/', $_rawUploadPath), '/');
}
define('UPLOAD_PATH', rtrim(str_replace('\\', '/', $_rawUploadPath), '/') . '/');
unset($_rawUploadPath);

define('ALLOWED_TYPES',   explode(',', getenv('ALLOWED_TYPES') ?: 'jpg,jpeg,png,pdf'));

// PhilSMS Gateway Settings
define('PHILSMS_API_KEY',   getenv('PHILSMS_API_KEY')   ?: getenv('PHILSMS_API_TOKEN') ?: getenv('PHILSMS_TOKEN') ?: '');
define('PHILSMS_API_URL',   getenv('PHILSMS_API_URL')   ?: 'https://dashboard.philsms.com/api/v3/sms/send');
define('PHILSMS_SENDER_ID', getenv('PHILSMS_SENDER_ID') ?: 'PhilSMS');
define('SMS_ENABLED',       getenv('SMS_ENABLED') !== false ? filter_var(getenv('SMS_ENABLED'), FILTER_VALIDATE_BOOLEAN) : true);

// Set timezone
date_default_timezone_set(APP_TIMEZONE);

// Error reporting based on environment
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
