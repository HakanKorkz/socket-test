<?php

namespace Socket\Client\Connect;

use Exception;
use PDO;
use PDOException;

class DbOperations extends DbConnection
{

    /**
     * @param string $table
     * @param array $data
     * @param string $colum
     * @param bool $lastInsertIdRequest
     * @return array|boolean
     * @throws Exception
     */
    public function insertData(string $table, array $data, string $colum = "", bool $lastInsertIdRequest = false): bool|array
    {
        $keys = array_keys($data);
        $values = array_values($data);
        $sql = "INSERT INTO $table (" . implode(',', $keys) . ") VALUES (" . implode(',', array_fill(0, count($values), '?')) . ")";
        $pdo = $this->getPdo(); // PDO bağlantısını al
        $pdo->beginTransaction();
        try {
            $stmt = $this->connect()->prepare($sql);
            // using bindParam
            foreach ($values as $index => $value) {
                $stmt->bindParam($index + 1, $values[$index]);
            }
            $insert = $stmt->execute();
            if ($insert) {
                $pdo->commit(); // İşlem başarılıysa işlemi tamamla
            } else {
                $pdo->rollBack(); // İşlem başarısızsa geri al
            }
            if ($lastInsertIdRequest) {
                $lastInsertedData = $this->getLastInsertedData($table, $colum);
                if (!is_null($lastInsertedData)) {
                    return ["boolean" => true, "lastInsertId" => $lastInsertedData[$colum]];
                } else {
                    return ["boolean" => true, "lastInsertId" => 0];
                }
            } else {
                return true;
            }
        } catch (PDOException $e) {
            $pdo->rollBack(); // İşlem başarısızsa geri al
            throw new Exception("Error retrieving data from $table: " . $e->getMessage());
        }
    }

