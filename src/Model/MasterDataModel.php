<?php

declare(strict_types=1);

namespace App\Model;

use Exception;

final class MasterDataModel
{
    protected $database;

    protected function db()
    {
        $pdo = new \Pecee\Pixie\QueryBuilder\QueryBuilderHandler($this->database);
        return $pdo;
    }

    public function __construct(\Pecee\Pixie\Connection $database)
    {
        $this->database       = $database;
    }

    public function listCategory() {
        $result = $this->db()->table('users_pelanggan_category')->get();

        return $result;
    }
}
