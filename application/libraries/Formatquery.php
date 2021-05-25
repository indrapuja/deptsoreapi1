<?php

/**
 * Description of FormatQuery
 *
 * @author Achmad Hafizh
 */
class FormatQuery {
    
    function insert_format($table, $parameters) {
        $col = "";
        $val = "";

        foreach ($parameters as $column => $value) {
            $col = $col." , ".trim($column).PHP_EOL;
            $val = $val." , ".trim($value).PHP_EOL;
        }

        $values = $this->str_replace_first(",", "", $val);
        $columns = $this->str_replace_first(",", "", $col);

        $sql = "INSERT INTO $table ($columns) VALUES ($values)";
        
        return $sql;
    }
    
    function update_format($table, $keys, $parameters) {
        $rec = "";
        $con = "";
        
        foreach ($keys as $column => $value) {
            $con = $con." AND ".$column." IN(".$value.")".PHP_EOL;
        }
        
        foreach ($parameters as $column => $value) {
            $rec = $rec." , ".$column." = ".$value.PHP_EOL;
        }
        
        $records = $this->str_replace_first(",", "", $rec);
        $conditions = $this->str_replace_first("AND", "", $con);
        
        $sql = "UPDATE $table SET $records WHERE $conditions";
        
        return $sql;
    }
    
    function delete_format($table, $keys) {
        $con = "";
        
        foreach ($keys as $column => $value) {
            $con = $con." AND ".$column." IN(".$value.")".PHP_EOL;
        }
        
        $conditions = $this->str_replace_first("AND", "", $con);
        
        $sql = "DELETE $table WHERE $conditions";
        
        return $sql;
    }
    
    function str_replace_first($from, $to, $subject) {
        $from = '/' . preg_quote($from, '/') . '/';
        return preg_replace($from, $to, $subject, 1);
    }

}