    /**
     * @param string $table
     * @param $colum
     * @return array|null
     */
    public function getLastInsertedData(string $table, $colum): ?array
    {
        $pdo = $this->getPdo(); // PDO bağlantısını al
        $sql = "SELECT * FROM $table ORDER BY $colum DESC LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result === false) {
            return null; // Son eklenen veri bulunamadı
        }
        return $result;
    }

    /**
     * @return PDO|null
     */
    public function getPdo(): ?PDO
    {
        return $this->connect();
    }

    /**
     * @param string $table
     * @param array $data
     * @param array $where
     * @return bool
     * @throws Exception
     */
    public function updateData(string $table, array $data, array $where): bool
    {
        $setValues = [];
        $whereValues = [];
        $params = array_merge(array_values($data), array_values($where));
        $pdo = $this->getPdo(); // PDO bağlantısını al
        $pdo->beginTransaction();
        foreach ($data as $key => $value) {
            $setValues[] = "$key=?";
        }
        foreach ($where as $key => $value) {
            $whereValues[] = "$key=?";
        }
        $sql = "UPDATE $table SET " . implode(',', $setValues) . " WHERE " . implode(' AND ', $whereValues);
        try {
            $stmt = $this->connect()->prepare($sql);
            foreach ($params as $index => $value) {
                $stmt->bindValue($index + 1, $value);
            }
            $update = $stmt->execute();
            if ($update) {
                $pdo->commit();
            } else {
                $pdo->rollBack();
            }
            return true;
        } catch (PDOException $e) {
            $pdo->rollBack();
            throw new Exception("Error retrieving data from $table: " . $e->getMessage());
        }
    }

    /**
     * @param string $table
     * @param array $where
     * @return bool
     * @throws Exception
     */
    public function deleteData(string $table, array $where): bool
    {
        $whereValues = [];
        foreach ($where as $key => $value) {
            $whereValues[] = "$key = ?";
        }
        $sql = "DELETE FROM $table WHERE " . implode(' AND ', $whereValues);
        try {
            $stmt = $this->connect()->prepare($sql);
            foreach ($where as $index => $value) {
                $stmt->bindParam((int)$index + 1, $where[$index]);
            }
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            throw new Exception("Error deleting data from $table: " . $e->getMessage());
        }
    }

    /**
     * @param int|null $code
     * @return array|mixed
     * @throws Exception
     */
    public function usersData(int $code = null): mixed
    {
        if (is_null($code)) {
            return $this->getData("users", ["user_avatar", "username", "username_slug"]);
        } else {
            return $this->getData("users", ["user_avatar", "username", "username_slug"], ["userCode" => $code])[0];
        }
    }

    /**
     * @param string $table
     * @param array $columns
     * @param array $where
     * @param bool $json
     * @param string $orderBy
     * @param string $orderColum
     * @param int $limit
     * @return array|string
     * @throws Exception
     */
    public function getData(string $table, array $columns = ['*'], array $where = [], bool $json=false, string $orderBy = '', string $orderColum = '', int $limit = 0): array|string
    {
        $pdo = $this->getPdo(); // PDO bağlantısını al
        $pdo->beginTransaction();
        $sql = "SELECT " . implode(',', $columns) . " FROM $table";
        if (!empty($where)) {
            $whereValues = [];
            foreach ($where as $key => $value) {
                $key = filter_var($key, FILTER_SANITIZE_FULL_SPECIAL_CHARS); // Kullanıcı girdisini filtrele
                $value = filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS); // Kullanıcı girdisini filtrele
                if (!empty($key) && !empty($value)) {
                    $whereValues[] = "$key=:$key";
                }
            }
            if (!empty($whereValues)) {
                $sql .= " WHERE " . implode(' AND ', $whereValues);
            }
        }
        if (!empty($orderBy)) {
            $sql .= " ORDER BY " . filter_var($orderColum, FILTER_SANITIZE_FULL_SPECIAL_CHARS) . " " . filter_var($orderBy, FILTER_SANITIZE_FULL_SPECIAL_CHARS); // Kullanıcı girdisini filtrele
        }
        if (!empty($limit)) {
            if (is_numeric($limit)) {
                $sql .= " LIMIT " . intval($limit);
            }
        }
        try {
            $stmt = $this->connect()->prepare($sql);
            foreach ($where as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            $select = $stmt->execute();
            if ($select) {
                $pdo->commit();
            } else {
                $pdo->rollBack();
            }

            if ($json) {
                $FETCH=PDO::FETCH_OBJ;
            } else {
                $FETCH=PDO::FETCH_ASSOC;
            }

            return $stmt->fetchAll($FETCH);

        } catch (PDOException $e) {
            $pdo->rollBack();
            throw new Exception("Error retrieving data from $table: " . $e->getMessage());
        }
    }

    /**
     * @param $table
     * @param $colum
     * @param $where
     * @param $value
     * @return float|int
     * @throws Exception
     */
    protected function uniqCode($table, $colum, $where, $value): float|int
    {
        $colum = filter_var($colum, FILTER_SANITIZE_FULL_SPECIAL_CHARS); // Kullanıcı girdisini filtrele
        $where = filter_var($where, FILTER_SANITIZE_FULL_SPECIAL_CHARS); // Kullanıcı girdisini filtrele
        $table = filter_var($table, FILTER_SANITIZE_FULL_SPECIAL_CHARS); // Kullanıcı girdisini filtrele
        $value = filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS); // Kullanıcı girdisini filtrele
        $sql = "SELECT $colum FROM $table WHERE $where=:$where";
        try {
            $stmt = $this->connect()->prepare($sql);
            $stmt->bindParam(":$where", $value, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->fetchAll(PDO::FETCH_ASSOC)) {
                return $this->uniqCode($table, $colum, $where, $this->generateCode());

            } else {
                return $this->generateCode();
            }
        } catch (PDOException $e) {
            throw new Exception("Error retrieving data from $table: " . $e->getMessage());
        }
    }

    /**
     * @return float|int
     */
    public function generateCode(): float|int
    {
        return substr(hexdec(uniqid()), 0, 10);
    }
}
