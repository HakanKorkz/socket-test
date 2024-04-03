<?php

namespace Socket\Client\Connect;

use Exception;

class StaticDbOperations extends DbOperations
{
    /**
     * @throws Exception
     */
    public static function insert(string $table, array $data): bool
    {
        $db = new self();
        return $db->insertData($table, $data);
    }

    /**
     * @throws Exception
     */
    public static function update(string $table, array $data, array $where): bool
    {
        $db = new self();
        return $db->updateData($table, $data, $where);
    }

    /**
     * @throws Exception
     */
    public static function get(string $table, array $columns = ['*'], array $where = [], bool $json=false, string $orderBy = '', int $limit = 0): array
    {
        $db = new self();
        return $db->getData($table, $columns, $where, $json, $orderBy, $limit);
    }

    /**
     * @throws Exception
     */
    public static function delete(string $table, array $where): bool
    {
        $db = new self();
        return $db->deleteData($table, $where);
    }
}
