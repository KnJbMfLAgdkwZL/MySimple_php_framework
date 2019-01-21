<?php

class ActiveRecord
{
    private $host;
    private $db_name;
    private $username;
    private $password;
    private static $DBH;

    public function __construct()
    {
        $config = Config::get();
        $this->host = $config['database']['host'];
        $this->db_name = $config['database']['db_name'];
        $this->username = $config['database']['username'];
        $this->password = $config['database']['password'];
    }

    private function connect()
    {
        if (empty(self::$DBH)) {
            $connection = 'mysql:host=' . $this->host . ';dbname=' . $this->db_name . ';charset=utf8';
            self::$DBH = new PDO($connection, $this->username, $this->password);
        }
    }

    public static function disconnect()
    {
        if (!empty(self::$DBH)) {
            self::$DBH = null;
        }
    }

    public function execute($sql, $params = null)
    {
        $this->connect();
        $stm = self::$DBH->prepare($sql);
        $stm->execute($params);
        $result = $stm->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function get_column_name_v1()
    {
        $param = [
            ':tb' => get_class($this),
            ':db' => $this->db_name
        ];
        $sql = 'SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = :tb AND table_schema = :db';
        $result = $this->execute($sql, $param);
        foreach ($result as $k => &$v) {
            $v = $v['COLUMN_NAME'];
        }
        return $result;
    }

    public function get_column_name_v2()
    {
        $tb = get_class($this);
        $sql = "SHOW COLUMNS FROM {$tb}";
        $result = $this->execute($sql);
        foreach ($result as $k => &$v) {
            $v = $v['Field'];
        }
        return $result;
    }

    public function get_column_name_v3()
    {
        $tb = get_class($this);
        $sql = "DESCRIBE {$tb}";
        $result = $this->execute($sql);
        foreach ($result as $k => &$v) {
            $v = $v['Field'];
        }
        return $result;
    }

    private function get_condition_string($condition)
    {
        $where = '';
        if (count($condition)) {
            $where = 'WHERE ' . implode(' AND ', array_map(
                    function ($v, $k) {
                        return sprintf("%s = :%s", $k, $k);
                    },
                    $condition,
                    array_keys($condition)
                ));
        }
        return $where;
    }

    public function select($condition = [])
    {
        $where = $this->get_condition_string($condition);
        $tb = get_class($this);
        $sql = "SELECT * FROM {$tb} {$where}";
        $result = $this->execute($sql, $condition);
        return $result;
    }

    public function delete($condition = [])
    {
        if (!count($condition)) {
            return false;
        }
        $where = $this->get_condition_string($condition);
        $tb = get_class($this);
        $sql = "DELETE FROM {$tb} {$where}";
        $result = $this->execute($sql, $condition);
        return $result;
    }

    public function insert($param = [])
    {
        if (!count($param)) {
            return false;
        }
        $keys = implode(', ', array_keys($param));
        $values = implode(', ', array_map(
            function ($k) {
                return sprintf(":%s", $k);
            },
            array_keys($param)
        ));
        $tb = get_class($this);
        $sql = "INSERT INTO {$tb} ({$keys}) VALUES ($values)";
        $this->execute($sql, $param);
        $result = self::$DBH->lastInsertId();
        return $result;
    }

    public function update($param = [], $condition = [])
    {
        if (!count($param)) {
            return false;
        }
        $_param = [];
        foreach ($param as $k => $v)
            $_param[":set_{$k}"] = $v;
        foreach ($condition as $k => $v)
            $_param[$k] = $v;
        $set = implode(', ', array_map(
            function ($v, $k) {
                return sprintf("%s = :set_%s", $k, $k);
            },
            $param,
            array_keys($param)
        ));
        $where = $this->get_condition_string($condition);
        $tb = get_class($this);
        $sql = "UPDATE {$tb} SET {$set} {$where}";
        $result = $this->execute($sql, $_param);
        return $result;
    }

}