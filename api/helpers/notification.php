<?php
// ============================================================
// Notification Service
// Centralised helper for sending in-app notifications
// from any controller or model
// ============================================================

/**
 * Quick helper — creates a DB notification + optionally sends email.
 *
 * @param int    $userId
 * @param string $title
 * @param string $message
 * @param string $type     info | success | warning | error
 * @param string|null $link
 * @param bool   $sendEmail  Whether to also fire a notification email
 */
function notify(
    int $userId,
    string $title,
    string $message,
    string $type = 'info',
    ?string $link = null,
    bool $sendEmail = false
): void {
    try {
        $db   = Database::getInstance();
        $stmt = $db->prepare(
            "INSERT INTO notifications (user_id, title, message, type, link, is_read)
             VALUES (?, ?, ?, ?, ?, 0)"
        );
        $stmt->execute([$userId, $title, $message, $type, $link]);

        if ($sendEmail) {
            // Fetch user email for the notification email
            $userStmt = $db->prepare("SELECT email, first_name FROM users WHERE id = ? LIMIT 1");
            $userStmt->execute([$userId]);
            $user = $userStmt->fetch();

            if ($user) {
                $html = emailTemplate($title, "
                    <p>Hi <strong>{$user['first_name']}</strong>,</p>
                    <p>{$message}</p>"
                    . ($link ? "<p style='text-align:center;margin:24px 0;'>
                        <a href='" . APP_URL . $link . "'
                           style='background:#2e7d32;color:#fff;padding:12px 28px;
                                  border-radius:5px;text-decoration:none;font-size:15px;'>
                           View Details
                        </a></p>" : '')
                );
                @sendMail($user['email'], $title, $html);
            }
        }
    } catch (Throwable) {
        // Notification failure must never crash the main request
    }
}

// ---- Preset notification helpers ----

function notifyPolicyApproved(int $userId, string $policyNumber): void {
    notify($userId,
        'Policy Approved ✅',
        "Your policy <strong>{$policyNumber}</strong> has been approved and is now active.",
        'success',
        '/views/user/my-applications.html',
        true
    );
}

function notifyPolicyRejected(int $userId, string $policyNumber, string $reason = ''): void {
    notify($userId,
        'Policy Application Rejected',
        "Your application for policy <strong>{$policyNumber}</strong> was rejected."
            . ($reason ? " Reason: {$reason}" : ''),
        'error',
        '/views/user/my-applications.html',
        true
    );
}

function notifyClaimStatusUpdated(int $userId, string $claimNumber, string $status): void {
    $labels = [
        'under_review' => ['Under Review 🔍', 'info'],
        'approved'     => ['Claim Approved ✅', 'success'],
        'rejected'     => ['Claim Rejected', 'error'],
        'paid'         => ['Payout Processed 💰', 'success'],
    ];
    [$title, $type] = $labels[$status] ?? ["Claim {$status}", 'info'];

    notify($userId,
        $title,
        "Your claim <strong>{$claimNumber}</strong> status has been updated to: <strong>{$status}</strong>.",
        $type,
        '/views/user/application-status.html',
        true
    );
}

function notifyPaymentReceived(int $userId, string $reference, float $amount): void {
    notify($userId,
        'Payment Received 💳',
        "We received your premium payment of <strong>₱" . number_format($amount, 2) . "</strong>. Reference: {$reference}.",
        'success',
        null,
        true
    );
}

function notifyPayoutProcessed(int $userId, string $reference, float $amount): void {
    notify($userId,
        'Payout Processed 💰',
        "Your claim payout of <strong>₱" . number_format($amount, 2) . "</strong> has been processed. Reference: {$reference}.",
        'success',
        null,
        true
    );
}
