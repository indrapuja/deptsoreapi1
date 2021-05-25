<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Description of Access_user_model
 *
 * @author Achmad Hafizh
 */
class Access_user_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database('default');
    }

    function get_user($id)
    {
        $replace_string = '"' . "'" . '"';
        $query = "SELECT a.group_no, a.user_no, a.user_name, a.dept_alias, a.store_list, email, TO_CHAR(REPLACE(a.hrddept_list, " . $replace_string . ", '')) AS hrddept_list,
                         a.cred_no, a.cred_name, a.class_list, a.foto, a.user_status, a.user_admin, 
                         a.ts_admin, a.ts_activation, b.group_name, b.ma_title, b.ma_name, 
                         b.menu_list
                  FROM access_user a
                  LEFT OUTER JOIN access_group b ON a.group_no = b.group_no
                  WHERE (a.user_no IN($id) 
                     OR LOWER(a.user_name) IN($id))   
                    AND a.user_status = 2
                    ORDER BY a.ts_admin DESC";
        $execute = $this->db->query($query);
        $result = $execute->result();

        if (!empty($result)) {
            foreach ($result as $obj) {
                $obj->hrddept_list = $obj->hrddept_list;
            }
        }

        return $this->format_output_select($result);
    }

    function get_user_by_group($group_no)
    {
        $replace_string = '"' . "'" . '"';
        $query = "SELECT a.group_no, a.user_no, a.user_name, a.dept_alias, a.store_list, email, TO_CHAR(REPLACE(a.hrddept_list, " . $replace_string . ", '')) AS hrddept_list,
                         a.cred_no, a.cred_name, a.class_list, a.foto, a.user_status, a.user_admin, 
                         a.ts_admin, a.ts_activation, b.group_name, b.ma_title, b.ma_name, 
                         b.menu_list
                  FROM access_user a
                  LEFT OUTER JOIN access_group b ON a.group_no = b.group_no
                  WHERE a.group_no = '$group_no'
                    AND a.user_status = 2";

        $execute = $this->db->query($query);
        $result = $execute->result();

        return $this->format_output_select($result);
    }

    function get_custom_user($store_list, $hrddept_list)
    {
        $replace_string = '"' . "'" . '"';
        $where_store_list = "";
        $where_hrddept_list = "";

        if (!empty($store_list)) {
            $where_store_list = "AND a.store_list IN($store_list)";
        } else if (!empty($hrddept_list)) {
            $where_hrddept_list = "AND a.cred_no IN (SELECT d.cred_no 
                                                     FROM class c, contract d, dept e 
                                                     WHERE c.class_no = d.class_no 
                                                       AND c.dept_no = e.dept_no 
                                                       AND ((e.hrd_dept IN($hrddept_list)) AND (d.period_con2 > 'today')) 
                                                     GROUP BY 1) ";
        }

        $query = "SELECT a.group_no, a.user_no, a.user_name, a.dept_alias, a.store_list, email, TO_CHAR(REPLACE(a.hrddept_list, " . $replace_string . ", '')) AS hrddept_list,
                         a.cred_no, a.cred_name, a.class_list, a.category_list, a.foto, a.user_status, a.user_admin, 
                         a.ts_admin, a.ts_activation, b.group_name, b.ma_title, b.ma_name, 
                         b.menu_list
                  FROM access_user a
                  LEFT OUTER JOIN access_group b ON a.group_no = b.group_no
                  WHERE user_status IS NOT NULL
                        $where_store_list
                        $where_hrddept_list
                  ORDER BY a.ts_admin DESC";
        $execute = $this->db->query($query);
        $result = $execute->result();

        if (!empty($result)) {
            foreach ($result as $obj) {
                $obj->hrddept_list = $obj->hrddept_list;
            }
        }

        return $this->format_output_select($result);
    }

    function get_spesific_user($user_id, $user_password, $type = NULL)
    {
        $replace_string = '"' . "'" . '"';
        $query = "SELECT a.group_no, a.user_no, a.user_name, a.dept_alias, a.store_list, email, a.category_list,
                         REPLACE(a.hrddept_list, " . $replace_string . ", '') AS hrddept_list,
                         a.cred_no, a.cred_name, a.class_list, a.foto, a.user_status, a.user_admin, 
                         a.ts_admin, a.ts_activation
                  FROM master_user a
                  WHERE (a.user_no IN($user_id) 
                     OR LOWER(a.user_name) IN($user_id))   
                    AND a.user_password = $user_password     
                    AND a.user_status = 2
                    LIMIT 1";

        $execute = $this->db->query($query);

        $user = $execute->result();

        if (!empty($user)) {
            foreach ($user as $obj) {
                $group_detail = $this->get_group($obj->group_no);
                $obj->hrddept_list = $obj->hrddept_list;
                $obj->MenuGroup = $group_detail;
                $obj->MenuList = $this->get_menu($group_detail->menu_list, $type);
            }
        }

        return $this->format_output_select($user);
    }

    function get_staff_user($user_id, $user_password)
    {
        $query = "SELECT CASE
                             WHEN a.cred_no = '' THEN '16'
                             ELSE '99' 
                         END AS group_no,
                         a.nik AS user_no, a.name AS user_name, 'STAFF/SPG' AS dept_alias, a.store_no AS store_list, '' AS email, a.hrd_dept AS hrddept_list, 
                         a.cred_no, TRIM(b.cred_name) AS cred_name, '' AS class_list, '' AS foto, 2 AS user_status, 'AUTO' AS user_admin, current AS ts_admin, current AS ts_activation
                  FROM view_login_staff_and_spg a
                  LEFT JOIN creditor b ON b.cred_no = a.cred_no
                  WHERE (a.nik = $user_id OR LOWER(a.name) = $user_id) 
                    AND a.password = $user_password 
                    AND a.cred_no = ''     
                  LIMIT 1";
        $execute = $this->db->query($query);

        $user = $execute->result();

        if (!empty($user)) {
            foreach ($user as $obj) {
                $group_detail = $this->get_group($obj->group_no);
                $obj->hrddept_list = $obj->hrddept_list;
                $obj->class_list = $this->get_class_list($obj->user_no);
                $obj->MenuGroup = $group_detail;
                $obj->MenuList = $this->get_menu($group_detail->menu_list);
            }
        }

        return $this->format_output_select($user);
    }

    function get_staff_spg_user($user_id, $user_password)
    {
        $query = "SELECT CASE
                             WHEN a.cred_no = '' THEN '16'
                             ELSE '99' 
                         END AS group_no,
                         a.nik AS user_no, a.name AS user_name, 'STAFF/SPG' AS dept_alias, a.store_no AS store_list, '' AS email, a.hrd_dept AS hrddept_list, 
                         a.cred_no, TRIM(b.cred_name) AS cred_name, '' AS class_list, '' AS foto, 2 AS user_status, 'AUTO' AS user_admin, current AS ts_admin, current AS ts_activation
                  FROM view_login_staff_and_spg a
                  LEFT JOIN creditor b ON b.cred_no = a.cred_no
                  WHERE (a.nik = $user_id OR LOWER(a.name) = $user_id) 
                    AND a.password = $user_password
                  LIMIT 1";

        $execute = $this->db->query($query);

        $user = $execute->result();

        if (!empty($user)) {
            foreach ($user as $obj) {
                $group_detail = $this->get_group($obj->group_no);
                $obj->hrddept_list = $obj->hrddept_list;
                $obj->class_list = $this->get_class_list($obj->user_no);
                $obj->MenuGroup = $group_detail;
                $obj->MenuList = $this->get_menu($this->format_string($group_detail->menu_list));
            }
        }

        return $this->format_output_select($user);
    }

    function get_staff_spg_user_custom($user_id)
    {
        $query = "SELECT '19' AS group_no,
                         a.nik AS user_no, a.name AS user_name, 'STAFF/SPG' AS dept_alias, a.store_no AS store_list, '' AS email, a.hrd_dept AS hrddept_list, 
                         a.cred_no, TRIM(b.cred_name) AS cred_name, '' AS class_list, '' AS foto, 2 AS user_status, 'AUTO' AS user_admin, current AS ts_admin, current AS ts_activation
                  FROM view_login_staff_and_spg a
                  LEFT JOIN creditor b ON b.cred_no = a.cred_no
                  WHERE (a.nik = $user_id OR LOWER(a.name) = $user_id) 
                  LIMIT 1";
        $execute = $this->db->query($query);

        $user = $execute->result();

        if (!empty($user)) {
            foreach ($user as $obj) {
                $group_detail = $this->get_group($obj->group_no);
                $obj->hrddept_list = $obj->hrddept_list;
                $obj->class_list = $this->get_class_list($obj->user_no);
                $obj->MenuGroup = $group_detail;
                $obj->MenuList = $this->get_menu($group_detail->menu_list, "NEW");
            }
        }

        return $this->format_output_select($user);
    }

    function get_class_list($user_no)
    {
        $string = "";
        $mip_no = date('ymm');
        $query = "SELECT unique a.class_no FROM contract a WHERE a.cred_no= (SELECT b.cred_no FROM hrd_spg b WHERE b.nik='$user_no') and a.period_con2>today";
        //SELECT class_no FROM cis_person WHERE nik_no = '$user_no' AND mip_no = $mip_no GROUP BY 1 
        $execute = $this->db->query($query);
        $result = $execute->result();

        if (!empty($result)) {
            foreach ($result as $obj) {
                $string .= $obj->class_no . ",";
            }
        }

        if (!empty($string)) {
            return substr($string, 0, -1);
        } else {
            return $string;
        }
    }

    function get_group($group_no)
    {
        $query = "SELECT * 
                  FROM master_group
                  WHERE group_no IN($group_no)
                  ORDER BY group_no ASC LIMIT 1";

        $execute = $this->db->query($query);
        return $execute->row();
    }
 
    function get_menu($menu_list, $type = NULL)
    {
        $query = "SELECT a.group_no, a.menu_no, a.parent_no, a.menu_name, a.menu_icon, a.menu_url, a.menu_sidebar
                      FROM master_menu a
                      WHERE a.menu_no IN($menu_list) 
                        AND a.parent_no = 0";

        $execute = $this->db->query($query);

        $menu = $execute->result();

        if (!empty($menu)) {
            foreach ($menu as $obj) {
                $obj->Child = $this->get_child_menu($obj->menu_no, $type);
            }
        }

        return $menu;
    }

    function get_child_menu($parent_no, $type = NULL)
    {
        /*if($type == "NEW") {
            $query = "SELECT a.group_no, a.menu_no, a.parent_no, a.menu_name, a.menu_icon, a.menu_url, a.menu_sidebar
                      FROM access_menu a
                      WHERE a.parent_no IN($parent_no) 
                      ORDER BY a.menu_no ASC";
        } else {
            $query = "SELECT a.group_no, a.menu_no, a.parent_no, a.menu_name, a.menu_icon, a.menu_url, a.menu_sidebar
                      FROM access_menu_old a
                      WHERE a.parent_no IN($parent_no) 
                      ORDER BY a.menu_no ASC";
        }*/
        $query = "SELECT a.group_no, a.menu_no, a.parent_no, a.menu_name, a.menu_icon, a.menu_url, a.menu_sidebar
                      FROM master_menu a
                      WHERE a.parent_no IN($parent_no) 
                      ORDER BY a.menu_no ASC";

        $execute = $this->db->query($query);
        $menu = $execute->result();

        if (!empty($menu)) {
            foreach ($menu as $obj) {
                $obj->Child = $this->get_child_menu($obj->menu_no, $type);
            }
        }

        return $menu;
    }

    function get_last_no($cred_no)
    {
        $query = "SELECT MAX(user_no) AS last_no
                  FROM access_user 
                  WHERE cred_no = '$cred_no'";
        $execute = $this->db->query($query);
        return $execute->row();
    }

    function get_group_internal()
    {
        $query = "SELECT * FROM access_group WHERE group_no NOT IN(90,80,8080) ORDER BY group_no ASC";
        $execute = $this->db->query($query);
        return $this->format_output_select($execute->result());
    }

    function get_group_external()
    {
        $query = "SELECT * FROM access_group WHERE group_no IN(90,91,92,93,99) ORDER BY group_no ASC";
        $execute = $this->db->query($query);
        return $this->format_output_select($execute->result());
    }

    function create_login_history($nik)
    {
        $error = array();
        $data = array();

        $data['nik'] = $nik;
        $data['app'] = "WEB";

        $this->db->trans_start();
        $this->db->query($this->db->insert_string("master_login", $data));
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $error = $this->db->error();
        }

        $output = array();
        $output['error'] = $error;
        $output['total_affected'] = $this->db->affected_rows();

        return $output;
    }

    function format_output_select($data)
    {
        $output = array();
        $output['total_records'] = count($data);
        $output['data'] = $data;

        return $output;
    }

    function format_string($string)
    {
        $new_string = "";
        $array = explode(",", $string);

        foreach ($array as $row) {
            $new_string .= "'" . $row . "',";
        }

        return substr($new_string, 0, -1);
    }

    function to_string($string)
    {
        return "'" . $string . "'";
    }
}