<?php
// ============================================================
// User Controller
// GET    /api/users              (admin)
// GET    /api/users/{id}
// PUT    /api/users/{id}
// DELETE /api/users/{id}         (admin)
// PUT    /api/users/{id}/status  (admin)
// ============================================================
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../middleware/RoleMiddleware.php';

class UserController extends BaseController {
    private UserModel $users;

    public function __construct() {
        $this->users = new UserModel();
    }

    // GET /api/users
    public function index(array $params): void {
        $auth = requireAuth();
        requireRole($auth, 'admin');

        ['page' => $page, 'perPage' => $perPage, 'offset' => $offset] = getPagination();
        $search = sanitize($_GET['search'] ?? '');

        $where  = $search ? "(first_name LIKE ? OR last_name LIKE ? OR email LIKE ?)" : '';
        $args   = $search ? ["%$search%", "%$search%", "%$search%"] : [];
        $items  = $this->users->paginate($offset, $perPage, $where, $args, 'created_at DESC');
        $total  = $this->users->count($where, $args);

        $items = array_map(fn($u) => $this->users->sanitize($u), $items);
        sendPaginated($items, $total, $page, $perPage);
    }

    // GET /api/users/{id}
    public function show(array $params): void {
        $auth = requireAuth();
        $id   = assertPositiveInt($params['id']);
        requireOwnerOrAdmin($auth, $id);

        $user = $this->users->find($id);
        if (!$user) sendNotFound('User not found.');

        sendSuccess($this->users->sanitize($user));
    }

    // PUT /api/users/{id}
    public function update(array $params): void {
        $auth = requireAuth();
        $id   = assertPositiveInt($params['id']);
        requireOwnerOrAdmin($auth, $id);

        $user = $this->users->find($id);
        if (!$user) sendNotFound('User not found.');

        $data = sanitizeAll($this->body());
        guardSqlInjection($data);
        $this->validateOrFail($data, [
            'first_name' => 'required|max:100',
            'last_name'  => 'required|max:100',
            'phone'      => 'max:20',
        ]);

        $this->users->update($id, [
            'first_name' => sanitize($data['first_name']),
            'last_name'  => sanitize($data['last_name']),
            'phone'      => sanitize($data['phone'] ?? $user['phone'] ?? ''),
        ]);

        $this->audit($auth['id'], 'update_user', 'users', "Updated user #$id");
        sendSuccess($this->users->sanitize($this->users->find($id)), 'Profile updated.');
    }

    // DELETE /api/users/{id}  — soft deactivate
    public function destroy(array $params): void {
        $auth = requireAuth();
        requireRole($auth, 'admin');
        $id   = assertPositiveInt($params['id']);

        $user = $this->users->find($id);
        if (!$user) sendNotFound('User not found.');
        if ($user['id'] === $auth['id']) sendError('You cannot deactivate your own account.', 400);

        $this->users->update($id, ['status' => 'inactive']);
        $this->audit($auth['id'], 'deactivate_user', 'users', "Deactivated user #$id");
        sendSuccess(null, 'User deactivated.');
    }

    // PUT /api/users/{id}/status  (admin)
    public function updateStatus(array $params): void {
        $auth = requireAuth();
        requireRole($auth, 'admin');
        $id   = assertPositiveInt($params['id']);

        $data = sanitizeAll($this->body());
        guardSqlInjection($data);
        $this->validateOrFail($data, ['status' => 'required|in:active,inactive,suspended']);

        $user = $this->users->find($id);
        if (!$user) sendNotFound('User not found.');

        $this->users->update($id, ['status' => $data['status']]);
        $this->audit($auth['id'], 'update_user_status', 'users',
            "Set user #$id status to {$data['status']}");
        sendSuccess(null, 'User status updated.');
    }
}
