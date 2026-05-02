<?php
// ============================================================
// Upload Helper
// Web-Based Crop Insurance System
// ============================================================

/**
 * Handle a file upload and save to the designated path.
 *
 * @param array  $file       $_FILES['field']
 * @param string $subFolder  e.g. 'claims', 'profiles'
 * @return array ['success', 'path'|'error']
 */
function uploadFile(array $file, string $subFolder = 'general'): array {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'File upload error code: ' . $file['error']];
    }

    if ($file['size'] > UPLOAD_MAX_SIZE) {
        $maxMb = UPLOAD_MAX_SIZE / 1048576;
        return ['success' => false, 'error' => "File size exceeds the {$maxMb}MB limit."];
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_TYPES, true)) {
        return ['success' => false, 'error' => 'File type not allowed. Allowed: ' . implode(', ', ALLOWED_TYPES)];
    }

    // Verify actual MIME type
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($file['tmp_name']);
    $allowedMimes = [
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png'  => 'image/png',
        'pdf'  => 'application/pdf',
    ];
    if (isset($allowedMimes[$ext]) && $allowedMimes[$ext] !== $mime) {
        return ['success' => false, 'error' => 'File content does not match its extension.'];
    }

    $dir = rtrim(UPLOAD_PATH, '/') . '/' . $subFolder . '/';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $filename = uniqid('file_', true) . '.' . $ext;
    $dest     = $dir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        return ['success' => false, 'error' => 'Failed to save the uploaded file.'];
    }

    return [
        'success'   => true,
        'path'      => $subFolder . '/' . $filename,
        'filename'  => $filename,
        'mime'      => $mime,
        'size'      => $file['size'],
    ];
}

/**
 * Delete a file from the uploads directory
 */
function deleteUploadedFile(string $relativePath): bool {
    $full = rtrim(UPLOAD_PATH, '/') . '/' . ltrim($relativePath, '/');
    if (file_exists($full)) {
        return unlink($full);
    }
    return false;
}
