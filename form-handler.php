<?php

require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

function sanitize_field(string $value): string
{
    return trim(htmlspecialchars(strip_tags($value), ENT_QUOTES, 'UTF-8'));
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

$timestamp = date('Y-m-d H:i:s');
$entry = implode(' | ', [
    $timestamp,
    $name,
    $studentId,
    $department,
    $track,
    $email,
    $message !== '' ? $message : 'N/A',
]) . PHP_EOL;

file_put_contents(__DIR__ . '/submissions.txt', $entry, FILE_APPEND | LOCK_EX);

header('Location: index.php?submitted=1#join');
exit;
