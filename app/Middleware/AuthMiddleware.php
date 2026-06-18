<?php

namespace App\Middleware;

use App\Helpers\Auth;

/**
 * Protects authenticated routes.
 */
class AuthMiddleware
{
    /**
     * Enforce login before continuing.
     *
     * @return void
     */
    public static function handle(): void
    {
        Auth::requireLogin();
    }
}
