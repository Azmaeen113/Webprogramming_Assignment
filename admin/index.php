<?php

session_start();

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

if (isset($_GET['logout'])) {
    $_SESSION = [];
    session_destroy();
    header('Location: index.php');
    exit;
}

$loginError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: index.php');
        exit;
    }

    $loginError = 'Invalid username or password.';
}

if (!empty($_SESSION['admin_logged_in']) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['id'])) {
    $id = sanitize_field($_POST['id']);
    $action = sanitize_field($_POST['action']);

    if ($action === 'approve') {
        $designation = sanitize_field($_POST['designation'] ?? 'Member');
        if ($designation === '') {
            $designation = 'Member';
        }
        update_submission($id, [
            'status' => 'approved',
            'designation' => $designation,
            'approved_at' => date('Y-m-d H:i:s'),
        ]);
    }

    if ($action === 'reject') {
        update_submission($id, [
            'status' => 'rejected',
            'rejected_at' => date('Y-m-d H:i:s'),
        ]);
    }

    if ($action === 'delete') {
        delete_submission($id);
        header('Location: index.php?deleted=1');
        exit;
    }

    if ($action === 'update') {
        $submission = get_submission_by_id($id);
        if ($submission === null) {
            header('Location: index.php?error=notfound');
            exit;
        }

        $name = sanitize_field($_POST['name'] ?? '');
        $studentId = sanitize_field($_POST['student_id'] ?? '');
        $department = sanitize_field($_POST['department'] ?? '');
        $track = sanitize_field($_POST['track'] ?? '');
        $email = sanitize_field($_POST['email'] ?? '');
        $message = sanitize_field($_POST['message'] ?? '');
        $designation = sanitize_field($_POST['designation'] ?? 'Member');

        if ($name === '' || $studentId === '' || $department === '' || $track === '' || $email === '') {
            header('Location: index.php?edit=' . urlencode($id) . '&error=1');
            exit;
        }

        if (!filter_var(html_entity_decode($email, ENT_QUOTES, 'UTF-8'), FILTER_VALIDATE_EMAIL)) {
            header('Location: index.php?edit=' . urlencode($id) . '&error=1');
            exit;
        }

        if ($designation === '') {
            $designation = 'Member';
        }

        $changes = [
            'name' => $name,
            'student_id' => $studentId,
            'department' => $department,
            'track' => $track,
            'email' => $email,
            'message' => $message,
            'designation' => $designation,
        ];

        $newPhoto = handle_photo_upload($_FILES['photo'] ?? []);
        if ($newPhoto !== null) {
            if (!empty($submission['photo'])) {
                delete_submission_photo($submission['photo']);
            }
            $changes['photo'] = $newPhoto;
        }

        update_submission($id, $changes);
        header('Location: index.php?updated=1');
        exit;
    }

    header('Location: index.php');
    exit;
}

$submissions = load_submissions();
usort($submissions, static function (array $a, array $b): int {
    return strcmp($b['submitted_at'] ?? '', $a['submitted_at'] ?? '');
});

$pending = array_values(array_filter($submissions, static fn(array $item): bool => ($item['status'] ?? '') === 'pending'));
$approved = array_values(array_filter($submissions, static fn(array $item): bool => ($item['status'] ?? '') === 'approved'));
$rejected = array_values(array_filter($submissions, static fn(array $item): bool => ($item['status'] ?? '') === 'rejected'));

$flashDeleted = isset($_GET['deleted']);
$flashUpdated = isset($_GET['updated']);
$flashError = isset($_GET['error']);
$editId = sanitize_field($_GET['edit'] ?? '');
$editing = $editId !== '' ? get_submission_by_id($editId) : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel | <?php echo SITE_NAME; ?></title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="admin-body">
<?php if (empty($_SESSION['admin_logged_in'])): ?>
  <main class="admin-login-wrap">
    <form class="admin-login" method="post">
      <h1>Admin Login</h1>
      <p>freelanceHUB-KUET membership review panel</p>
      <?php if ($loginError !== ''): ?>
      <p class="form-status error"><?php echo htmlspecialchars($loginError); ?></p>
      <?php endif; ?>
      <div class="form-group">
        <label for="username">Username</label>
        <input id="username" name="username" type="text" required>
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input id="password" name="password" type="password" required>
      </div>
      <button type="submit" name="login" value="1" class="btn btn-primary btn-full">Sign In</button>
      <p class="admin-back"><a href="../index.php">Back to website</a></p>
    </form>
  </main>
