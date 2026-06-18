<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($title ?? 'CLMIS') ?></title>
  <link rel="icon" type="image/svg+xml" href="/assets/img/favicon.svg">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/app.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="app-shell">
  <aside class="sidebar" id="sidebar">
    <a class="sidebar-brand" href="/"><span class="brand-mark">CL</span><span>CLMIS</span></a>
    <nav>
      <?php $nav = [
        '/' => 'Dashboard', '/land' => 'Land Records', '/land/create' => 'Register Plot',
        '/land/search' => 'Search', '/owners' => 'Land Owners', '/transactions' => 'Transactions',
        '/documents' => 'Documents', '/reports' => 'Reports', '/help' => 'Help'
      ]; ?>
      <?php foreach ($nav as $href => $label): ?>
        <a href="<?= e($href) ?>" class="<?= strtok($_SERVER['REQUEST_URI'], '?') === $href ? 'active' : '' ?>"><?= e($label) ?></a>
      <?php endforeach; ?>
      <?php if (in_array($_SESSION['role'] ?? '', ['superadmin', 'admin'], true)): ?>
        <div class="nav-section">Administration</div>
        <?php if (($_SESSION['role'] ?? '') === 'superadmin'): ?><a href="/admin/users">Users</a><?php endif; ?>
        <a href="/admin/audit">Audit Trail</a>
      <?php endif; ?>
    </nav>
  </aside>
  <div class="main-area">
    <header class="topbar">
      <button class="icon-button" data-sidebar-toggle aria-label="Toggle navigation"><span data-sidebar-icon>☰</span></button>
      <h1><?= e($title ?? 'Dashboard') ?></h1>
      <div class="topbar-user">
        <span class="bell">●</span>
        <span class="avatar"><?= e(strtoupper(substr($_SESSION['name'] ?? 'U', 0, 1))) ?></span>
        <span><?= e($_SESSION['name'] ?? 'User') ?></span>
        <a href="/auth/logout">Logout</a>
      </div>
    </header>
    <div class="flash-stack">
      <?php foreach (\App\Helpers\Flash::get() as $flash): ?>
        <div class="flash flash-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
      <?php endforeach; ?>
    </div>
    <main class="content"><?= $content ?></main>
  </div>
</div>
<script src="/assets/js/app.js"></script>
</body>
</html>
