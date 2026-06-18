<?php

namespace App\Config;

use PDO;
use PDOException;

/**
 * Provides one shared PDO connection to Supabase PostgreSQL.
 */
class Database
{
    private static ?PDO $pdo = null;

    /**
     * Return the singleton PDO connection.
     *
     * @return PDO
     */
    public static function connection(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $host = Env::get('DB_HOST');
        $port = Env::get('DB_PORT', '5432');
        $name = Env::get('DB_NAME', 'postgres');
        $user = Env::get('DB_USER');
        $pass = Env::get('DB_PASS');

        if (!$host || !$user || !$pass) {
            throw new PDOException('Database environment variables are not configured.');
        }

        $dsn = "pgsql:host={$host};port={$port};dbname={$name};sslmode=require";
        self::$pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);

        return self::$pdo;
    }
}
