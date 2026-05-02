<?php
// ============================================================
// Base Controller
// Web-Based Crop Insurance System
// ============================================================

abstract class BaseController {

    /**
     * Get validated JSON body or send 400
     */
    protected function body(): array {
        $data = getJsonBody();
        if ($data === null) {
            sendError('Invalid JSON body.', 400);
        }
        return $data;
    }

    /**
     * Run validation and auto-respond on failure
     */
    protected function validateOrFail(array $data, array $rules): array {
        $errors = validate($data, $rules);
        if (!empty($errors)) {
            sendValidationError($errors);
        }
        return $data;
    }

    /**
     * Get the authenticated user from JWT or send 401
     */
    protected function auth(): array {
        $token   = getBearerToken();
        if (!$token) sendUnauthorized('No token provided.');
        $payload = jwtDecode($token);
        if (!$payload) sendUnauthorized('Invalid or expired token.');
        return $payload;
    }

    /**
     * Guard a route by role(s)
     */
    protected function requireRole(array $payload, string|array $roles): void {
        $roles = (array) $roles;
        if (!in_array($payload['role'], $roles, true)) {
            sendForbidden('You do not have permission to perform this action.');
        }
    }

    /**
     * Log an audit action
     */
    protected function audit(int|null $userId, string $action, string $module, string $description = ''): void {
        try {
            $db = Database::getInstance();
            $stmt = $db->prepare(
                "INSERT INTO audit_logs (user_id, action, module, description, ip_address, user_agent)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([
                $userId,
                $action,
                $module,
                $description,
                $_SERVER['REMOTE_ADDR'] ?? null,
                $_SERVER['HTTP_USER_AGENT'] ?? null,
            ]);
        } catch (Throwable) {
            // Audit failure should not crash the request
        }
    }
}
