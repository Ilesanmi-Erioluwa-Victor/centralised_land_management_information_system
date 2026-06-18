<?php

declare(strict_types=1);

use App\Config\Env;
use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\DocumentController;
use App\Controllers\HelpController;
use App\Controllers\LandController;
use App\Controllers\OwnerController;
use App\Controllers\ReportController;
use App\Controllers\TransactionController;
use App\Helpers\Auth;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;

$root = dirname(__DIR__);
if (is_file($root . '/vendor/autoload.php')) {
    require $root . '/vendor/autoload.php';
} else {
    spl_autoload_register(function (string $class) use ($root): void {
        $prefix = 'App\\';
        if (str_starts_with($class, $prefix)) {
            $path = $root . '/app/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
            if (is_file($path)) {
                require $path;
            }
        }
    });
}

Env::load();
$secure = Env::get('APP_ENV', 'production') === 'production';
session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'secure' => $secure, 'httponly' => true, 'samesite' => 'Strict']);
session_start();

/**
 * Escape output for HTML.
 *
 * @param mixed $value
 * @return string
 */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/**
 * Return a CSRF hidden field.
 *
 * @return string
 */
function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(Auth::csrfToken()) . '">';
}

/**
 * Render a view into a layout.
 *
 * @param string $view
 * @param array<string,mixed> $data
 * @param string $layout
 * @return void
 */
function view(string $view, array $data = [], string $layout = 'main'): void
{
    extract($data, EXTR_SKIP);
    ob_start();
    require dirname(__DIR__) . '/app/Views/' . $view . '.php';
    $content = ob_get_clean();
    require dirname(__DIR__) . '/app/Views/layouts/' . $layout . '.php';
}

/**
 * Render an email partial.
 *
 * @param string $view
 * @param array<string,mixed> $data
 * @return string
 */
function render_email(string $view, array $data = []): string
{
    extract($data, EXTR_SKIP);
    ob_start();
    require dirname(__DIR__) . '/app/Views/emails/' . $view . '.php';
    return (string) ob_get_clean();
}

/**
 * Match a route pattern against a path and return UUID/string params.
 *
 * @param string $pattern
 * @param string $path
 * @return array<int,string>|null
 */
function match_route(string $pattern, string $path): ?array
{
    $regex = preg_replace('#\{[^/]+\}#', '([^/]+)', $pattern);
    if (preg_match('#^' . $regex . '$#', $path, $matches)) {
        array_shift($matches);
        return $matches;
    }
    return null;
}

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
$method = $_SERVER['REQUEST_METHOD'];

