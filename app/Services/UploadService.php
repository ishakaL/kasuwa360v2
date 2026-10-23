<?php

class UploadService
{
    public function save(array $file, string $folder): array
    {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return ['ok' => false, 'message' => 'No valid upload was provided.'];
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['ok' => false, 'message' => 'The uploaded file could not be processed.'];
        }

        if ($file['size'] <= 0 || $file['size'] > UPLOAD_MAX_SIZE) {
            return ['ok' => false, 'message' => 'File size exceeds the allowed limit.'];
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array($ext, $allowed, true)) {
            return ['ok' => false, 'message' => 'Only JPG, PNG, and WebP images are allowed.'];
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) {
            return ['ok' => false, 'message' => 'Uploaded content is not a valid image.'];
        }

        if (!function_exists('getimagesize')) {
            return ['ok' => false, 'message' => 'Image processing is unavailable in this environment.'];
        }

        $imageInfo = @getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            return ['ok' => false, 'message' => 'The file does not appear to be a valid image.'];
        }

        $targetDir = APP_ROOT . '/storage/uploads/' . $folder;
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $filename = uniqid('upload_', true) . '.' . $ext;
        $destination = $targetDir . '/' . $filename;
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return ['ok' => false, 'message' => 'The image could not be saved.'];
        }

        return ['ok' => true, 'path' => 'storage/uploads/' . $folder . '/' . $filename];
    }
}
