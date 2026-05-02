<?php
// ============================================================
// Notification Controller
// GET    /api/notifications            — list (paginated)
// GET    /api/notifications/unread-count
// PUT    /api/notifications/{id}/read  — mark one as read
// PUT    /api/notifications/read-all   — mark all as read
// DELETE /api/notifications/clear      — delete all read
// POST   /api/notifications            — create (admin/agent)
// ============================================================
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/NotificationModel.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../middleware/RoleMiddleware.php';

class NotificationController extends BaseController {
    private NotificationModel $notifications;

    public function __construct() {
        $this->notifications = new NotificationModel();
    }

    // GET /api/notifications
    public function index(array $params): void {
        $auth = requireAuth();
        ['page' => $page, 'perPage' => $perPage, 'offset' => $offset] = getPagination();

        $items = $this->notifications->getByUser($auth['id'], $offset, $perPage);
        $total = $this->notifications->countByUser($auth['id']);

        sendPaginated($items, $total, $page, $perPage);
    }

    // GET /api/notifications/unread-count
    public function unreadCount(array $params): void {
        $auth  = requireAuth();
        $count = $this->notifications->countUnread($auth['id']);
        sendSuccess(['unread' => $count], 'Unread count retrieved.');
    }

    // PUT /api/notifications/{id}/read
    public function markRead(array $params): void {
        $auth  = requireAuth();
        $id    = assertPositiveInt($params['id']);
        $notif = $this->notifications->find($id);
        if (!$notif) sendNotFound('Notification not found.');
        if ((int)$notif['user_id'] !== (int)$auth['id']) sendForbidden();

        $this->notifications->markRead($id, $auth['id']);
        sendSuccess(null, 'Notification marked as read.');
    }

    // PUT /api/notifications/read-all
    public function markAllRead(array $params): void {
        $auth = requireAuth();
        $this->notifications->markAllRead($auth['id']);
        sendSuccess(null, 'All notifications marked as read.');
    }

    // DELETE /api/notifications/clear
    public function clear(array $params): void {
        $auth = requireAuth();
        $this->notifications->clearRead($auth['id']);
        sendSuccess(null, 'Read notifications cleared.');
    }

    // POST /api/notifications  (admin/agent sends to a specific user)
    public function store(array $params): void {
        $auth = requireAuth();
        requireRole($auth, ['admin', 'agent']);

        $raw  = $this->body();
        guardSqlInjection($raw);
        $data = sanitizeAll($raw);
        $this->validateOrFail($data, [
            'user_id' => 'required|numeric',
            'title'   => 'required|max:255',
            'message' => 'required',
            'type'    => 'in:info,success,warning,error',
        ]);

        $id = $this->notifications->notify(
            (int)$data['user_id'],
            sanitize($data['title']),
            sanitize($data['message']),
            $data['type'] ?? 'info',
            isset($data['link']) ? sanitize($data['link']) : null
        );

        sendSuccess($this->notifications->find($id), 'Notification sent.', 201);
    }
}
