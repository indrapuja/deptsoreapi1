<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Description of Product_model
 *
 * @author Achmad Hafizh
 */
class Product_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->mysql = $this->load->database('default', TRUE);
        // $this->informix = $this->load->database('informix', TRUE);
    }

    function update_data($parameters)
    {
        $affected_rows = 0;

        $this->mysql->trans_begin();

        foreach ($parameters as $row) {
            $data = $row;
            unset($data['sku_no']);
            unset($data['image_gallery']);

            $where = array('sku_no' => $row['sku_no']);

            $sql_update = $this->mysql->update_string('master_product', $data, $where);
            $this->mysql->query($sql_update);
            $affected_rows += $this->mysql->affected_rows();

            if ($this->mysql->trans_status() === FALSE) {
                $error = $this->mysql->error();
                $error['sql'] = $sql_update;
                $this->mysql->trans_rollback();
            }

            if (!empty($row['image_gallery'])) {
                $sql_delete = "DELETE from master_image WHERE sku_no = '" . $row['sku_no'] . "'";
                $this->mysql->query($sql_delete);
                $affected_rows += $this->mysql->affected_rows();

                if ($this->mysql->trans_status() === FALSE) {
                    $error = $this->mysql->error();
                    $error['sql'] = $sql_delete;
                    $this->mysql->trans_rollback();
                }

                foreach ($row['image_gallery'] as $rowx) {
                    $insert_value['sku_no'] = $row['sku_no'];
                    $insert_value['image_name'] = $rowx['image_name'];
                    $insert_value['image_ext'] = "jpg";
                    $insert_value['image_path'] = "uploads/product/";
                    $insert_value['image_link'] = "http://images.metroindonesia.com/uploads/product/" . $rowx['image_name'];
                    $insert_value['flag'] = 1;
                    $insert_value['status'] = 1;

                    $sql_insert = $this->mysql->insert_string('master_image', $insert_value);
                    $this->mysql->query($sql_insert);
                    $affected_rows += $this->mysql->affected_rows();

                    if ($this->mysql->trans_status() === FALSE) {
                        $error = $this->mysql->error();
                        $error['sql'] = $sql_insert;
                        $this->mysql->trans_rollback();
                    }
                }
            }
        }

        if ($this->mysql->trans_status() === TRUE) {
            $error = array();
            $this->mysql->trans_commit();
        }

        return $this->commonutil->format_output($affected_rows, $error);
    }
}
