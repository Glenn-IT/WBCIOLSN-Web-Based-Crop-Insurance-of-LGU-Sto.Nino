<?php
// ============================================================
// Response Helper
// Standardizes all JSON API responses
// ============================================================

/**
 * Send a JSON success response
 */
function sendSuccess(mixed $data = null, string $message = 'Success', int $code = 200): never {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => $message,
        'data'    => $data,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Send a JSON error response
 */
function sendError(string $message = 'An error occurred', int $code = 400, mixed $errors = null): never {
    http_response_code($code);
    header('Content-Type: application/json');
    $body = [
        'success' => false,
        'message' => $message,
    ];
    if ($errors !== null) {
        $body['errors'] = $errors;
    }
    echo json_encode($body, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Send a 404 Not Found response
 */
function sendNotFound(string $message = 'Resource not found'): never {
    sendError($message, 404);
}

/**
 * Send a 401 Unauthorized response
 */
function sendUnauthorized(string $message = 'Unauthorized'): never {
    sendError($message, 401);
}

/**
 * Send a 403 Forbidden response
 */
function sendForbidden(string $message = 'Forbidden'): never {
    sendError($message, 403);
}

/**
 * Send a 422 Validation Error response
 */
function sendValidationError(array $errors, string $message = 'Validation failed'): never {
    sendError($message, 422, $errors);
}

/**
 * Send a 500 Server Error response
 */
function sendServerError(string $message = 'Internal server error'): never {
    sendError($message, 500);
}

/**
 * Return paginated JSON response
 */
function sendPaginated(array $items, int $total, int $page, int $perPage, string $message = 'Success'): never {
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode([
        'success'    => true,
        'message'    => $message,
        'data'       => $items,
        'pagination' => [
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'last_page'    => (int) ceil($total / $perPage),
        ],
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}
