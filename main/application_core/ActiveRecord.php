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
        $check = new Check();
        if (!$check->exists(self::$DBH)) {
            $connection = 'mysql:host=' . $this->host . ';dbname=' . $this->db_name . ';charset=utf8';
            self::$DBH = new PDO($connection, $this->username, $this->password);
        }
    }

    public static function disconnect()
    {
        $check = new Check();
        if ($check->exists(self::$DBH)) {
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

    public function select()
    {
        $tb = get_class($this);
        $sql = "SELECT * FROM {$tb}";
        $result = $this->execute($sql);
        return $result;
    }

}