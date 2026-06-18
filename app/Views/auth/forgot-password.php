<section class="auth-card">
  <h1><?= ($mode ?? 'request') === 'reset' ? 'Reset password' : 'Forgot password' ?></h1>
  <form method="post" action="<?= ($mode ?? 'request') === 'reset' ? '/auth/reset-password' : '/auth/forgot-password' ?>">
    <?= csrf_field() ?>
    <label>Email <input type="email" name="email" required></label>
    <?php if (($mode ?? 'request') === 'reset'): ?>
      <label>OTP <input name="otp" inputmode="numeric" maxlength="6" required></label>
      <label>New password <input type="password" name="password" minlength="8" required></label>
    <?php endif; ?>
    <button class="btn primary" type="submit"><?= ($mode ?? 'request') === 'reset' ? 'Update password' : 'Send OTP' ?></button>
    <a href="/auth/login">Back to login</a>
  </form>
</section>
