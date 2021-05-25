<?php

defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('max_execution_time', 0); 

require APPPATH . 'libraries/REST_Controller.php';

/**
 * Description of Graph
 *
 * @author Achmad Hafizh
 */
class Graph extends REST_Controller {

    function __construct() {
        parent::__construct();
        
        $this->_check_jwt();
        $this->load->database('default');
    }
    
    public function query_post() {
        $parameters = $this->post();
        
        if(!empty($parameters['tables'])) {
            $tables = $parameters['tables'];
        } else {
            $tables = null;
        }
        
        if(!empty($parameters['fields'])) {
            $fields = $parameters['fields'];
        } else {
            $fields = NULL;
        }
        
        if(!empty($parameters['conditions'])) {
            $conditions = $parameters['conditions'];
        } else {
            $conditions = NULL;
        }
        
        if(!empty($parameters['groups'])) {
            $groups = $parameters['groups'];
        } else {
            $groups = NULL;
        }
        
        if(!empty($parameters['orders'])) {
            $orders = $parameters['orders'];
        } else {
            $orders = NULL;
        }
        
        if(!empty($parameters['paging_start'])) {
            $paging_start = $parameters['paging_start'];
        } else {
            $paging_start = NULL;
        }
        
        if(!empty($parameters['paging_limit'])) {
            $paging_limit = $parameters['paging_limit'];
        } else {
            $paging_limit = NULL;
        }
        
        if(!empty($tables)) {
            $output['status'] = TRUE;
            $output['message'] = "Records found.";
            $data = $this->fetch($tables, $fields, $conditions, $groups, $orders, $paging_start, $paging_limit);
            
            $this->response(array_merge($output, $data), REST_Controller::HTTP_OK);
        } else {
            $output['status'] = FALSE;
            $output['message'] = 'Please check your parameters!';
            $output['error']['parameters'] = array('Required tables object!');
            $output['format']['parameters'] = array(
                'tables' => 'String',
                'fields' => 'array()',
                'conditions' => 'array()',
                'groups' => 'array()',
                'orders' => 'array()',
                'paging_start' => 'Integer',
                'paging_limit' => 'Integer'
            );

            $this->response($output, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
    
    public function query_debug_post() {
        $parameters = $this->post();
        
        $tables = $parameters['tables'];
        
        if(!empty($parameters['fields'])) {
            $fields = $parameters['fields'];
        } else {
            $fields = NULL;
        }
        
        if(!empty($parameters['conditions'])) {
            $conditions = $parameters['conditions'];
        } else {
            $conditions = NULL;
        }
        
        if(!empty($parameters['groups'])) {
            $groups = $parameters['groups'];
        } else {
            $groups = NULL;
        }
        
        if(!empty($parameters['orders'])) {
            $orders = $parameters['orders'];
        } else {
            $orders = NULL;
        }
        
        if(!empty($parameters['paging_start'])) {
            $paging_start = $parameters['paging_start'];
        } else {
            $paging_start = NULL;
        }
        
        if(!empty($parameters['paging_limit'])) {
            $paging_limit = $parameters['paging_limit'];
        } else {
            $paging_limit = NULL;
        }
        
        if(!empty($tables)) {
            $data = $this->fetch_debug($tables, $fields, $conditions, $groups, $orders, $paging_start, $paging_limit);
            echo json_encode($data);
        } else {
            $output['status'] = FALSE;
            $output['message'] = 'Please check your parameters!';
            $output['error']['parameters'] = array('Required tables object!');
            $output['format']['parameters'] = array(
                'tables' => 'String',
                'fields' => 'array()',
                'conditions' => 'array()',
                'groups' => 'array()',
                'orders' => 'array()',
                'paging_start' => 'Integer',
                'paging_limit' => 'Integer'
            );

            $this->response($output, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    function fetch($table, $column = NULL, $condition = NULL, $group_by = NULL, $order_by = NULL, $skip_row = NULL, $limit_row = NULL) {
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
        
        if(!empty($limit_row)) {
            $limit = " LIMIT ". $limit_row;
        } else {
            if(empty($con)) {
                $limit = " LIMIT 10";
            } else {
                $limit = "";
            }
        }
        
        if(!empty($skip_row)) {
            $skip = " OFFSET ". $skip_row;
        } else {
            $skip = "";
        }
        
        $sql = "SELECT " . $col . " FROM " . $table . $con. $group. $order . $limit . $skip;
        $query = $this->db->query($sql);
        return $this->format_output_select($sql, $query->result_array());
    }
    
    function fetch_debug($table, $column = NULL, $condition = NULL, $group_by = NULL, $order_by = NULL, $skip_row = NULL, $limit_row = NULL) {
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
        
        if(!empty($limit_row)) {
            $limit = " LIMIT ". $limit_row;
        } else {
            if(empty($con)) {
                $limit = " LIMIT 10";
            } else {
                $limit = "";
            }
        }
        
        if(!empty($skip_row)) {
            $skip = " OFFSET ". $skip_row;
        } else {
            $skip = "";
        }
        
        return "SELECT " . $col . " FROM " . $table . $con. $group. $order . $limit . $skip;
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

    function str_replace_first($from, $to, $subject) {
        $from = '/' . preg_quote($from, '/') . '/';
        return preg_replace($from, $to, $subject, 1);
    }

    function format_string($string) {
        $new_string = "";
        $array = explode(",", $string);
        
        foreach ($array as $row) {
            $new_string .= '"'.$row.'",';
        }
        
        return substr($new_string, 0, -1);
    }

    function format_output_select($last_sql, $data) {
        $output = array();
        
        if(ENVIRONMENT == "development") {
            $output['last_query'] = $last_sql;
        }
        
        $output['total_data'] = count($data);
        $output['data'] = $data;
        
        return $output;
    }
    
}
