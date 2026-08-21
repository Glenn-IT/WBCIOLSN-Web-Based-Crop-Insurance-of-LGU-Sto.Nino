<?php
// ============================================================
// SMS Helper — PhilSMS API Integration
// Web-Based Crop Insurance System
// ============================================================

/**
 * Format a Philippine mobile phone number to standard 12-digit format (639XXXXXXXXX).
 * Accepts:
 *   - "09171234567"   -> "639171234567"
 *   - "+639171234567" -> "639171234567"
 *   - "639171234567"  -> "639171234567"
 *   - "9171234567"    -> "639171234567"
 *
 * @param string|null $phone
 * @return string|null Normalized 12-digit number or null if invalid
 */
function formatPhPhoneNumber(?string $phone): ?string {
    if (empty($phone)) {
        return null;
    }

    // Strip non-digit characters
    $clean = preg_replace('/\D+/', '', $phone);

    if (empty($clean)) {
        return null;
    }

    // 09XXXXXXXXX (11 digits) -> 639XXXXXXXXX
    if (strlen($clean) === 11 && str_starts_with($clean, '09')) {
        return '63' . substr($clean, 1);
    }

    // 9XXXXXXXXX (10 digits) -> 639XXXXXXXXX
    if (strlen($clean) === 10 && str_starts_with($clean, '9')) {
        return '63' . $clean;
    }

    // 639XXXXXXXXX (12 digits)
    if (strlen($clean) === 12 && str_starts_with($clean, '639')) {
        return $clean;
    }

    return $clean;
}

/**
 * Log SMS transmission attempt to the database (sms_logs table).
 */
function logSmsToDb(
    string $recipient,
    string $message,
    string $status,
    ?int $httpCode = null,
    mixed $response = null,
    ?string $error = null
): void {
    try {
        if (!class_exists('Database')) {
            return;
        }
        $db = Database::getInstance();

        // Auto-create sms_logs table if it doesn't exist yet
        static $tableChecked = false;
        if (!$tableChecked) {
            $db->exec("CREATE TABLE IF NOT EXISTS sms_logs (
                id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                recipient     VARCHAR(20) NOT NULL,
                message       TEXT NOT NULL,
                status        ENUM('sent', 'failed', 'simulated') NOT NULL DEFAULT 'sent',
                http_code     INT UNSIGNED DEFAULT NULL,
                response_body TEXT DEFAULT NULL,
                error_message VARCHAR(255) DEFAULT NULL,
                created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_sms_recipient (recipient),
                INDEX idx_sms_status (status),
                INDEX idx_sms_created_at (created_at)
            ) ENGINE=InnoDB;");
            $tableChecked = true;
        }

        $responseStr = null;
        if (!empty($response)) {
            $responseStr = is_string($response) ? $response : json_encode($response, JSON_UNESCAPED_SLASHES);
        }

        $stmt = $db->prepare(
            "INSERT INTO sms_logs (recipient, message, status, http_code, response_body, error_message, created_at)
             VALUES (?, ?, ?, ?, ?, ?, NOW())"
        );
        $stmt->execute([
            $recipient,
            $message,
            $status,
            $httpCode,
            $responseStr,
            $error,
        ]);
    } catch (Throwable $e) {
        error_log("[PhilSMS DB Log Error] " . $e->getMessage());
    }
}

/**
 * Send an SMS message using the PhilSMS Gateway API.
 *
 * @param string $recipient Phone number (PH mobile format)
 * @param string $message   Message text to send
 * @return array ['success' => bool, 'message' => string, 'response' => mixed]
 */
