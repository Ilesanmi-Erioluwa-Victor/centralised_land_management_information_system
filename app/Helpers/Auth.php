<?php

namespace App\Helpers;

use App\Models\User;

/**
 * Session authentication and role helpers.
 */
class Auth
{
    /**
     * Check whether a user is logged in.
     *
     * @return bool
     */
    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * Return the current user row when available.
     *
     * @return array<string,mixed>|null
     */
    public static function currentUser(): ?array
    {
        return self::isLoggedIn() ? (new User())->findById($_SESSION['user_id']) : null;
    }

    /**
     * Return the current role.
     *
     * @return string|null
     */
    public static function currentRole(): ?string
    {
        return $_SESSION['role'] ?? null;
    }

    /**
     * Redirect guests to login.
     *
     * @return void
     */
    public static function requireLogin(): void
    {
        if (!self::isLoggedIn()) {
            Flash::set('warning', 'Please log in to continue.');
            header('Location: /auth/login');
            exit;
        }
    }

    /**
     * Require one of the supplied roles.
     *
     * @param array<int,string> $roles
     * @return void
     */
    public static function requireRole(array $roles): void
    {
        self::requireLogin();
        if (!in_array(self::currentRole(), $roles, true)) {
            http_response_code(403);
            view('403', ['title' => 'Forbidden'], 'auth');
            exit;
        }
    }

    /**
     * Return or create a CSRF token.
     *
     * @return string
     */
    public static function csrfToken(): string
    {
        $_SESSION['csrf_token'] = $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32));
        return $_SESSION['csrf_token'];
    }

    /**
     * Verify a submitted CSRF token.
     *
     * @param string|null $token
     * @return bool
     */
    public static function verifyCsrf(?string $token): bool
    {
        return is_string($token) && hash_equals($_SESSION['csrf_token'] ?? '', $token);
    }
}