<?php else: ?>
  <header class="admin-header">
    <div class="container admin-header-inner">
      <div>
        <h1>Membership Admin</h1>
        <p>Review applications and approve members for the public directory</p>
      </div>
      <div class="admin-header-actions">
        <a class="btn btn-secondary" href="../index.php#members">View Members Section</a>
        <a class="btn btn-primary" href="index.php?logout=1">Log Out</a>
      </div>
    </div>
  </header>

  <main class="admin-main container">
    <?php if ($flashDeleted): ?>
    <p class="form-status success admin-flash">Application deleted successfully.</p>
    <?php endif; ?>
    <?php if ($flashUpdated): ?>
    <p class="form-status success admin-flash">Member details updated successfully.</p>
    <?php endif; ?>
    <?php if ($flashError): ?>
    <p class="form-status error admin-flash">Could not complete that action. Please try again.</p>
    <?php endif; ?>

    <?php if ($editing !== null): ?>
    <section class="admin-section admin-edit-section">
      <div class="admin-edit-header">
        <h2>Edit Member Details</h2>
        <a class="btn btn-secondary btn-sm" href="index.php">Cancel</a>
      </div>
      <form class="admin-edit-form" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($editing['id']); ?>">
        <div class="admin-edit-grid">
          <div class="admin-edit-photo">
            <img src="../<?php echo htmlspecialchars($editing['photo']); ?>" alt="<?php echo htmlspecialchars($editing['name']); ?>">
            <div class="form-group">
              <label for="edit-photo">Replace photo (optional)</label>
              <input id="edit-photo" name="photo" type="file" accept="image/jpeg,image/png,image/webp">
            </div>
          </div>
          <div class="admin-edit-fields">
            <div class="form-group">
              <label for="edit-name">Full name</label>
              <input id="edit-name" name="name" type="text" value="<?php echo htmlspecialchars($editing['name']); ?>" required>
            </div>
            <div class="form-group">
              <label for="edit-student-id">Student ID</label>
              <input id="edit-student-id" name="student_id" type="text" value="<?php echo htmlspecialchars($editing['student_id']); ?>" required>
            </div>
            <div class="form-group">
              <label for="edit-department">Department</label>
              <input id="edit-department" name="department" type="text" value="<?php echo htmlspecialchars($editing['department']); ?>" required>
            </div>
            <div class="form-group">
              <label for="edit-track">Track</label>
              <input id="edit-track" name="track" type="text" value="<?php echo htmlspecialchars($editing['track']); ?>" required>
            </div>
            <div class="form-group">
              <label for="edit-email">Email</label>
              <input id="edit-email" name="email" type="email" value="<?php echo htmlspecialchars($editing['email']); ?>" required>
            </div>
            <div class="form-group">
              <label for="edit-designation">Designation</label>
              <input id="edit-designation" name="designation" type="text" value="<?php echo htmlspecialchars($editing['designation'] ?? 'Member'); ?>" required>
            </div>
            <div class="form-group">
              <label for="edit-message">Message</label>
              <textarea id="edit-message" name="message" rows="3"><?php echo htmlspecialchars($editing['message'] ?? ''); ?></textarea>
            </div>
          </div>
        </div>
        <div class="admin-edit-actions">
          <button type="submit" name="action" value="update" class="btn btn-primary">Save Changes</button>
          <a class="btn btn-secondary" href="index.php">Cancel</a>
        </div>
      </form>
    </section>
    <?php elseif ($editId !== ''): ?>
    <p class="form-status error admin-flash">That application could not be found.</p>
    <?php endif; ?>

    <div class="admin-stats">
      <div class="admin-stat-card">
        <span class="admin-stat-num"><?php echo count($pending); ?></span>
        <span class="admin-stat-label">Pending</span>
      </div>
      <div class="admin-stat-card">
        <span class="admin-stat-num"><?php echo count($approved); ?></span>
        <span class="admin-stat-label">Approved</span>
      </div>
      <div class="admin-stat-card">
        <span class="admin-stat-num"><?php echo count($rejected); ?></span>
        <span class="admin-stat-label">Rejected</span>
      </div>
    </div>

    <section class="admin-section">
      <h2>Pending Applications</h2>
      <?php if (count($pending) === 0): ?>
      <p class="admin-empty">No pending applications right now.</p>
      <?php else: ?>
      <div class="admin-cards">
        <?php foreach ($pending as $submission): ?>
        <article class="admin-card">
          <div class="admin-card-top">
            <img src="../<?php echo htmlspecialchars($submission['photo']); ?>" alt="<?php echo htmlspecialchars($submission['name']); ?>" class="admin-photo">
            <div>
              <h3><?php echo htmlspecialchars($submission['name']); ?></h3>
              <p class="admin-meta"><?php echo htmlspecialchars($submission['student_id']); ?> · <?php echo htmlspecialchars($submission['department']); ?></p>
              <p class="admin-meta"><?php echo htmlspecialchars($submission['email']); ?></p>
              <p class="admin-meta">Track: <?php echo htmlspecialchars($submission['track']); ?></p>
              <p class="admin-meta">Submitted: <?php echo htmlspecialchars($submission['submitted_at']); ?></p>
              <?php if (!empty($submission['message'])): ?>
              <p class="admin-message"><?php echo htmlspecialchars($submission['message']); ?></p>
              <?php endif; ?>
            </div>
          </div>
          <form class="admin-actions" method="post">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($submission['id']); ?>">
            <div class="form-group">
              <label for="designation-<?php echo htmlspecialchars($submission['id']); ?>">Designation on approval</label>
              <input id="designation-<?php echo htmlspecialchars($submission['id']); ?>" name="designation" type="text" value="Member" required>
            </div>
            <div class="admin-action-buttons">
              <button type="submit" name="action" value="approve" class="btn btn-primary">Approve</button>
              <button type="submit" name="action" value="reject" class="btn btn-secondary">Reject</button>
              <button type="submit" name="action" value="delete" class="btn btn-danger" onclick="return confirm('Delete this application permanently?');">Delete</button>
            </div>
          </form>
        </article>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </section>

    <section class="admin-section">
      <h2>Approved Members</h2>
      <?php if (count($approved) === 0): ?>
      <p class="admin-empty">No approved members yet.</p>
      <?php else: ?>
      <div class="admin-table-wrap">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Designation</th>
              <th>Department</th>
              <th>Track</th>
              <th>Approved</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($approved as $submission): ?>
            <tr>
              <td><?php echo htmlspecialchars($submission['name']); ?></td>
              <td><?php echo htmlspecialchars($submission['designation'] ?? 'Member'); ?></td>
              <td><?php echo htmlspecialchars($submission['department']); ?></td>
              <td><?php echo htmlspecialchars($submission['track']); ?></td>
              <td><?php echo htmlspecialchars($submission['approved_at'] ?? ''); ?></td>
              <td class="admin-row-actions">
                <a class="btn btn-secondary btn-sm" href="index.php?edit=<?php echo urlencode($submission['id']); ?>">Edit</a>
                <form method="post" class="admin-inline-form" onsubmit="return confirm('Delete this member permanently?');">
                  <input type="hidden" name="id" value="<?php echo htmlspecialchars($submission['id']); ?>">
                  <button type="submit" name="action" value="delete" class="btn btn-danger btn-sm">Delete</button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </section>

    <?php if (count($rejected) > 0): ?>
    <section class="admin-section">
      <h2>Rejected Applications</h2>
      <div class="admin-table-wrap">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Rejected</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($rejected as $submission): ?>
            <tr>
              <td><?php echo htmlspecialchars($submission['name']); ?></td>
              <td><?php echo htmlspecialchars($submission['email']); ?></td>
              <td><?php echo htmlspecialchars($submission['rejected_at'] ?? ''); ?></td>
              <td class="admin-row-actions">
                <a class="btn btn-secondary btn-sm" href="index.php?edit=<?php echo urlencode($submission['id']); ?>">Edit</a>
                <form method="post" class="admin-inline-form" onsubmit="return confirm('Delete this application permanently?');">
                  <input type="hidden" name="id" value="<?php echo htmlspecialchars($submission['id']); ?>">
                  <button type="submit" name="action" value="delete" class="btn btn-danger btn-sm">Delete</button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>
    <?php endif; ?>
  </main>
<?php endif; ?>
</body>
</html>