function sendPhilSMS(string $recipient, string $message): array {
    $enabled = getenv('SMS_ENABLED') !== false
        ? filter_var(getenv('SMS_ENABLED'), FILTER_VALIDATE_BOOLEAN)
        : true;

    if (!$enabled) {
        $result = [
            'success' => false,
            'message' => 'SMS service is disabled via configuration.',
        ];
        logSmsToDb($recipient, $message, 'failed', null, null, $result['message']);
        return $result;
    }

    $apiKey = getenv('PHILSMS_API_KEY')
        ?: getenv('PHILSMS_API_TOKEN')
        ?: getenv('PHILSMS_TOKEN')
        ?: '';

    $phone = formatPhPhoneNumber($recipient);
    if (empty($phone)) {
        error_log("[PhilSMS] Invalid phone number provided: '{$recipient}'");
        $result = [
            'success' => false,
            'message' => 'Invalid recipient phone number.',
        ];
        logSmsToDb($recipient, $message, 'failed', null, null, $result['message']);
        return $result;
    }

    $apiUrl   = getenv('PHILSMS_API_URL')   ?: 'https://dashboard.philsms.com/api/v3/sms/send';
    $senderId = getenv('PHILSMS_SENDER_ID') ?: 'PhilSMS';

    // Cap sender_id to 11 characters max for alphanumeric sender IDs as required by PhilSMS
    if (strlen($senderId) > 11 && !is_numeric($senderId)) {
        $senderId = substr($senderId, 0, 11);
    }

    // If API key is not yet configured, log safely without failing the caller
    if (empty($apiKey) || $apiKey === 'your_philsms_api_token_here') {
        error_log("[PhilSMS (Unconfigured Token)] Would send SMS to {$phone}: \"{$message}\"");
        $result = [
            'success'   => false,
            'simulated' => true,
            'message'   => 'PhilSMS API token is not yet configured in .env. Message logged to server.',
        ];
        logSmsToDb($phone, $message, 'simulated', null, null, $result['message']);
        return $result;
    }

    $payload = [
        'recipient' => $phone,
        'sender_id' => $senderId,
        'type'      => 'plain',
        'message'   => $message,
    ];

    if (!function_exists('curl_init')) {
        error_log('[PhilSMS] cURL extension is not enabled in PHP.');
        $result = [
            'success' => false,
            'message' => 'PHP cURL extension is not available.',
        ];
        logSmsToDb($phone, $message, 'failed', null, null, $result['message']);
        return $result;
    }

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $apiUrl,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json',
            'Accept: application/json',
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);

    $rawResponse = curl_exec($ch);
    $curlError   = curl_error($ch);
    $httpCode    = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($curlError) {
        error_log("[PhilSMS] cURL Error: {$curlError}");
        $result = [
            'success' => false,
            'message' => 'Network error connecting to PhilSMS API.',
            'error'   => $curlError,
        ];
        logSmsToDb($phone, $message, 'failed', $httpCode, null, $curlError);
        return $result;
    }

    $data = json_decode($rawResponse, true);

    $isSuccess = ($httpCode >= 200 && $httpCode < 300) &&
                 (isset($data['status']) && strtolower((string)$data['status']) === 'success' || !isset($data['status']));

    if (!$isSuccess) {
        error_log("[PhilSMS] API returned error (HTTP {$httpCode}): {$rawResponse}");
        $errMsg = $data['message'] ?? 'Failed to deliver SMS via PhilSMS.';
        $result = [
            'success'   => false,
            'http_code' => $httpCode,
            'message'   => $errMsg,
            'response'  => $data,
        ];
        logSmsToDb($phone, $message, 'failed', $httpCode, $data ?: $rawResponse, $errMsg);
        return $result;
    }

    $result = [
        'success'   => true,
        'http_code' => $httpCode,
        'message'   => 'SMS sent successfully.',
        'response'  => $data,
    ];
    logSmsToDb($phone, $message, 'sent', $httpCode, $data ?: $rawResponse, null);
    return $result;
}

/**
 * Generic alias for sendPhilSMS
 */
function sendSMS(string $recipient, string $message): array {
    return sendPhilSMS($recipient, $message);
}

/**
 * Send SMS notification for Policy Application Decision (Approved, Rejected, Under Review, Pending)
 *
 * @param string $phone
 * @param string $farmerName
 * @param string $policyNumber
 * @param string $status (active|approved, rejected, under_review, pending)
 * @param string $remarks Optional admin notes or rejection reason
 * @return array
 */
