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
require_once __DIR__ . '/../models/OtpModel.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../middleware/RoleMiddleware.php';
require_once __DIR__ . '/../middleware/RateLimitMiddleware.php';
require_once __DIR__ . '/../helpers/mailer.php';

class UserController extends BaseController {
    private UserModel $users;
    private OtpModel $otp;

    public function __construct() {
        $this->users = new UserModel();
        $this->otp   = new OtpModel();
    }

    // POST /api/users/send-otp  (admin — verify a farmer's email before creating the account)
    public function sendOtp(array $params): void {
        $auth = requireAuth();
        requireRole($auth, 'admin');
        rateLimit(5, 60, 'user_send_otp');

        $raw  = $this->body();
        guardSqlInjection($raw);
        $data = sanitizeAll($raw);

        $this->validateOrFail($data, ['email' => 'required|email|max:191']);

        $email = strtolower(trim($data['email']));
        if ($this->users->emailExists($email)) {
            sendError('Email address is already in use.', 409);
        }

        $name = trim(sanitize($data['first_name'] ?? '')) ?: 'there';
        $code = $this->otp->create($email, 'admin_create_user');

        if (!sendOtpEmail($email, $name, $code)) {
            sendError('Failed to send verification email. Please check the address and try again.', 502);
        }

        sendSuccess(null, 'Verification code sent to ' . $email . '.');
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

    // POST /api/users  (admin creates a farmer account)
    // Requires a valid OTP (sent via sendOtp) to confirm the email is real.
    // A temporary password is auto-generated and emailed; the farmer must
    // change it on first login.
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
            'otp'        => 'required',
        ]);

        $email = strtolower(trim($data['email']));

        if ($this->users->emailExists($email)) {
            sendError('Email address is already in use.', 409);
        }

        if (!$this->otp->verify($email, trim($data['otp']), 'admin_create_user')) {
            sendError('Invalid or expired verification code. Please send a new code and try again.', 422);
        }

        $phone = sanitize($data['phone'] ?? '');
        if ($phone !== '' && !preg_match('/^09\d{9}$/', $phone)) {
            sendError('Phone number must be 11 digits in PH format (e.g. 09XXXXXXXXX).', 422);
        }

        $tempPassword = generateTempPassword();
        $firstName    = sanitize($data['first_name']);

        $userId = $this->users->createUser([
            'first_name'           => $firstName,
            'last_name'            => sanitize($data['last_name']),
            'email'                => $email,
            'password'             => $tempPassword,
            'phone'                => $phone,
            'role'                 => 'farmer',
            'status'               => $data['status'] ?? 'active',
            'email_verified'       => 1,
            'must_change_password' => 1,
        ]);

        @sendTempPasswordEmail($email, $firstName, $tempPassword);

        $this->audit($auth['id'], 'create_user', 'users', "Admin created user #$userId");
        sendSuccess([
            'user'          => $this->users->sanitize($this->users->find($userId)),
            'temp_password' => $tempPassword,
        ], 'User created successfully. A temporary password has been emailed to them.', 201);
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

        if (!empty($data['phone']) && !preg_match('/^09\d{9}$/', $data['phone'])) {
            sendError('Phone number must be 11 digits in PH format (e.g. 09XXXXXXXXX).', 422);
        }

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
