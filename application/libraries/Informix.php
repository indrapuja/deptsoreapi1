<?php

/**
 * Description of Informix
 *
 * @author Achmad Hafizh
 */
class Informix {
    
    protected $ci;

    public function __construct() {
        $this->ci = & get_instance();
        $this->ci->load->database('informix');
    }
    
    public function get($table, $column = NULL, $condition = NULL, $group_by = NULL, $order_by = NULL, $skip_row = NULL, $limit_row = NULL) {
        if(!empty($column)) {
            $col = $this->reformat_select_column($column);
        } else {
            $col = "*";
        }
        
        if(!empty($condition)) {
            $con = " WHERE ". $this->reformat_select_condition($condition);
        } else {
            $con = "";
        }
        
        if(!empty($group_by)) {
            $group = " GROUP BY ". $this->reformat_select_group_and_order($group_by);
        } else {
            $group = "";
        }
        
        if(!empty($order_by)) {
            $order = " ORDER BY ". $this->reformat_select_group_and_order($order_by);
        } else {
            $order = "";
        }
        
        if(!empty($skip_row)) {
            $skip = " SKIP ". $skip_row;
        } else {
            $skip = "";
        }
        
        if(!empty($limit_row)) {
            $limit = " LIMIT ". $limit_row;
        } else {
            if(empty($con)) {
                $limit = " LIMIT 10";
            } else {
                $limit = "";
            }
        }
        
        $sql = "SELECT " . $col . " FROM " . $table . $con. $group. $order . $skip . $limit;
        $query = $this->ci->db->query($sql);
        return $this->format_output_select($sql, $query->result_array());
    }
    
    public function get_debug($table, $column = NULL, $condition = NULL, $group_by = NULL, $order_by = NULL, $skip_row = NULL, $limit_row = NULL) {
        if(!empty($column)) {
            $col = $this->reformat_select_column($column);
        } else {
            $col = "*";
        }
        
        if(!empty($condition)) {
            $con = " WHERE ". $this->reformat_select_condition($condition);
        } else {
            $con = "";
        }
        
        if(!empty($group_by)) {
            $group = " GROUP BY ". $this->reformat_select_group_and_order($group_by);
        } else {
            $group = "";
        }
        
        if(!empty($order_by)) {
            $order = " ORDER BY ". $this->reformat_select_group_and_order($order_by);
        } else {
            $order = "";
        }
        
        if(!empty($skip_row)) {
            $skip = " SKIP ". $skip_row;
        } else {
            $skip = "";
        }
        
        if(!empty($limit_row)) {
            $limit = " LIMIT ". $limit_row;
        } else {
            if(empty($con)) {
                $limit = " LIMIT 10";
            } else {
                $limit = "";
            }
        }
        
        return "SELECT " . $col . " FROM " . $table . $con. $group. $order . $skip . $limit;
    }

    public function insert($table, $data) {
        $error = NULL;
        $last_insert_id = 0;
        $sql_insert = $this->reformat_insert($table, $data);
        $sql_info = "SELECT DBINFO( 'bigserial' ) AS last_insert_id FROM systables WHERE tabid = 1";
        
        $this->ci->db->trans_begin();
        $this->ci->db->query($sql_insert);
        $affected_rows = $this->ci->db->affected_rows();
        $get_info = $this->ci->db->query($sql_info);
        
        if ($this->ci->db->trans_status() === FALSE) {
            $error = $this->ci->db->error();
            
            if($error['code'] == "00000") {
                $error['message'] = "Data values doesn't match the column type!";
            }
            
            $this->ci->db->trans_rollback();
        } else {
            $last_insert_id = $get_info->result()[0]->last_insert_id;
                    
            $this->ci->db->trans_commit();
        }
        
        return $this->format_output_insert($affected_rows, $last_insert_id, $sql_insert, $error);
    }
    
