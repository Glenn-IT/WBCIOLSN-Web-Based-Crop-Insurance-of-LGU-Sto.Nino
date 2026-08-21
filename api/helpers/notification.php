<?php
// ============================================================
// Notification Service
// Centralised helper for sending in-app notifications,
// emails, and SMS from any controller or model
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

            if ($user && !empty($user['email'])) {
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
    } catch (Throwable $e) {
        error_log("Notification error: " . $e->getMessage());
    }
}

/**
 * Helper to get user contact info for notifications
 */
function getUserNotificationInfo(int $userId): ?array {
    try {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id, first_name, last_name, email, phone FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$userId]);
        return $stmt->fetch() ?: null;
    } catch (Throwable) {
        return null;
    }
}

// ---- Preset policy notification helpers ----

/**
 * Notify farmer that their policy application has been approved
 */
function notifyPolicyApproved(int $userId, string $policyNumber, string $remarks = ''): void {
    $remarkText = $remarks ? " Remarks: {$remarks}" : '';
    notify($userId,
        'Policy Approved ✅',
        "Your policy <strong>{$policyNumber}</strong> has been approved and is now active.{$remarkText}",
        'success',
        '/views/user/my-applications.php',
        true
    );

    // Send SMS via PhilSMS
    $user = getUserNotificationInfo($userId);
    if ($user && !empty($user['phone'])) {
        $name = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
        sendPolicyDecisionSMS($user['phone'], $name, $policyNumber, 'active', $remarks);
    }
}

/**
 * Notify farmer that their policy application has been rejected
 */
function notifyPolicyRejected(int $userId, string $policyNumber, string $reason = ''): void {
    $reasonText = $reason ? " Reason: {$reason}" : '';
    notify($userId,
        'Policy Application Rejected ❌',
        "Your application for policy <strong>{$policyNumber}</strong> was rejected.{$reasonText}",
        'error',
        '/views/user/my-applications.php',
        true
    );

    // Send SMS via PhilSMS
    $user = getUserNotificationInfo($userId);
    if ($user && !empty($user['phone'])) {
        $name = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
        sendPolicyDecisionSMS($user['phone'], $name, $policyNumber, 'rejected', $reason);
    }
}

/**
 * Notify farmer that their policy application is under review
 */
function notifyPolicyUnderReview(int $userId, string $policyNumber, string $remarks = ''): void {
    $remarkText = $remarks ? " Remarks: {$remarks}" : '';
    notify($userId,
        'Policy Under Review 🔍',
        "Your policy application <strong>{$policyNumber}</strong> is currently under review by the Municipal Agriculture Office.{$remarkText}",
        'info',
        '/views/user/my-applications.php',
        true
    );

    // Send SMS via PhilSMS
    $user = getUserNotificationInfo($userId);
    if ($user && !empty($user['phone'])) {
        $name = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
        sendPolicyDecisionSMS($user['phone'], $name, $policyNumber, 'under_review', $remarks);
    }
}

/**
 * Notify farmer that their policy application is set to pending
 */
function notifyPolicyPending(int $userId, string $policyNumber, string $remarks = ''): void {
    $remarkText = $remarks ? " Remarks: {$remarks}" : '';
    notify($userId,
        'Policy Set to Pending ⏳',
        "Your policy application <strong>{$policyNumber}</strong> status is set to pending.{$remarkText}",
        'warning',
        '/views/user/my-applications.php',
        true
    );

    // Send SMS via PhilSMS
    $user = getUserNotificationInfo($userId);
    if ($user && !empty($user['phone'])) {
        $name = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
        sendPolicyDecisionSMS($user['phone'], $name, $policyNumber, 'pending', $remarks);
    }
}

// ---- Preset claim notification helpers ----

/**
 * Notify farmer on claim status decision (approved, rejected, under_review, paid, submitted)
 */
function notifyClaimStatusUpdated(
    int $userId,
    string $claimNumber,
    string $status,
    float $approvedAmount = 0,
    string $remarks = ''
): void {
    $labels = [
        'submitted'    => ['Claim Submitted 📩', 'info'],
        'under_review' => ['Claim Under Review 🔍', 'info'],
        'approved'     => ['Claim Approved ✅', 'success'],
        'rejected'     => ['Claim Rejected ❌', 'error'],
        'paid'         => ['Payout Processed 💰', 'success'],
    ];
    [$title, $type] = $labels[$status] ?? ["Claim {$status}", 'info'];

    $extraMessage = '';
    if ($status === 'approved' && $approvedAmount > 0) {
        $extraMessage .= " Approved Indemnity Amount: <strong>₱" . number_format($approvedAmount, 2) . "</strong>.";
    }
    if ($remarks) {
        $extraMessage .= " Remarks: {$remarks}";
    }

    notify($userId,
        $title,
        "Your claim <strong>{$claimNumber}</strong> status has been updated to: <strong>{$status}</strong>.{$extraMessage}",
        $type,
        '/views/user/application-status.php',
        true
    );

    // Send SMS via PhilSMS
    $user = getUserNotificationInfo($userId);
    if ($user && !empty($user['phone'])) {
        $name = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
        sendClaimDecisionSMS($user['phone'], $name, $claimNumber, $status, $approvedAmount, $remarks);
    }
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
