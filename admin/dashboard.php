<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';

$admin = require_admin();

$counts = [
    'total' => (int) db()->query('SELECT COUNT(*) FROM enquiries')->fetchColumn(),
    'new' => (int) db()->query("SELECT COUNT(*) FROM enquiries WHERE status = 'New'")->fetchColumn(),
    'contacted' => (int) db()->query("SELECT COUNT(*) FROM enquiries WHERE status = 'Contacted'")->fetchColumn(),
];

$recent = db()->query('SELECT id, name, email, service, status, created_at FROM enquiries ORDER BY created_at DESC LIMIT 6')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title>Admin Dashboard | AI Solution</title>
  <link rel="stylesheet" href="../styles.css?v=20260612-admin">
  <link rel="icon" type="image/svg+xml" href="../assets/ai-solution-logo.svg">
</head>
<body class="admin-page light-mode">
  <header class="admin-topbar">
    <a class="brand" href="dashboard.php" aria-label="AI Solution admin dashboard">
      <span class="brand-mark">
        <img class="brand-logo" src="../assets/ai-solution-logo.svg" alt="" aria-hidden="true">
      </span>
      <span>
        <strong>AI Solution</strong>
        <small>ADMIN DASHBOARD</small>
      </span>
    </a>
    <nav class="admin-nav" aria-label="Admin navigation">
      <a class="nav-button is-active" href="dashboard.php">Dashboard</a>
      <a class="nav-button" href="enquiries.php">Enquiries</a>
      <form method="post" action="logout.php">
        <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
        <button class="nav-button" type="submit">Logout</button>
      </form>
    </nav>
  </header>

  <main class="admin-shell">
    <section class="dashboard-topbar">
      <div>
        <p class="eyebrow">Welcome <?= h($admin['username']) ?></p>
        <h1>Admin Dashboard</h1>
        <p class="admin-muted">Track customer enquiries submitted through the AI Solution website.</p>
      </div>
      <a class="btn btn-primary" href="enquiries.php">Manage Enquiries</a>
    </section>

    <section class="admin-stat-grid" aria-label="Enquiry statistics">
      <article class="stat-card">
        <strong><?= h((string) $counts['total']) ?></strong>
        <span>Total enquiries</span>
      </article>
      <article class="stat-card">
        <strong><?= h((string) $counts['new']) ?></strong>
        <span>New enquiries</span>
      </article>
      <article class="stat-card">
        <strong><?= h((string) $counts['contacted']) ?></strong>
        <span>Contacted clients</span>
      </article>
    </section>

    <section class="table-section">
      <div class="table-header">
        <div>
          <p class="eyebrow">Recent enquiries</p>
          <h2>Latest website submissions</h2>
        </div>
        <a class="link-button" href="enquiries.php">View all</a>
      </div>
      <div class="table-wrap">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Service</th>
              <th>Status</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recent as $row): ?>
              <tr>
                <td><?= h($row['name']) ?></td>
                <td><?= h($row['email']) ?></td>
                <td><?= h($row['service'] ?: 'Not selected') ?></td>
                <td><span class="status-pill"><?= h($row['status']) ?></span></td>
                <td><?= h(date('d M Y', strtotime($row['created_at']))) ?></td>
              </tr>
            <?php endforeach; ?>
            <?php if (!$recent): ?>
              <tr><td colspan="5">No enquiries have been submitted yet.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>
  </main>
</body>
</html>
