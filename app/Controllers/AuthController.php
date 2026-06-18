<?php

namespace App\Controllers;

use App\Config\Env;
use App\Helpers\Flash;
use App\Helpers\Mailer;
use App\Helpers\Validator;
use App\Models\AuditLog;
use App\Models\User;

/**
 * Handles login, registration, password reset, and logout.
 */
class AuthController extends BaseController
{
    /**
     * Render or process login.
     *
     * @return void
     */
    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->view('auth/login', ['title' => 'Login'], 'auth');
            return;
        }
        $this->verifyPost();
        $email = strtolower(trim($_POST['email'] ?? ''));
        $key = 'login_attempt_' . sha1($email);
        $attempt = $_SESSION[$key] ?? ['count' => 0, 'locked_until' => 0];
        if (($attempt['locked_until'] ?? 0) > time()) {
            Flash::set('error', 'Too many failed attempts. Try again in 15 minutes.');
            $this->redirect('/auth/login');
        }
        if (($_SESSION['login_ip_count']['minute'] ?? 0) < time() - 60) {
            $_SESSION['login_ip_count'] = ['minute' => time(), 'count' => 0];
        }
        $_SESSION['login_ip_count']['count']++;
        if ($_SESSION['login_ip_count']['count'] > 10) {
            Flash::set('error', 'Too many login requests. Wait a minute and try again.');
            $this->redirect('/auth/login');
        }

        $user = (new User())->findByEmail($email);
        if ($user && $user['is_active'] && password_verify($_POST['password'] ?? '', $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['full_name'];
            unset($_SESSION[$key]);
            $this->redirect('/');
        }

        $attempt['count'] = ($attempt['count'] ?? 0) + 1;
        if ($attempt['count'] >= 5) {
            $attempt['locked_until'] = time() + 900;
        }
        $_SESSION[$key] = $attempt;
        Flash::set('error', 'Invalid credentials or inactive account.');
        $this->redirect('/auth/login');
    }

    /**
     * Render or process user registration.
     *
     * @return void
     */
    public function register(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->view('auth/register', ['title' => 'Create User'], 'main');
            return;
        }
        $this->verifyPost();
        $missing = Validator::validateRequired(['full_name', 'email', 'password', 'role'], $_POST);
        if ($missing || !Validator::validateEmail($_POST['email'] ?? '')) {
            Flash::set('error', 'Complete all required fields with a valid email.');
            $this->redirect('/auth/register');
        }
        $users = new User();
        if ($users->findByEmail($_POST['email'])) {
            Flash::set('error', 'A user already exists with that email.');
            $this->redirect('/auth/register');
        }
        $id = $users->create($_POST);
        (new AuditLog())->create($_SESSION['user_id'] ?? null, 'user_created', 'user', $id, null, ['email' => $_POST['email'], 'role' => $_POST['role']], $this->ip());
        $html = render_email('welcome', ['name' => $_POST['full_name'], 'email' => $_POST['email'], 'loginUrl' => Env::get('APP_URL', '') . '/auth/login']);
        Mailer::send($_POST['email'], $_POST['full_name'], 'Your CLMIS Account Has Been Created', $html);
        Flash::set('success', 'User account created.');
        $this->redirect('/admin/users');
    }

    /**
     * Render or process password reset request.
     *
     * @return void
     */
    public function forgotPassword(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->view('auth/forgot-password', ['title' => 'Forgot Password', 'mode' => 'request'], 'auth');
            return;
        }
        $this->verifyPost();
        $user = (new User())->findByEmail($_POST['email'] ?? '');
        if ($user) {
            $otp = (string) random_int(100000, 999999);
            (new User())->updateResetToken($user['id'], password_hash($otp, PASSWORD_BCRYPT, ['cost' => 12]), date('c', time() + 900));
            $html = render_email('otp', ['name' => $user['full_name'], 'otp' => $otp]);
            Mailer::send($user['email'], $user['full_name'], 'Your CLMIS Password Reset OTP', $html);
        }
        Flash::set('success', 'If the email exists, an OTP has been sent.');
        $this->redirect('/auth/reset-password');
    }

    /**
     * Render or process password reset completion.
     *
     * @return void
     */
    public function resetPassword(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->view('auth/forgot-password', ['title' => 'Reset Password', 'mode' => 'reset'], 'auth');
            return;
        }
        $this->verifyPost();
        $user = (new User())->findByEmail($_POST['email'] ?? '');
        if ($user && $user['reset_token'] && strtotime((string) $user['reset_expires']) >= time() && password_verify($_POST['otp'] ?? '', $user['reset_token'])) {
            (new User())->updatePassword($user['id'], $_POST['password'] ?? '');
            Flash::set('success', 'Password updated. Log in with the new password.');
            $this->redirect('/auth/login');
        }
        Flash::set('error', 'Invalid or expired OTP.');
        $this->redirect('/auth/reset-password');
    }

    /**
     * Destroy the current session.
     *
     * @return void
     */
    public function logout(): void
    {
        session_destroy();
        header('Location: /auth/login');
        exit;
    }
}
