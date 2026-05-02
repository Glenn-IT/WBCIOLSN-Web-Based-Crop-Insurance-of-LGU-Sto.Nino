<?php
// ============================================================
// Notification Model
// Web-Based Crop Insurance System
// ============================================================
require_once __DIR__ . '/BaseModel.php';

class NotificationModel extends BaseModel {
    protected string $table = 'notifications';

    /**
     * Get paginated notifications for a user
     */
    public function getByUser(int $userId, int $offset, int $limit): array {
        return $this->raw(
            "SELECT * FROM notifications
             WHERE user_id = ?
             ORDER BY created_at DESC
             LIMIT ? OFFSET ?",
            [$userId, $limit, $offset]
        );
    }

    public function countByUser(int $userId): int {
        return $this->count('user_id = ?', [$userId]);
    }

    public function countUnread(int $userId): int {
        return $this->count('user_id = ? AND is_read = 0', [$userId]);
    }

    /**
     * Mark a single notification as read
     */
    public function markRead(int $id, int $userId): bool {
        $stmt = $this->db->prepare(
            "UPDATE notifications SET is_read = 1
             WHERE id = ? AND user_id = ?"
        );
        return $stmt->execute([$id, $userId]);
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllRead(int $userId): bool {
        $stmt = $this->db->prepare(
            "UPDATE notifications SET is_read = 1 WHERE user_id = ?"
        );
        return $stmt->execute([$userId]);
    }

    /**
     * Create a notification record
     */
    public function notify(int $userId, string $title, string $message, string $type = 'info', ?string $link = null): int {
        return $this->insert([
            'user_id' => $userId,
            'title'   => $title,
            'message' => $message,
            'type'    => $type,
            'link'    => $link,
            'is_read' => 0,
        ]);
    }

    /**
     * Delete all read notifications for a user
     */
    public function clearRead(int $userId): bool {
        $stmt = $this->db->prepare(
            "DELETE FROM notifications WHERE user_id = ? AND is_read = 1"
        );
        return $stmt->execute([$userId]);
    }
}
