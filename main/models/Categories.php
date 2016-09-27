<?php

class Categories extends ActiveRecord
{
    public function get_All_Categories()
    {
        $sql = 'SELECT * FROM Categories ORDER BY Id';
        $result = $this->execute($sql);
        return $result;
    }
}