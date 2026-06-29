<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';

function clean_input(string $key, int $maxLength = 255): string
{
    $value = trim((string) ($_POST[$key] ?? ''));
    $value = preg_replace('/\s+/', ' ', $value) ?? '';

    return mb_substr($value, 0, $maxLength);
}

function redirect_back(string $status): void
{
    $fallback = 'enquiry.html';
    $referer = (string) ($_SERVER['HTTP_REFERER'] ?? '');

    if (str_contains($referer, 'contact.html')) {
        $fallback = 'contact.html';
    }

    $separator = str_contains($fallback, '?') ? '&' : '?';
    header('Location: ' . $fallback . $separator . $status);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: enquiry.html');
    exit;
}

if (trim((string) ($_POST['website'] ?? '')) !== '') {
    redirect_back('submitted=1');
}

$name = clean_input('name', 120);
$email = clean_input('email', 160);
$phone = clean_input('phone', 40);
$company = clean_input('company', 140);
$country = clean_input('country', 100);
$jobTitle = clean_input('job_title', 120);
$service = clean_input('service', 120);
$timeline = clean_input('timeline', 80);
$message = trim((string) ($_POST['message'] ?? ''));
$message = mb_substr($message, 0, 4000);

if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($message) < 10) {
    redirect_back('error=validation');
}

if ($phone !== '' && !preg_match('/^[0-9+\-\s()]{7,20}$/', $phone)) {
    redirect_back('error=validation');
}

try {
    $statement = db()->prepare(
        'INSERT INTO enquiries (name, email, phone, company, country, job_title, service, timeline, message)
         VALUES (:name, :email, :phone, :company, :country, :job_title, :service, :timeline, :message)'
    );

    $statement->execute([
        ':name' => $name,
        ':email' => $email,
        ':phone' => $phone !== '' ? $phone : null,
        ':company' => $company !== '' ? $company : null,
        ':country' => $country !== '' ? $country : null,
        ':job_title' => $jobTitle !== '' ? $jobTitle : null,
        ':service' => $service !== '' ? $service : null,
        ':timeline' => $timeline !== '' ? $timeline : null,
        ':message' => $message,
    ]);

    redirect_back('submitted=1');
} catch (Throwable $error) {
    error_log('AI Solution enquiry insert failed: ' . $error->getMessage());
    redirect_back('error=database');
}
