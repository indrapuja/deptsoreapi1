<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Description of User_model
 *
 * @author Achmad Hafizh
 */
class Brand_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function get_brand($client_id)
    {
        $sql = "SELECT no as brand_no, name as brand_name 
                FROM product_brand 
                WHERE client_id = '$client_id' order by 2 ASC";
        $query = $this->db->query($sql);

        return format_output_select($query->num_rows(), $query->result());
    }



    //-----------------------------------------------------
    function format_string($string) {
        $new_string = "";
        $array = explode(",", $string);
        
        foreach ($array as $row) {
            $new_string .= "'".$row."',";
        }
        
        return substr($new_string, 0, -1);
    }

    function api_login($username, $password)
    {
        $sql = "SELECT *
                  FROM master_user_api a
                  LEFT JOIN master_app b ON b.app_id = a.app_id
                  WHERE a.user_api_username = '$username'
                    AND a.user_api_password = '$password'
                    AND a.user_api_status = 'ACTIVE'
                  ORDER BY a.user_api_name ASC";
        $query = $this->db->query($sql);

        return format_output_select($query->num_rows(), $query->result());
    }

    function api_validate_id($id)
    {
        $sql = "SELECT *
                FROM master_user_api a
                LEFT JOIN master_app b ON b.app_id = a.app_id
                WHERE a.user_api_username = '$id'
                  AND a.user_api_status = 'ACTIVE'
                ORDER BY a.user_api_name ASC";
        $query = $this->db->query($sql);

        return format_output_select($query->num_rows(), $query->result());
    }

    function get_user($user_id)
    {
        $query = "SELECT *
                  FROM master_user_app
                  WHERE (LOWER(user_email) = '$user_id' OR LOWER(user_phone) = '$user_id')   
                    AND user_status = 'ACTIVE'
                    AND current_timestamp() <= user_expired";
        $query = $this->db->query($query);

        return format_output_select($query->num_rows(), $query->row());
    }

    function validate_user($user_id, $user_password)
    {
        // $query = "SELECT *
        //           FROM master_user_app
        //           WHERE (LOWER(user_email) = '$user_id' OR LOWER(user_phone) = '$user_id')   
        //             AND user_status = 'ACTIVE'
        //             AND current_timestamp() <= user_expired";
        $query = "SELECT * 
                  FROM view_user
                  WHERE (LOWER(user_email) = '$user_id' OR LOWER(user_phone) = '$user_id')   
                            AND is_active = TRUE
                            AND curdate() <= date_expired";
        $query = $this->db->query($query);
        $result = $query->row();

        if (!empty($result) and $user_password == $this->encryption->decrypt($result->user_password)) {
            unset($result->user_password);
            return format_output_select($query->num_rows(), $result);
        } else {
            return format_output_select(0, null);
        }
    }

    function getAccessMenu($user_id)
    {
        $menu = array();
        $topMenuList = $this->fetchTopMenu($user_id);

        if (!empty($topMenuList)) {
            foreach ($topMenuList as $obj) {
                $obj->children = $this->getChildMenu($obj->user_id, $obj->menu_id);
                $menu[] = $obj;
            }
        }


        return array('data' => $menu);
    }

    function getChildMenu($user_id, $menu_id)
    {
        $menu = $this->fetchChildMenu($user_id, $menu_id);

        if (!empty($menu)) {
            $i = 0;

            foreach ($menu as $obj) {
                if ($obj->has_child == 1) {
                    $detail_menu = $this->getChildMenu($obj->user_id, $obj->menu_id);

                    if (!empty($detail_menu)) {
                        $sub_child = array();

                        foreach ($detail_menu as $objx) {
                            $sub_child[] = $objx;
                        }

                        $obj->children = $sub_child;
                    }
                }

                $i++;
            }
        }

        return $menu;
    }

    function fetchTopMenu($user_id)
    {
        $sql = "SELECT *
                FROM view_user_menu
                WHERE user_id = $user_id
                  AND parent_id = 0
                ORDER BY menu_id ASC";

        $query = $this->db->query($sql);
        return $query->result();
    }

    function fetchChildMenu($user_id, $parent_id)
    {
        $sql = "SELECT *
                FROM view_user_menu
                WHERE user_id = $user_id
                  AND parent_id = $parent_id
                ORDER BY parent_id, sort_no ASC";

        $query = $this->db->query($sql);
        return $query->result();
    }

    // function getAccessMenu($clientId, $menuPrivilege = null)
    // {
    //     $menu = array();
    //     $client = $this->fetchClient($clientId);
    //     $client->active_package_list = $this->fetchClientAccess($client->client_package_privilege);

    //     foreach ($client->active_package_list as $package) {
    //         if (!empty($package->active_group_list)) {
    //             $topMenuList = $this->fetchTopMenu($package->active_group_list);
    //         }
    //     }

    //     if (!empty($topMenuList)) {
    //         foreach ($topMenuList as $obj) {
    //             if(!empty($menuPrivilege)) {
    //                 $obj->children = $this->getChildMenu($obj->menu_no, $menuPrivilege);
    //             } else {
    //                 $obj->children = $this->getChildMenu($obj->menu_no);
    //             }

    //             $menu[] = $obj;
    //         }
    //     }

    //     return array('data' => $menu);
    // }

    function fetchClient($clientId)
    {
        $sql = "SELECT *
                FROM master_client
                WHERE client_no = $clientId
                  AND client_status = 'ACTIVE'
                ORDER BY client_name ASC";

        $query = $this->db->query($sql);

        return $query->row();
    }

    function fetchClientAccess($clientPackagePrivilege)
    {
        $sql = "SELECT *
                FROM master_client_package
                WHERE package_id IN($clientPackagePrivilege)
                  AND package_status = 'ACTIVE'
                ORDER BY package_name ASC";

        $query = $this->db->query($sql);
        $packages = $query->result();

        if (!empty($packages)) {
            foreach ($packages as $obj) {
                $obj->active_group_list = $this->fetchGroup($obj->package_group_privilege);
            }
        }

        return $query->result();
    }

    function fetchGroup($packageGroupPrivilege)
    {
        if (!empty($packageGroupPrivilege)) {
            $sql = "SELECT *
                FROM master_client_group
                WHERE group_id IN($packageGroupPrivilege)
                  AND group_status = 'ACTIVE'
                ORDER BY group_name ASC";

            $query = $this->db->query($sql);
            $groups = $query->result();

            $groupList = "";

            if (!empty($groups)) {
                foreach ($groups as $obj) {
                    if (!empty($obj->group_menu_privilege)) {
                        $groupList .= $obj->group_menu_privilege . ",";
                    }
                }
            }

            return remove_duplicate_string(",", $groupList);
        } else {
            return null;
        }
    }



    // function fetchTopMenu($listMenuId)
    // {
    //     $sql = "SELECT *
    //             FROM master_menu
    //             WHERE menu_no IN($listMenuId)
    //               AND menu_parent_id = 0
    //               AND menu_status = 'ACTIVE'
    //             ORDER BY menu_sort_no ASC";

    //     $query = $this->db->query($sql);
    //     return $query->result();
    // }



    // function getChildMenu($parentId, $menuPrivilege = null)
    // {
    //     $menu = $this->fetchChildMenu($parentId);

    //     if (!empty($menu)) {
    //         $i = 0;

    //         foreach ($menu as $obj) {
    //             if (!empty($menuPrivilege)) {
    //                 $arr_privilege = explode(",", $menuPrivilege);

    //                 if ($obj->menu_has_child == 'YES') {
    //                     $detail_menu = $this->getChildMenu($obj->menu_no, $menuPrivilege);

    //                     if (!empty($detail_menu)) {
    //                         $filtered_menu = array();

    //                         foreach ($detail_menu as $objx) {
    //                             if ($objx->menu_has_child == 'NO') {
    //                                 if (in_array($objx->menu_no, $arr_privilege)) {
    //                                     $filtered_menu[] = $objx;
    //                                 } else {
    //                                     if (count($detail_menu) == 1) {
    //                                         unset($menu[$i]);
    //                                     }
    //                                 }
    //                             } else {
    //                                 $filtered_menu[] = $objx;
    //                             }
    //                         }

    //                         $obj->children = $filtered_menu;
    //                     }
    //                 }
    //             } else {
    //                 if ($obj->menu_has_child == 'YES') {
    //                     $detail_menu = $this->getChildMenu($obj->menu_no);

    //                     if (!empty($detail_menu)) {
    //                         $obj->children = $detail_menu;
    //                     }
    //                 }
    //             }

    //             $i++;
    //         }
    //     }

    //     return $menu;
    // }

    // function fetchChildMenu($parentId)
    // {
    //     $sql = "SELECT *
    //             FROM master_menu
    //             WHERE menu_parent_id IN($parentId)
    //               AND menu_parent_id NOT IN(0)
    //               AND menu_status = 'ACTIVE'
    //             ORDER BY menu_sort_no ASC";

    //     $query = $this->db->query($sql);
    //     return $query->result();
    // }

    function save_data($data)
    {
        $error = array();
        $email = addSingleQuotes($data['email']);
        $phone = addSingleQuotes($data['phone']);
        $name = addSingleQuotes($data['name']);
        $address = addSingleQuotes($data['address']);
        $pic = addSingleQuotes($data['pic']);
        $package = addSingleQuotes($data['package']);
        $password = addSingleQuotes($this->encryption->encrypt($data['password']));

        $this->db->trans_begin();

        $sqlInsertClient = "INSERT INTO master_client(client_name, client_address, client_pic_name, client_pic_phone, client_package_privilege, client_status, user_create) VALUES($name, $address, $pic, $phone, $package, 'ACTIVE', 'ADMINISTRATOR')";
        $this->db->query($sqlInsertClient);
        $lastInsertId = $this->db->insert_id();

        if ($this->db->trans_status() === FALSE) {
            $error['sql'] = $sqlInsertClient;
            $this->db->trans_rollback();
        }

        $sqlInsertUser = "INSERT INTO master_user_app(user_name, user_title, user_email, user_phone, user_address, user_password, user_type, user_status, user_expired, client_no) VALUES($name, 'ADMINISTRATOR', $email, $phone, $address, $password, 'ADMIN', 'ACTIVE', TIMESTAMPADD(DAY, 14, current_timestamp()), $lastInsertId)";
        $this->db->query($sqlInsertUser);

        if ($this->db->trans_status() === FALSE) {
            $error['sql'] = $sqlInsertUser;
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }

        return format_output($this->db->affected_rows(), $error);
    }

    function fetchPackage()
    {
        $sql = "SELECT *
                FROM master_client_package
                WHERE package_type = 'BASIC'
                ORDER BY package_id ASC";

        $query = $this->db->query($sql);
        return format_output_select($query->num_rows(), $query->result());
    }

    function fetchPackageDetail()
    {
        $sql = "SELECT *
                FROM master_client_package
                WHERE package_type = 'ADD ON'
                ORDER BY package_id ASC";

        $query = $this->db->query($sql);
        return format_output_select($query->num_rows(), $query->result());
    }

    public function format_output_select($count, $data)
    {
        $output = array();
        $output['total_records'] = $count;
        $output['data'] = $data;

        return $output;
    }
}
