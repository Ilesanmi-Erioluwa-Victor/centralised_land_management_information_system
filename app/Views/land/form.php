<section class="card form-card">
  <h2><?= isset($plot['id']) ? 'Edit plot' : 'Register new plot' ?></h2>
  <form method="post" action="<?= $action ?>" class="grid-form">
    <?= csrf_field() ?>
    <label>Plot number <input name="plot_number" value="<?= e($plot['plot_number'] ?? '') ?>" required></label>
    <label>Land type <select name="land_type"><?php foreach(['urban','agricultural','residential','commercial','industrial'] as $x): ?><option <?= ($plot['land_type'] ?? '')===$x?'selected':'' ?>><?= $x ?></option><?php endforeach; ?></select></label>
    <label>Location <input name="location" value="<?= e($plot['location'] ?? '') ?>" required></label>
    <label>State <input name="state" value="<?= e($plot['state'] ?? '') ?>" required></label>
    <label>LGA <input name="lga" value="<?= e($plot['lga'] ?? '') ?>"></label>
    <label>Area sqm <input type="number" step="0.01" min="0" name="area_sqm" value="<?= e($plot['area_sqm'] ?? '') ?>"></label>
    <label>Coordinates <input name="coordinates" value="<?= e($plot['coordinates'] ?? '') ?>"></label>
    <label>Status <select name="status"><?php foreach(['available','allocated','disputed','revoked','pending'] as $x): ?><option <?= ($plot['status'] ?? '')===$x?'selected':'' ?>><?= $x ?></option><?php endforeach; ?></select></label>
    <label class="span-2">Description <textarea name="description"><?= e($plot['description'] ?? '') ?></textarea></label>
    <button class="btn primary" type="submit">Save plot</button>
  </form>
</section>