    public function insert_batch($table, $data) {
        $error = NULL;
        $sql_insert = "";
        $affected_rows = 0;
        $last_insert_id = 0;
        
        $this->ci->db->trans_begin();
        
        foreach ($data as $rows) {
            $sql_insert .= $this->reformat_insert($table, $rows).";".PHP_EOL;
            $this->ci->db->query($sql_insert);
            $affected_rows += $this->ci->db->affected_rows();
        }
        
        $sql_info = "SELECT DBINFO( 'bigserial' ) AS last_insert_id FROM systables WHERE tabid = 1";
        $get_info = $this->ci->db->query($sql_info);
        
        if ($this->ci->db->trans_status() === FALSE) {
            $error = $this->ci->db->error();
            
            if($error['code'] == "00000") {
                $error['message'] = "Data values doesn't match the column type!";
            }
            
            $this->ci->db->trans_rollback();
        } else {
            $last_insert_id = $get_info->result()[0]->last_insert_id;
                    
            $this->ci->db->trans_commit();
        }
        
        return $this->format_output_insert($affected_rows, $last_insert_id, $sql_insert, $error);
    }
    
    public function update($table, $data, $key) {
        $error = NULL;
        $sql_update = $this->reformat_update($table, $data, $key);
        
        $this->ci->db->trans_begin();
        $this->ci->db->query($sql_update);
        
        if ($this->ci->db->trans_status() === FALSE) {
            $error = $this->ci->db->error();
            
            if($error['code'] == "00000") {
                $error['message'] = "Data values doesn't match the column type!";
            }
            
            $this->ci->db->trans_rollback();
        } else {        
            $this->ci->db->trans_commit();
        }
        
        return $this->format_output_insert($this->ci->db->affected_rows(), 0, $sql_update, $error);
    }
    
    public function update_batch($table, $data, $key) {
        $error = NULL;
        $sql_update = "";
        $affected_rows = 0;
        
        if(count($data) == count($key)) {
            $this->ci->db->trans_begin();
            
            $i = 0;
            
            foreach ($data as $rows) {
                $sql_update .= $this->reformat_update($table, $rows, $key[$i]) . ";" . PHP_EOL;
                $this->ci->db->query($this->reformat_update($table, $rows, $key[$i]));
                $affected_rows += $this->ci->db->affected_rows();
                $i++;
            }

            if ($this->ci->db->trans_status() === FALSE) {
                $error = $this->ci->db->error();

                if ($error['code'] == "00000") {
                    $error['message'] = "Data values doesn't match the column type!";
                }

                $this->ci->db->trans_rollback();
            } else {
                $this->ci->db->trans_commit();
            }
        } else {
            $error = array('message' => 'Total Values and Keys not match!');
        }
        
        return $this->format_output_insert($affected_rows, 0, $sql_update, $error);
    }
    
    public function delete($table, $keys) {
        $error = NULL;
        $sql_delete = $this->reformat_delete($table, $keys);
        
        $this->ci->db->trans_begin();
        $this->ci->db->query($sql_delete);
        $affected_rows = $this->ci->db->affected_rows();
        
        if ($this->ci->db->trans_status() === FALSE) {
            $error = $this->ci->db->error();
            
            if($error['code'] == "00000") {
                $error['message'] = "Data values doesn't match the column type!";
            }
            
            $this->ci->db->trans_rollback();
        } else {    
            $this->ci->db->trans_commit();
        }
        
        return $this->format_output_insert($affected_rows, 0, $sql_delete, $error);
    }
    
    public function delete_batch($table, $keys) {
        $error = NULL;
        $sql_delete = "";
        $affected_rows = 0;
        
        $this->ci->db->trans_begin();
        
        foreach($keys as $rows) {
            $sql_delete .= $this->reformat_delete($table, $rows).";".PHP_EOL;
            $this->ci->db->query($this->reformat_delete($table, $rows));
            $affected_rows += $this->ci->db->affected_rows();
        }
        
        if ($this->ci->db->trans_status() === FALSE) {
            $error = $this->ci->db->error();
            
            if($error['code'] == "00000") {
                $error['message'] = "Data values doesn't match the column type!";
            }
            
            $this->ci->db->trans_rollback();
        } else {    
            $this->ci->db->trans_commit();
        }
        
        return $this->format_output_insert($affected_rows, 0, $sql_delete, $error);
    }
    
