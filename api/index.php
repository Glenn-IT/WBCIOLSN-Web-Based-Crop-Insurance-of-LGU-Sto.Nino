<?php
// ============================================================
// Main API Entry Point
// Web-Based Crop Insurance System
// Access: /api/index.php
// ============================================================

require_once __DIR__ . '/bootstrap.php';

// Load controllers (add more as phases progress)
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/UserController.php';
require_once __DIR__ . '/controllers/FarmController.php';
require_once __DIR__ . '/controllers/PlanController.php';
require_once __DIR__ . '/controllers/PolicyController.php';
require_once __DIR__ . '/controllers/ClaimController.php';
require_once __DIR__ . '/controllers/PaymentController.php';
require_once __DIR__ . '/controllers/NotificationController.php';
require_once __DIR__ . '/controllers/ReportController.php';

// Load models
require_once __DIR__ . '/models/UserModel.php';
require_once __DIR__ . '/models/FarmModel.php';
require_once __DIR__ . '/models/PlanModel.php';
require_once __DIR__ . '/models/PolicyModel.php';
require_once __DIR__ . '/models/ClaimModel.php';
require_once __DIR__ . '/models/PaymentModel.php';
require_once __DIR__ . '/models/NotificationModel.php';
require_once __DIR__ . '/models/ReportModel.php';

// Load base classes
require_once __DIR__ . '/models/BaseModel.php';
require_once __DIR__ . '/controllers/BaseController.php';

// Initialize Router
$router = new Router('/web-based-crop-insurance/api');

// ---- Health check ----
$router->get('/health', function () {
    sendSuccess([
        'status'  => 'ok',
        'version' => '1.0.0',
        'time'    => date('Y-m-d H:i:s'),
    ], 'API is running.');
});

// ---- Auth routes (Phase 3) ----
$router->post('/auth/register',        [AuthController::class, 'register']);
$router->post('/auth/login',           [AuthController::class, 'login']);
$router->post('/auth/logout',          [AuthController::class, 'logout']);
$router->post('/auth/forgot-password',   [AuthController::class, 'forgotPassword']);
$router->post('/auth/verify-otp',        [AuthController::class, 'verifyOtp']);
$router->post('/auth/reset-password',    [AuthController::class, 'resetPassword']);
$router->post('/auth/change-password',   [AuthController::class, 'changePassword']);
$router->post('/auth/security-question', [AuthController::class, 'setSecurityQuestion']);
$router->get('/auth/me',                 [AuthController::class, 'me']);

// ---- User routes (Phase 4) ----
$router->get('/users',                  [UserController::class, 'index']);
$router->post('/users',                 [UserController::class, 'store']);
$router->post('/users/send-otp',        [UserController::class, 'sendOtp']);
$router->get('/users/{id}',             [UserController::class, 'show']);
$router->put('/users/{id}',             [UserController::class, 'update']);
$router->delete('/users/{id}',          [UserController::class, 'destroy']);
$router->put('/users/{id}/status',      [UserController::class, 'updateStatus']);

// ---- Farm routes (Phase 4) ----
$router->get('/farms',                  [FarmController::class, 'index']);
$router->post('/farms',                 [FarmController::class, 'store']);
$router->get('/farms/{id}',             [FarmController::class, 'show']);
$router->put('/farms/{id}',             [FarmController::class, 'update']);
$router->delete('/farms/{id}',          [FarmController::class, 'destroy']);

// ---- Coverage Plan routes (Phase 4) ----
$router->get('/plans',                  [PlanController::class, 'index']);
$router->post('/plans',                 [PlanController::class, 'store']);
$router->get('/plans/{id}',             [PlanController::class, 'show']);
$router->put('/plans/{id}',             [PlanController::class, 'update']);
$router->delete('/plans/{id}',          [PlanController::class, 'destroy']);

// ---- Policy routes (Phase 4) ----
$router->get('/policies',               [PolicyController::class, 'index']);
$router->post('/policies',              [PolicyController::class, 'store']);
$router->get('/policies/{id}',          [PolicyController::class, 'show']);
$router->put('/policies/{id}',          [PolicyController::class, 'update']);
$router->put('/policies/{id}/approve',  [PolicyController::class, 'approve']);
$router->put('/policies/{id}/reject',   [PolicyController::class, 'reject']);
$router->put('/policies/{id}/review',   [PolicyController::class, 'review']);
$router->delete('/policies/{id}',       [PolicyController::class, 'destroy']);
$router->put('/policies/{id}/cancel',   [PolicyController::class, 'cancel']);
$router->post('/policies/{id}/documents', [PolicyController::class, 'uploadDocument']);

// ---- Claim routes (Phase 4) ----
$router->get('/claims',                 [ClaimController::class, 'index']);
$router->post('/claims',                [ClaimController::class, 'store']);
$router->get('/claims/{id}',            [ClaimController::class, 'show']);
$router->put('/claims/{id}/status',     [ClaimController::class, 'updateStatus']);
$router->post('/claims/{id}/documents', [ClaimController::class, 'uploadDocument']);

// ---- Payment routes (Phase 4) ----
$router->get('/payments',               [PaymentController::class, 'index']);
$router->get('/payments/{id}',          [PaymentController::class, 'show']);
$router->post('/payments/premium',      [PaymentController::class, 'premium']);
$router->post('/payments/payout',       [PaymentController::class, 'payout']);

// ---- Notification routes (Phase 6) ----
$router->get('/notifications',                      [NotificationController::class, 'index']);
$router->post('/notifications',                     [NotificationController::class, 'store']);
$router->get('/notifications/unread-count',         [NotificationController::class, 'unreadCount']);
$router->put('/notifications/read-all',             [NotificationController::class, 'markAllRead']);
$router->delete('/notifications/clear',             [NotificationController::class, 'clear']);
$router->put('/notifications/{id}/read',            [NotificationController::class, 'markRead']);

// ---- Dashboard & Report routes (Phase 7) ----
$router->get('/dashboard/stats',                    [ReportController::class, 'dashboardStats']);
$router->get('/reports/claims',                     [ReportController::class, 'claimsReport']);
$router->get('/reports/policies',                   [ReportController::class, 'policiesReport']);
$router->get('/reports/payments',                   [ReportController::class, 'paymentsReport']);
$router->get('/reports/trends/premium',             [ReportController::class, 'premiumTrend']);
$router->get('/reports/claims/by-crop',             [ReportController::class, 'claimsByCrop']);
$router->get('/reports/claims/by-province',         [ReportController::class, 'claimsByProvince']);
$router->get('/reports/export/{type}',              [ReportController::class, 'export']);

// Dispatch
$router->dispatch();
