<?php

namespace App\Helpers;

/**
 * Common sanitisation and validation helpers.
 */
class Validator
{
    /**
     * Escape user-facing text.
     *
     * @param string|null $value
     * @return string
     */
    public static function sanitize(?string $value): string
    {
        return htmlspecialchars(trim((string) $value), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validate an email address.
     *
     * @param string $email
     * @return bool
     */
    public static function validateEmail(string $email): bool
    {
        return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Return missing required field names.
     *
     * @param array<int,string> $fields
     * @param array<string,mixed> $data
     * @return array<int,string>
     */
    public static function validateRequired(array $fields, array $data): array
    {
        return array_values(array_filter($fields, fn ($field) => trim((string)($data[$field] ?? '')) === ''));
    }

    /**
     * Validate UUID text.
     *
     * @param string $value
     * @return bool
     */
    public static function isUUID(string $value): bool
    {
        return (bool) preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $value);
    }
}
