<div class="toolbar"><a class="btn primary" href="/land/create">Register Plot</a><a class="btn secondary" href="/land/search">Advanced Search</a></div>
<section class="card">
  <form class="filters" method="get"><label>Keyword <input name="q" value="<?= e($_GET['q'] ?? '') ?>"></label><label>Status <select name="status"><option value="">Any</option><option>available</option><option>allocated</option><option>pending</option><option>disputed</option><option>revoked</option></select></label><button class="btn secondary">Filter</button></form>
  <table><thead><tr><th>Plot No</th><th>Type</th><th>Location</th><th>State</th><th>Status</th><th></th></tr></thead><tbody>
  <?php foreach ($plots as $plot): ?><tr><td><?= e($plot['plot_number']) ?></td><td><?= e($plot['land_type']) ?></td><td><?= e($plot['location']) ?></td><td><?= e($plot['state']) ?></td><td><span class="badge <?= e($plot['status']) ?>"><?= e($plot['status']) ?></span></td><td><a href="/land/<?= e($plot['id']) ?>">View</a></td></tr><?php endforeach; ?>
  </tbody></table>
</section>
