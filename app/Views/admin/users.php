<div class="page-head">
  <div>
    <h2>User Management</h2>
    <p class="page-desc">Manage system users, roles, and account status</p>
  </div>
  <a class="btn primary" href="/auth/register">+ Create User</a>
</div>

<?php
$total = count($users);
$active = count(array_filter($users, fn($u) => $u['is_active']));
$roles = array_count_values(array_column($users, 'role'));
?>

<div class="stat-row stat-row-4">
  <div class="stat-box">
    <span class="stat-label">Total Users</span>
    <strong class="stat-value"><?= $total ?></strong>
  </div>
  <div class="stat-box">
    <span class="stat-label">Active</span>
    <strong class="stat-value stat-green"><?= $active ?></strong>
  </div>
  <div class="stat-box">
    <span class="stat-label">Admins</span>
    <strong class="stat-value"><?= ($roles['admin'] ?? 0) + ($roles['superadmin'] ?? 0) ?></strong>
  </div>
  <div class="stat-box">
    <span class="stat-label">Officers</span>
    <strong class="stat-value"><?= $roles['officer'] ?? 0 ?></strong>
  </div>
</div>

<div class="card">
  <table class="table">
    <thead>
      <tr>
        <th>User</th>
        <th>Email</th>
        <th>Role</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $user): ?>
        <tr>
          <td>
            <div class="user-cell">
              <span class="user-avatar"><?= e(strtoupper(substr($user['full_name'], 0, 1))) ?></span>
              <span><?= e($user['full_name']) ?></span>
            </div>
          </td>
          <td class="cell-muted"><?= e($user['email']) ?></td>
          <td>
            <span class="badge badge-<?= e($user['role'] === 'superadmin' ? 'danger' : ($user['role'] === 'admin' ? 'warning' : 'info')) ?>">
              <?= e(ucfirst($user['role'])) ?>
            </span>
          </td>
          <td>
            <?php if ($user['is_active']): ?>
              <span class="badge badge-success">Active</span>
            <?php else: ?>
              <span class="badge badge-muted">Inactive</span>
            <?php endif; ?>
          </td>
          <td>
            <div class="action-group">
              <form method="post" action="/admin/users/<?= e($user['id']) ?>/toggle" class="inline-form">
                <?= csrf_field() ?>
                <button class="btn btn-sm <?= $user['is_active'] ? 'btn-outline-danger' : 'btn-outline-success' ?>">
                  <?= $user['is_active'] ? 'Deactivate' : 'Activate' ?>
                </button>
              </form>
              <form method="post" action="/admin/users/<?= e($user['id']) ?>/role" class="inline-form role-form">
                <?= csrf_field() ?>
                <select name="role" class="role-select">
                  <option value="viewer" <?= $user['role'] === 'viewer' ? 'selected' : '' ?>>Viewer</option>
                  <option value="officer" <?= $user['role'] === 'officer' ? 'selected' : '' ?>>Officer</option>
                  <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                  <option value="superadmin" <?= $user['role'] === 'superadmin' ? 'selected' : '' ?>>Superadmin</option>
                </select>
                <button class="btn btn-sm btn-outline">Update</button>
              </form>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
