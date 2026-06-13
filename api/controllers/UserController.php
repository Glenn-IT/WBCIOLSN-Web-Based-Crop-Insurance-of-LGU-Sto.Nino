<?php
// ============================================================
// User Controller
// GET    /api/users              (admin)
// POST   /api/users              (admin)
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
        $role   = sanitize($_GET['role'] ?? '');
        $status = sanitize($_GET['status'] ?? '');

        $conditions = ["role != 'admin'"];
        $args = [];

        if ($search) {
            $conditions[] = "(first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR phone LIKE ?)";
            $args = array_merge($args, ["%$search%", "%$search%", "%$search%", "%$search%"]);
        }
        if ($role) {
            $conditions[] = "role = ?";
            $args[] = $role;
        }
        if ($status) {
            $conditions[] = "status = ?";
            $args[] = $status;
        }

        $where = implode(' AND ', $conditions);
        $items = $this->users->paginate($offset, $perPage, $where, $args, 'created_at DESC');
        $total = $this->users->count($where, $args);

        $items = array_map(fn($u) => $this->users->sanitize($u), $items);
        sendPaginated($items, $total, $page, $perPage);
    }

    // POST /api/users  (admin creates a user)
    public function store(array $params): void {
        $auth = requireAuth();
        requireRole($auth, 'admin');

        $raw  = $this->body();
        guardSqlInjection($raw);
        $data = sanitizeAll($raw);

        $this->validateOrFail($data, [
            'first_name' => 'required|max:100',
            'last_name'  => 'required|max:100',
            'email'      => 'required|email|max:191',
            'password'   => 'required|min:6',
            'role'       => 'required|in:farmer,agent',
        ]);

        if ($this->users->emailExists($data['email'])) {
            sendError('Email address is already in use.', 409);
        }

        $userId = $this->users->createUser([
            'first_name' => sanitize($data['first_name']),
            'last_name'  => sanitize($data['last_name']),
            'email'      => strtolower(trim($data['email'])),
            'password'   => $data['password'],
            'phone'      => sanitize($data['phone'] ?? ''),
            'role'       => $data['role'],
            'status'     => $data['status'] ?? 'active',
        ]);

        $this->audit($auth['id'], 'create_user', 'users', "Admin created user #$userId");
        sendSuccess($this->users->sanitize($this->users->find($userId)), 'User created successfully.', 201);
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

        $raw  = $this->body();
        guardSqlInjection($raw);
        $data = sanitizeAll($raw);
        $this->validateOrFail($data, [
            'first_name' => 'required|max:100',
            'last_name'  => 'required|max:100',
            'phone'      => 'max:20',
        ]);

        $updateData = [
            'first_name'  => sanitize($data['first_name']),
            'last_name'   => sanitize($data['last_name']),
            'phone'       => sanitize($data['phone']        ?? $user['phone']        ?? ''),
            'address'     => sanitize($data['address']      ?? $user['address']      ?? ''),
            'farmer_type' => sanitize($data['farmer_type']  ?? $user['farmer_type']  ?? ''),
        ];

        // Admin can also update role and email
        if ($auth['role'] === 'admin') {
            if (!empty($data['role']) && in_array($data['role'], ['farmer', 'agent'])) {
                $updateData['role'] = $data['role'];
            }
            if (!empty($data['email'])) {
                $newEmail = strtolower(trim($data['email']));
                if ($newEmail !== $user['email'] && $this->users->emailExists($newEmail)) {
                    sendError('Email address is already in use.', 409);
                }
                $updateData['email'] = $newEmail;
            }
            if (!empty($data['password'])) {
                $updateData['password'] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
            }
        }

        $this->users->update($id, $updateData);

        $this->audit($auth['id'], 'update_user', 'users', "Updated user #$id");
        sendSuccess($this->users->sanitize($this->users->find($id)), 'User updated successfully.');
    }

    // DELETE /api/users/{id}  — soft deactivate
    public function destroy(array $params): void {
        $auth = requireAuth();
        requireRole($auth, 'admin');
        $id   = assertPositiveInt($params['id']);

        $user = $this->users->find($id);
        if (!$user) sendNotFound('User not found.');
        if ($user['id'] === $auth['id']) sendError('You cannot deactivate your own account.', 400);
        if ($user['role'] === 'admin') sendError('Admin accounts cannot be deleted.', 403);

        $this->users->update($id, ['status' => 'inactive']);
        $this->audit($auth['id'], 'deactivate_user', 'users', "Deactivated user #$id");
        sendSuccess(null, 'User deactivated.');
    }

    // PUT /api/users/{id}/status  (admin)
    public function updateStatus(array $params): void {
        $auth = requireAuth();
        requireRole($auth, 'admin');
        $id   = assertPositiveInt($params['id']);

        $raw  = $this->body();
        guardSqlInjection($raw);
        $data = sanitizeAll($raw);
        $this->validateOrFail($data, ['status' => 'required|in:active,inactive,suspended']);

        $user = $this->users->find($id);
        if (!$user) sendNotFound('User not found.');
        if ($user['role'] === 'admin') sendError('Cannot change status of admin accounts.', 403);

        $this->users->update($id, ['status' => $data['status']]);
        $this->audit($auth['id'], 'update_user_status', 'users',
            "Set user #$id status to {$data['status']}");
        sendSuccess(null, 'User status updated.');
    }
}
