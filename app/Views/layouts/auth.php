<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($title ?? 'CLMIS') ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="auth-body">
  <main class="auth-shell">
    <div class="brand-block">
      <div class="brand-mark">CL</div>
      <div>
        <strong>CLMIS</strong>
        <span>Centralised Land Management Information System</span>
      </div>
    </div>
    <?php foreach (\App\Helpers\Flash::get() as $flash): ?>
      <div class="flash flash-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
    <?php endforeach; ?>
    <?= $content ?>
  </main>
  <script src="/assets/js/app.js"></script>
</body>
</html>
