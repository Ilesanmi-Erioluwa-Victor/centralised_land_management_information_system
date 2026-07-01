<?php

namespace App\Controllers;

use App\Helpers\Auth;
use App\Helpers\Flash;

/**
 * Shared controller utilities for rendering, redirects, CSRF, and auditing context.
 */
abstract class BaseController
{
    /**
     * Render a PHP view.
     *
     * @param string $view
     * @param array<string,mixed> $data
     * @param string $layout
     * @return void
     */
    protected function view(string $view, array $data = [], string $layout = 'main'): void
    {
        view($view, $data, $layout);
    }

    /**
     * Redirect to another route.
     *
     * @param string $path
     * @return void
     */
    protected function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }

    /**
     * Require a valid CSRF token for a POST request.
     *
     * @return void
     */
    protected function verifyPost(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !Auth::verifyCsrf($_POST['_csrf'] ?? null)) {
            Flash::set('error', 'Security token expired. Please try again.');
            $this->redirect($_SERVER['HTTP_REFERER'] ?? '/');
        }
    }

    /**
     * Current request IP address.
     *
     * @return string|null
     */
    protected function ip(): ?string
    {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? null;
        if ($ip !== null && str_contains($ip, ',')) {
            $ip = explode(',', $ip)[0];
        }
        return $ip !== null ? trim($ip) : null;
    }
}
