<?php
// ============================================================
// Role Middleware
// Enforces role-based access control
// ============================================================

/**
 * Assert the authenticated user has one of the required roles.
 * Call requireAuth() first to get the $payload.
 *
 * @param array        $payload   JWT payload from requireAuth()
 * @param string|array $roles     e.g. 'admin' or ['admin','agent']
 */
function requireRole(array $payload, string|array $roles): void {
    $roles = (array) $roles;
    if (!in_array($payload['role'] ?? '', $roles, true)) {
        sendForbidden('You do not have permission to access this resource.');
    }
}

/**
 * Assert the authenticated user owns the resource OR is admin/agent
 */
function requireOwnerOrAdmin(array $payload, int $resourceUserId): void {
    if (
        (int)$payload['id'] !== $resourceUserId &&
        !in_array($payload['role'], ['admin', 'agent'], true)
    ) {
        sendForbidden('You do not have permission to access this resource.');
    }
}
