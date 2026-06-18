<section class="auth-card">
  <h1>Sign in</h1>
  <form method="post" action="/auth/login">
    <?= csrf_field() ?>
    <label>Email <input type="email" name="email" required autocomplete="email"></label>
    <label>Password <input type="password" name="password" required autocomplete="current-password"></label>
    <button class="btn primary" type="submit">Login</button>
    <a href="/auth/forgot-password">Forgot password?</a>
  </form>
</section>
