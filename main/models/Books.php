<?php

class Books extends ActiveRecord
{
    public function get_Books_By_Categories_Id($id, $colums = '*')
    {
        $sql = "SELECT {$colums} FROM Books WHERE Categories = :id ORDER BY Id";
        $result = $this->execute($sql, [':id' => $id]);
        return $result;
    }

    public function get_Book_By_Id($id)
    {
        $sql = 'SELECT * FROM Books WHERE Id = :id ORDER BY Id';
        $result = $this->execute($sql, [':id' => $id]);
        return $result;
    }
}