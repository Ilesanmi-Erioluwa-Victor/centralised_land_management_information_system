<section class="card form-card">
  <h2>Create system user</h2>
  <form method="post" action="/auth/register" class="grid-form">
    <?= csrf_field() ?>
    <label>Full name <input name="full_name" required></label>
    <label>Email <input type="email" name="email" required></label>
    <label>Password <input type="password" name="password" minlength="8" required></label>
    <label>Role <select name="role" required><option>viewer</option><option>officer</option><option>admin</option><option>superadmin</option></select></label>
    <button class="btn primary" type="submit">Create user</button>
  </form>
</section>
