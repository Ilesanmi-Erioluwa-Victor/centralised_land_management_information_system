<section class="kpi-grid">
  <?php foreach (['plots' => 'Total Plots', 'owners' => 'Owners', 'transactions' => 'Transactions', 'documents' => 'Documents'] as $key => $label): ?>
    <article class="card kpi"><span><?= e($label) ?></span><strong><?= e($counts[$key] ?? 0) ?></strong></article>
  <?php endforeach; ?>
</section>
<section class="two-col">
  <article class="card"><h2>Land Type Distribution</h2><canvas id="typeChart" data-labels='<?= e(json_encode(array_column($types, 'land_type'))) ?>' data-values='<?= e(json_encode(array_column($types, 'total'))) ?>'></canvas></article>
  <article class="card"><h2>Recent Activity</h2><table><thead><tr><th>Time</th><th>User</th><th>Action</th></tr></thead><tbody><?php foreach (array_slice($activity, 0, 8) as $log): ?><tr><td><?= e($log['created_at']) ?></td><td><?= e($log['full_name'] ?? 'System') ?></td><td><?= e($log['action']) ?></td></tr><?php endforeach; ?></tbody></table></article>
</section>
