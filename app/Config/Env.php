<?php

namespace App\Config;

/**
 * Reads environment variables supplied by Render or a local .env file.
 */
class Env
{
    private static bool $loaded = false;

    /**
     * Load a root .env file when present.
     *
     * @return void
     */
    public static function load(): void
    {
        if (self::$loaded) {
            return;
        }
        $path = dirname(__DIR__, 2) . '/.env';
        if (is_readable($path)) {
            foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                $line = trim($line);
                if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
                    continue;
                }
                [$key, $value] = array_map('trim', explode('=', $line, 2));
                $_ENV[$key] = $_ENV[$key] ?? trim($value, "\"'");
                putenv($key . '=' . $_ENV[$key]);
            }
        }
        self::$loaded = true;
    }

    /**
     * Get an environment variable with a fallback value.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        self::load();
        return $_ENV[$key] ?? getenv($key) ?: $default;
    }
}