$routes = [
    ['GET', '/', DashboardController::class, 'index', ['auth' => true]],
    ['GET|POST', '/auth/login', AuthController::class, 'login', []],
    ['GET|POST', '/auth/register', AuthController::class, 'register', ['auth' => true, 'roles' => ['superadmin']]],
    ['GET|POST', '/auth/forgot-password', AuthController::class, 'forgotPassword', []],
    ['GET|POST', '/auth/reset-password', AuthController::class, 'resetPassword', []],
    ['GET', '/auth/logout', AuthController::class, 'logout', []],
    ['GET', '/land', LandController::class, 'index', ['auth' => true]],
    ['GET', '/land/search', LandController::class, 'search', ['auth' => true]],
    ['GET', '/land/create', LandController::class, 'create', ['auth' => true, 'roles' => ['superadmin', 'admin', 'officer']]],
    ['POST', '/land/store', LandController::class, 'store', ['auth' => true, 'roles' => ['superadmin', 'admin', 'officer']]],
    ['GET', '/land/{id}', LandController::class, 'show', ['auth' => true]],
    ['GET', '/land/{id}/edit', LandController::class, 'edit', ['auth' => true, 'roles' => ['superadmin', 'admin', 'officer']]],
    ['POST', '/land/{id}/update', LandController::class, 'update', ['auth' => true, 'roles' => ['superadmin', 'admin', 'officer']]],
    ['POST', '/land/{id}/delete', LandController::class, 'delete', ['auth' => true, 'roles' => ['superadmin', 'admin']]],
    ['GET', '/owners', OwnerController::class, 'index', ['auth' => true]],
    ['GET', '/owners/create', OwnerController::class, 'create', ['auth' => true, 'roles' => ['superadmin', 'admin', 'officer']]],
    ['POST', '/owners/create', OwnerController::class, 'store', ['auth' => true, 'roles' => ['superadmin', 'admin', 'officer']]],
    ['GET', '/owners/{id}', OwnerController::class, 'show', ['auth' => true]],
    ['GET', '/owners/{id}/edit', OwnerController::class, 'edit', ['auth' => true, 'roles' => ['superadmin', 'admin', 'officer']]],
    ['POST', '/owners/{id}/edit', OwnerController::class, 'update', ['auth' => true, 'roles' => ['superadmin', 'admin', 'officer']]],
    ['GET', '/transactions', TransactionController::class, 'index', ['auth' => true]],
    ['GET', '/transactions/create', TransactionController::class, 'create', ['auth' => true, 'roles' => ['superadmin', 'admin', 'officer']]],
    ['POST', '/transactions/create', TransactionController::class, 'store', ['auth' => true, 'roles' => ['superadmin', 'admin', 'officer']]],
    ['GET', '/transactions/{id}', TransactionController::class, 'show', ['auth' => true]],
    ['POST', '/transactions/{id}/approve', TransactionController::class, 'approve', ['auth' => true, 'roles' => ['superadmin', 'admin']]],
    ['POST', '/transactions/{id}/reject', TransactionController::class, 'reject', ['auth' => true, 'roles' => ['superadmin', 'admin']]],
    ['GET', '/documents', DocumentController::class, 'index', ['auth' => true]],
    ['GET', '/documents/upload', DocumentController::class, 'upload', ['auth' => true, 'roles' => ['superadmin', 'admin', 'officer']]],
    ['POST', '/documents/upload', DocumentController::class, 'store', ['auth' => true, 'roles' => ['superadmin', 'admin', 'officer']]],
    ['GET', '/documents/{id}/download', DocumentController::class, 'download', ['auth' => true]],
    ['POST', '/documents/{id}/delete', DocumentController::class, 'delete', ['auth' => true, 'roles' => ['superadmin', 'admin']]],
    ['GET', '/reports', ReportController::class, 'index', ['auth' => true]],
    ['GET', '/reports/export/csv', ReportController::class, 'exportCsv', ['auth' => true, 'roles' => ['superadmin', 'admin', 'officer']]],
    ['GET', '/admin/users', AdminController::class, 'users', ['auth' => true, 'roles' => ['superadmin']]],
    ['POST', '/admin/users/{id}/toggle', AdminController::class, 'toggleUser', ['auth' => true, 'roles' => ['superadmin']]],
    ['POST', '/admin/users/{id}/role', AdminController::class, 'changeRole', ['auth' => true, 'roles' => ['superadmin']]],
    ['GET', '/admin/audit', AdminController::class, 'audit', ['auth' => true, 'roles' => ['superadmin', 'admin']]],
    ['GET', '/help', HelpController::class, 'index', ['auth' => true]],
    ['GET', '/api/ping', AdminController::class, 'ping', []],
];

foreach ($routes as [$verbs, $pattern, $controller, $action, $options]) {
    if (!in_array($method, explode('|', $verbs), true)) {
        continue;
    }
    $params = match_route($pattern, $path);
    if ($params === null) {
        continue;
    }
    if (($options['auth'] ?? false) === true) {
        AuthMiddleware::handle();
    }
    if (!empty($options['roles'])) {
        RoleMiddleware::handle($options['roles']);
    }
    (new $controller())->$action(...$params);
    exit;
}

http_response_code(404);
view('404', ['title' => 'Not Found'], 'auth');
