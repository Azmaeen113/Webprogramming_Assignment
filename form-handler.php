<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$name = sanitize_field($_POST['name'] ?? '');
$studentId = sanitize_field($_POST['student_id'] ?? '');
$department = sanitize_field($_POST['department'] ?? '');
$track = sanitize_field($_POST['track'] ?? '');
$email = sanitize_field($_POST['email'] ?? '');
$message = sanitize_field($_POST['message'] ?? '');

if ($name === '' || $studentId === '' || $department === '' || $track === '' || $email === '') {
    header('Location: index.php?error=1#join');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: index.php?error=1#join');
    exit;
}

$photoPath = handle_photo_upload($_FILES['photo'] ?? []);
if ($photoPath === null) {
    header('Location: index.php?error=photo#join');
    exit;
}

$id = generate_submission_id();
$timestamp = date('Y-m-d H:i:s');

$submission = [
    'id' => $id,
    'name' => $name,
    'student_id' => $studentId,
    'department' => $department,
    'track' => $track,
    'email' => $email,
    'message' => $message,
    'photo' => $photoPath,
    'designation' => 'Member',
    'status' => 'pending',
    'submitted_at' => $timestamp,
];

$submissions = load_submissions();
$submissions[] = $submission;
save_submissions($submissions);

$textEntry = implode(' | ', [
    $timestamp,
    $name,
    $studentId,
    $department,
    $track,
    $email,
    $message !== '' ? $message : 'N/A',
    $photoPath,
]) . PHP_EOL;

file_put_contents(__DIR__ . '/submissions.txt', $textEntry, FILE_APPEND | LOCK_EX);

header('Location: index.php?submitted=1#join');
exit;
