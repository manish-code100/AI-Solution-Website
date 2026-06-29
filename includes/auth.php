<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

function start_secure_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    ]);

    session_start();
}

function csrf_token(): string
{
    start_secure_session();

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf(?string $token): bool
{
    start_secure_session();

    return is_string($token)
        && isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

function current_admin(): ?array
{
    start_secure_session();

    if (empty($_SESSION[ADMIN_SESSION_KEY])) {
        return null;
    }

    $statement = db()->prepare('SELECT id, username, last_login_at FROM admins WHERE id = ? LIMIT 1');
    $statement->execute([(int) $_SESSION[ADMIN_SESSION_KEY]]);
    $admin = $statement->fetch();

    return $admin ?: null;
}

function require_admin(): array
{
    $admin = current_admin();

    if (!$admin) {
        header('Location: login.php');
        exit;
    }

    return $admin;
}

function login_admin(string $username, string $password): bool
{
    $statement = db()->prepare('SELECT id, username, password_hash FROM admins WHERE username = ? LIMIT 1');
    $statement->execute([$username]);
    $admin = $statement->fetch();

    if (!$admin || !password_verify($password, $admin['password_hash'])) {
        return false;
    }

    start_secure_session();
    session_regenerate_id(true);
    $_SESSION[ADMIN_SESSION_KEY] = (int) $admin['id'];

    $update = db()->prepare('UPDATE admins SET last_login_at = NOW() WHERE id = ?');
    $update->execute([(int) $admin['id']]);

    return true;
}

function logout_admin(): void
{
    start_secure_session();
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'] ?? '', (bool) $params['secure'], (bool) $params['httponly']);
    }

    session_destroy();
}
