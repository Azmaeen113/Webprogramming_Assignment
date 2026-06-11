<?php

function load_submissions(): array
{
    $path = SUBMISSIONS_FILE;
    if (!file_exists($path)) {
        return [];
    }

    $data = json_decode(file_get_contents($path), true);
    return is_array($data) ? $data : [];
}

function save_submissions(array $submissions): bool
{
    $path = SUBMISSIONS_FILE;
    $dir = dirname($path);

    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $json = json_encode($submissions, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents($path, $json, LOCK_EX) !== false;
}

function get_submission_by_id(string $id): ?array
{
    foreach (load_submissions() as $submission) {
        if (($submission['id'] ?? '') === $id) {
            return $submission;
        }
    }

    return null;
}

function update_submission(string $id, array $changes): bool
{
    $submissions = load_submissions();
    $updated = false;

    foreach ($submissions as $index => $submission) {
        if (($submission['id'] ?? '') !== $id) {
            continue;
        }

        $submissions[$index] = array_merge($submission, $changes);
        $updated = true;
        break;
    }

    return $updated ? save_submissions($submissions) : false;
}

function get_approved_members(): array
{
    return array_values(array_filter(
        load_submissions(),
        static fn(array $submission): bool => ($submission['status'] ?? '') === 'approved'
    ));
}

function generate_submission_id(): string
{
    return bin2hex(random_bytes(8));
}

function sanitize_field(string $value): string
{
    return trim(htmlspecialchars(strip_tags($value), ENT_QUOTES, 'UTF-8'));
}

function handle_photo_upload(array $file): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        return null;
    }

    $allowed = ['image/jpeg', 'image/png', 'image/webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $allowed, true)) {
        return null;
    }

    if (($file['size'] ?? 0) > 2 * 1024 * 1024) {
        return null;
    }

    if (!is_dir(UPLOADS_DIR)) {
        mkdir(UPLOADS_DIR, 0755, true);
    }

    $extension = match ($mime) {
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        default => 'jpg',
    };

    $filename = generate_submission_id() . '.' . $extension;
    $destination = UPLOADS_DIR . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return null;
    }

    return 'uploads/applications/' . $filename;
}