    function reformat_select_column($data) {
        $col = "";

        foreach ($data as $column) {
            $col = $col .", ". trim($column);
        }

        return $this->str_replace_first(", ", "", $col);
    }
    
    function reformat_select_condition($data) {
        $string = "";

        foreach ($data as $column => $value) {
            if(substr(trim($value), 0, 1) == "{" AND substr(trim($value), -1) == "}") {
                $string .= " " . trim($column) . " ( " . substr(trim(substr($value, 1)), 0, -1) . " )";
            } else {
                $string .= " " . trim($column) . " ( " . $this->format_string(trim($value)) . " )";
            }
        }

        return $this->str_replace_first(" AND ", "", $string);
    }
    
    function reformat_select_group_and_order($data) {
        $col = "";

        foreach ($data as $column) {
            $col = $col .", ". trim($column);
        }

        return $this->str_replace_first(", ", "", $col);
    }
    
    function reformat_insert($table, $parameters) {
        $col = "";
        $val = "";

        foreach ($parameters as $column => $value) {
            $col = $col .", ". trim($column);
            $val = $val .", ". $this->to_string(trim($value));
        }

        $values = $this->str_replace_first(", ", "", $val);
        $columns = $this->str_replace_first(", ", "", $col);

        $sql = "INSERT INTO $table ($columns) VALUES ($values)";
        
        return $sql;
    }
    
    function reformat_update($table, $parameters, $keys) {
        $rec = "";
        $con = "";
        
        foreach ($parameters as $column => $value) {
            $rec = $rec.", ".$column." = ".$this->to_string(trim($value));
        }
        
        foreach ($keys as $column => $value) {
            $con = $con." AND ".$column." IN(".$this->to_string(trim($value)).")";
        }
        
        $records = $this->str_replace_first(", ", "", trim($rec));
        $conditions = $this->str_replace_first("AND ", "", trim($con));
        
        $sql = "UPDATE $table SET $records WHERE $conditions";
        
        return $sql;
    }
    
    function reformat_delete($table, $keys) {
        $con = "";
        
        foreach ($keys as $column => $value) {
            $con = $con." AND ".$column." IN(". $this->to_string(trim($value)) .")";
        }
        
        $conditions = $this->str_replace_first("AND ", "", trim($con));
        
        $sql = "DELETE $table WHERE $conditions";
        
        return $sql;
    }
    
    function format_output_insert($affected, $last_insert_id, $last_sql, $error) {
        $output = array();
        $output['affected_rows'] = $affected;
        $output['last_insert_id'] = $last_insert_id;
        
        if(ENVIRONMENT == "development") {
            $output['last_query'] = $last_sql;
        }
        
        $output['error'] = $error;
        
        return $output;
    }
    
    function format_output_select($last_sql, $data) {
        $output = array();
        
        if(ENVIRONMENT == "development") {
            $output['last_query'] = $last_sql;
        }
        
        $output['total_data'] = count($data);
        $output['data'] = $this->trim_data($data);
        
        return $output;
    }
    
    function str_replace_first($from, $to, $subject) {
        $from = '/' . preg_quote($from, '/') . '/';
        return preg_replace($from, $to, $subject, 1);
    }
    
    function to_string($string) {
        return '"'.$string.'"';
    }
    
    function format_string($string) {
        $new_string = "";
        $array = explode(",", $string);
        
        foreach ($array as $row) {
            $new_string .= '"'.$row.'",';
        }
        
        return substr($new_string, 0, -1);
    }
    
    function trim_data($array) {
        $new_array = array();
        
        foreach ($array as $row) {
            $data = array();
            
            foreach ($row as $col => $val) {
                $data[$col] = trim($val);
            }
            
            $new_array[] = (object) $data;
        }
        
        return $new_array;
    }
    
}