function sendPolicyDecisionSMS(
    string $phone,
    string $farmerName,
    string $policyNumber,
    string $status,
    string $remarks = ''
): array {
    $name = trim($farmerName) ?: 'Farmer';
    $statusLower = strtolower($status);

    switch ($statusLower) {
        case 'active':
        case 'approved':
            $msg = "Dear {$name}, your Crop Insurance application ({$policyNumber}) has been APPROVED. Your policy is now active. Thank you! - Sto. Nino Crop Insurance";
            break;

        case 'rejected':
            $reasonText = $remarks ? " Reason: {$remarks}." : "";
            $msg = "Dear {$name}, your Crop Insurance application ({$policyNumber}) was REJECTED.{$reasonText} Please visit the Municipal Agriculture Office for inquiries. - Sto. Nino Crop Insurance";
            break;

        case 'under_review':
            $noteText = $remarks ? " Note: {$remarks}." : "";
            $msg = "Dear {$name}, your Crop Insurance application ({$policyNumber}) is now UNDER REVIEW by the Municipal Agriculture Office.{$noteText} We will update you once processed. - Sto. Nino Crop Insurance";
            break;

        case 'pending':
        default:
            $noteText = $remarks ? " Note: {$remarks}." : "";
            $msg = "Dear {$name}, your Crop Insurance application ({$policyNumber}) status has been set to PENDING.{$noteText} Please monitor your account for updates. - Sto. Nino Crop Insurance";
            break;
    }

    return sendPhilSMS($phone, $msg);
}

/**
 * Send SMS notification for Claim Verification Decision (Approved, Rejected, Under Review, Paid)
 *
 * @param string $phone
 * @param string $farmerName
 * @param string $claimNumber
 * @param string $status (approved, rejected, under_review, paid)
 * @param float  $approvedAmount Approved indemnity amount (if applicable)
 * @param string $remarks Optional admin notes or rejection reason
 * @return array
 */
function sendClaimDecisionSMS(
    string $phone,
    string $farmerName,
    string $claimNumber,
    string $status,
    float $approvedAmount = 0,
    string $remarks = ''
): array {
    $name = trim($farmerName) ?: 'Farmer';
    $statusLower = strtolower($status);

    switch ($statusLower) {
        case 'approved':
            $amountText = $approvedAmount > 0 ? " for indemnity of ₱" . number_format($approvedAmount, 2) : "";
            $noteText = $remarks ? " Note: {$remarks}." : "";
            $msg = "Dear {$name}, your Insurance Claim ({$claimNumber}) has been APPROVED{$amountText}.{$noteText} Please check your account. - Sto. Nino Crop Insurance";
            break;

        case 'rejected':
            $reasonText = $remarks ? " Reason: {$remarks}." : "";
            $msg = "Dear {$name}, your Insurance Claim ({$claimNumber}) was REJECTED.{$reasonText} Please contact the Municipal Agriculture Office for details. - Sto. Nino Crop Insurance";
            break;

        case 'under_review':
            $noteText = $remarks ? " Note: {$remarks}." : "";
            $msg = "Dear {$name}, your Insurance Claim ({$claimNumber}) is now UNDER REVIEW by the Municipal Agriculture Office.{$noteText} - Sto. Nino Crop Insurance";
            break;

        case 'paid':
            $amountText = $approvedAmount > 0 ? " of ₱" . number_format($approvedAmount, 2) : "";
            $msg = "Dear {$name}, your Claim ({$claimNumber}) indemnity payout{$amountText} has been PROCESSED. Reference: " . ($remarks ?: 'Released') . " - Sto. Nino Crop Insurance";
            break;

        default:
            $noteText = $remarks ? " Note: {$remarks}." : "";
            $msg = "Dear {$name}, your Insurance Claim ({$claimNumber}) status has been updated to {$status}.{$noteText} - Sto. Nino Crop Insurance";
            break;
    }

    return sendPhilSMS($phone, $msg);
}
