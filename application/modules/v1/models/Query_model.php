<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Description of Query_model
 *
 * @author Achmad Hafizh
 */
class Query_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function executeQuery($sql)
    {
        $query = $this->db->query($sql);

        if(strtolower(substr($sql, 0, 6)) == "select") {
            $result = $query->result_array();
            $total_data = count($result);
        } else {
            $result = array();
            $total_data = $this->db->affected_rows();
        }
        
        return array(
            'affected_rows' => $this->db->affected_rows(),
            'total_data' => $total_data,
            'data' => $result,
            'error_detail' => $this->db->error()
        );
    }

    function executeTransaction($listSql)
    {
        $error = array();
        $affected_rows = 0;

        $this->db->trans_begin();

        foreach ($listSql as $sql) {
            $this->db->query($sql);
            $affected_rows += $this->db->affected_rows();

            if ($this->db->trans_status() === FALSE) {
                $error = $this->db->error();
                $error['sql'] = $sql;
                $this->db->trans_rollback();
            }
        }

        if ($this->db->trans_status() === TRUE) {
            $error = array();
            $this->db->trans_commit();
        }

        return array(
            'affected_rows' => $affected_rows,
            'error_sql' => $error,
            'error_detail' => $this->db->error()
        );
    }
}
