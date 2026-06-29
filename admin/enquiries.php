<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';

require_admin();

$allowedStatuses = ['New', 'In Review', 'Contacted', 'Closed'];
$notice = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        $error = 'Security check failed. Please try again.';
    } else {
        $id = (int) ($_POST['id'] ?? 0);
        $action = (string) ($_POST['action'] ?? '');

        if ($id > 0 && $action === 'update_status') {
            $status = (string) ($_POST['status'] ?? 'New');
            if (in_array($status, $allowedStatuses, true)) {
                $statement = db()->prepare('UPDATE enquiries SET status = ? WHERE id = ?');
                $statement->execute([$status, $id]);
                $notice = 'Enquiry status updated.';
            }
        } elseif ($id > 0 && $action === 'delete') {
            $statement = db()->prepare('DELETE FROM enquiries WHERE id = ?');
            $statement->execute([$id]);
            $notice = 'Enquiry deleted.';
        }
    }
}

$statusFilter = (string) ($_GET['status'] ?? '');
$search = trim((string) ($_GET['search'] ?? ''));
$params = [];
$where = [];

if (in_array($statusFilter, $allowedStatuses, true)) {
    $where[] = 'status = ?';
    $params[] = $statusFilter;
}

if ($search !== '') {
    $where[] = '(name LIKE ? OR email LIKE ? OR company LIKE ? OR service LIKE ?)';
    $keyword = '%' . $search . '%';
    array_push($params, $keyword, $keyword, $keyword, $keyword);
}

$sql = 'SELECT * FROM enquiries';
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY created_at DESC';

$statement = db()->prepare($sql);
$statement->execute($params);
$enquiries = $statement->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title>Enquiry Management | AI Solution</title>
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
        <small>ENQUIRY MANAGEMENT</small>
      </span>
    </a>
    <nav class="admin-nav" aria-label="Admin navigation">
      <a class="nav-button" href="dashboard.php">Dashboard</a>
      <a class="nav-button is-active" href="enquiries.php">Enquiries</a>
      <form method="post" action="logout.php">
        <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
        <button class="nav-button" type="submit">Logout</button>
      </form>
    </nav>
  </header>

  <main class="admin-shell">
    <section class="dashboard-topbar">
      <div>
        <p class="eyebrow">Admin system</p>
        <h1>Enquiry Management</h1>
        <p class="admin-muted">View, filter, update and delete enquiries stored in MySQL.</p>
      </div>
    </section>

    <?php if ($notice !== ''): ?>
      <p class="form-status"><?= h($notice) ?></p>
    <?php endif; ?>
    <?php if ($error !== ''): ?>
      <p class="form-status is-error"><?= h($error) ?></p>
    <?php endif; ?>

    <section class="table-section">
      <form class="table-header admin-filter" method="get" action="enquiries.php">
        <label class="table-search">
          <span>Search</span>
          <input type="search" name="search" value="<?= h($search) ?>" placeholder="Name, email, company or service">
        </label>
        <label>
          <span>Status</span>
          <select name="status">
            <option value="">All statuses</option>
            <?php foreach ($allowedStatuses as $status): ?>
              <option value="<?= h($status) ?>" <?= $statusFilter === $status ? 'selected' : '' ?>><?= h($status) ?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <button class="btn btn-secondary" type="submit">Filter</button>
      </form>

      <div class="table-wrap">
        <table class="admin-table enquiry-table">
          <thead>
            <tr>
              <th>Customer</th>
              <th>Project Details</th>
              <th>Message</th>
              <th>Status</th>
              <th>Manage</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($enquiries as $row): ?>
              <tr>
                <td>
                  <strong><?= h($row['name']) ?></strong>
                  <span><?= h($row['email']) ?></span>
                  <span><?= h($row['phone'] ?: 'No phone') ?></span>
                </td>
                <td>
                  <span><?= h($row['company'] ?: 'No company') ?></span>
                  <span><?= h($row['country'] ?: 'No country') ?></span>
                  <span><?= h($row['service'] ?: 'No service') ?></span>
                  <span><?= h($row['timeline'] ?: 'No timeline') ?></span>
                  <small><?= h(date('d M Y, h:i A', strtotime($row['created_at']))) ?></small>
                </td>
                <td><?= nl2br(h($row['message'])) ?></td>
                <td><span class="status-pill"><?= h($row['status']) ?></span></td>
                <td>
                  <form class="inline-admin-form" method="post" action="enquiries.php">
                    <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                    <input type="hidden" name="id" value="<?= h((string) $row['id']) ?>">
                    <input type="hidden" name="action" value="update_status">
                    <select name="status">
                      <?php foreach ($allowedStatuses as $status): ?>
                        <option value="<?= h($status) ?>" <?= $row['status'] === $status ? 'selected' : '' ?>><?= h($status) ?></option>
                      <?php endforeach; ?>
                    </select>
                    <button class="btn btn-secondary" type="submit">Update</button>
                  </form>
                  <form class="inline-admin-form" method="post" action="enquiries.php">
                    <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                    <input type="hidden" name="id" value="<?= h((string) $row['id']) ?>">
                    <input type="hidden" name="action" value="delete">
                    <button class="btn btn-danger" type="submit">Delete</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (!$enquiries): ?>
              <tr><td colspan="5">No enquiries match this filter.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>
  </main>
</body>
</html>
