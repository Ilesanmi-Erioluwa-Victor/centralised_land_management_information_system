<?php

namespace App\Helpers;

/**
 * Stores one-read flash messages in the session.
 */
class Flash
{
    /**
     * Store a flash message.
     *
     * @param string $type
     * @param string $message
     * @return void
     */
    public static function set(string $type, string $message): void
    {
        $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
    }

    /**
     * Read and clear flash messages.
     *
     * @return array<int,array{type:string,message:string}>
     */
    public static function get(): array
    {
        $messages = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $messages;
    }
}
