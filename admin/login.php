<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';

start_secure_session();

if (current_admin()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        $error = 'Security check failed. Please try again.';
    } elseif (login_admin(trim((string) ($_POST['username'] ?? '')), (string) ($_POST['password'] ?? ''))) {
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title>Admin Login | AI Solution</title>
  <link rel="stylesheet" href="../styles.css?v=20260612-admin">
  <link rel="icon" type="image/svg+xml" href="../assets/ai-solution-logo.svg">
</head>
<body class="admin-page light-mode">
  <main class="admin-shell admin-login-shell">
    <section class="admin-login-card" aria-labelledby="admin-login-title">
      <a class="brand admin-brand" href="../index.html" aria-label="AI Solution home">
        <span class="brand-mark">
          <img class="brand-logo" src="../assets/ai-solution-logo.svg" alt="" aria-hidden="true">
        </span>
        <span>
          <strong>AI Solution</strong>
          <small>ADMIN PANEL</small>
        </span>
      </a>
      <p class="eyebrow">Secure admin access</p>
      <h1 id="admin-login-title">Sign in to manage enquiries.</h1>
      <p class="admin-muted">This area is separated from the public website and is only for authorised administrators.</p>

      <?php if ($error !== ''): ?>
        <p class="form-status is-error" role="alert"><?= h($error) ?></p>
      <?php endif; ?>

      <form class="login-form" method="post" action="login.php" novalidate>
        <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
        <label>
          <span>Username</span>
          <input type="text" name="username" autocomplete="username" required>
        </label>
        <label>
          <span>Password</span>
          <input type="password" name="password" autocomplete="current-password" required>
        </label>
        <button class="btn btn-primary" type="submit">Login</button>
      </form>
    </section>
  </main>
</body>
</html>
