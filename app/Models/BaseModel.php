<?php

namespace App\Models;

use App\Config\Database;
use PDO;

/**
 * Base model exposing the shared database connection.
 */
abstract class BaseModel
{
    protected PDO $db;

    /**
     * Create a model with the shared PDO connection.
     */
    public function __construct()
    {
        $this->db = Database::connection();
    }

    /**
     * Build a paginated limit and offset pair.
     *
     * @param int $page
     * @param int $perPage
     * @return array{limit:int,offset:int,page:int}
     */
    protected function pagination(int $page, int $perPage = 20): array
    {
        $page = max(1, $page);
        return ['limit' => $perPage, 'offset' => ($page - 1) * $perPage, 'page' => $page];
    }
}
