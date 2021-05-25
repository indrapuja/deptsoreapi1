<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Description of Master_model
 *
 * @author Achmad Hafizh
 */
class Master_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->mysql = $this->load->database('default', TRUE);
        $this->informix = $this->load->database('informix', TRUE);
    }

    function create_conversion_category($data)
    {
        $error = array();
        $affected_rows = 0;

        $this->mysql->trans_start();

        foreach ($data as $row) {
            $sql_insert_ignore = "";
            
            $format = array();
            $format['class_no'] = $row['class_no'];
            $format['category_vendor'] = $row['category_vendor'];
            $format['category_metro'] = $row['category_metro'];

            $sql_insert_ignore = $this->mysql->insert_ignore_string("master_conversion_category", $format);

            // Transaction 1 -> INSERT master_conversion_category
            $this->mysql->query($sql_insert_ignore);

            $affected_rows += $this->mysql->affected_rows();
        }

        $this->mysql->trans_complete();

        if ($this->mysql->trans_status() === FALSE) {
            $error = $this->mysql->error();
            $error['sql'] = $sql_insert_ignore;
        }

        return $this->commonutil->format_output($affected_rows, $error, $data);
    }

    function update_conversion_category($data) {
        $execute = $this->mysql->replace('master_conversion_category', $data);
        return $this->commonutil->format_output($this->mysql->affected_rows(), $this->mysql->error(), $data);
    }

    function delete_conversion_category($data) {
        $execute = $this->mysql->delete('master_conversion_category', $data);
        return $this->commonutil->format_output($this->mysql->affected_rows(), $this->mysql->error(), $data);
    }

    function create_conversion_size($data)
    {
        $error = array();
        $affected_rows = 0;

        $this->mysql->trans_start();

        foreach ($data as $row) {
            $sql_insert_ignore = "";
            
            $format = array();
            $format['class_no'] = $row['class_no'];
            $format['size_vendor'] = $row['size_vendor'];
            $format['size_metro'] = $row['size_metro'];

            $sql_insert_ignore = $this->mysql->insert_ignore_string("master_conversion_size", $format);

            // Transaction 1 -> INSERT master_conversion_size
            $this->mysql->query($sql_insert_ignore);

            $affected_rows += $this->mysql->affected_rows();
        }

        $this->mysql->trans_complete();

        if ($this->mysql->trans_status() === FALSE) {
            $error = $this->mysql->error();
            $error['sql'] = $sql_insert_ignore;
        }

        return $this->commonutil->format_output($affected_rows, $error, $data);
    }

    function update_conversion_size($data) {
        $execute = $this->mysql->replace('master_conversion_size', $data);
        return $this->commonutil->format_output($this->mysql->affected_rows(), $this->mysql->error(), $data);
    }

    function delete_conversion_size($data) {
        $execute = $this->mysql->delete('master_conversion_size', $data);
        return $this->commonutil->format_output($this->mysql->affected_rows(), $this->mysql->error(), $data);
    }

    function create_conversion_color($data)
    {
        $error = array();
        $affected_rows = 0;

        $this->mysql->trans_start();

        foreach ($data as $row) {
            $sql_insert_ignore = "";
            
            $format = array();
            $format['class_no'] = $row['class_no'];
            $format['color_vendor'] = $row['color_vendor'];
            $format['color_metro'] = $row['color_metro'];

            $sql_insert_ignore = $this->mysql->insert_ignore_string("master_conversion_color", $format);

            // Transaction 1 -> INSERT master_conversion_color
            $this->mysql->query($sql_insert_ignore);

            $affected_rows += $this->mysql->affected_rows();
        }

        $this->mysql->trans_complete();

        if ($this->mysql->trans_status() === FALSE) {
            $error = $this->mysql->error();
            $error['sql'] = $sql_insert_ignore;
        }

        return $this->commonutil->format_output($affected_rows, $error, $data);
    }

    function update_conversion_color($data) {
        $execute = $this->mysql->replace('master_conversion_color', $data);
        return $this->commonutil->format_output($this->mysql->affected_rows(), $this->mysql->error(), $data);
    }

    function delete_conversion_color($data) {
        $execute = $this->mysql->delete('master_conversion_color', $data);
        return $this->commonutil->format_output($this->mysql->affected_rows(), $this->mysql->error(), $data);
    }

    function create_conversion_material($data)
    {
        $error = array();
        $affected_rows = 0;

        $this->mysql->trans_start();

        foreach ($data as $row) {
            $sql_insert_ignore = "";
            
            $format = array();
            $format['class_no'] = $row['class_no'];
            $format['material_vendor'] = $row['material_vendor'];
            $format['material_metro'] = $row['material_metro'];

            $sql_insert_ignore = $this->mysql->insert_ignore_string("master_conversion_material", $format);

            // Transaction 1 -> INSERT master_conversion_material
            $this->mysql->query($sql_insert_ignore);

            $affected_rows += $this->mysql->affected_rows();
        }

        $this->mysql->trans_complete();

        if ($this->mysql->trans_status() === FALSE) {
            $error = $this->mysql->error();
            $error['sql'] = $sql_insert_ignore;
        }

        return $this->commonutil->format_output($affected_rows, $error, $data);
    }

    function update_conversion_material($data) {
        $execute = $this->mysql->replace('master_conversion_material', $data);
        return $this->commonutil->format_output($this->mysql->affected_rows(), $this->mysql->error(), $data);
    }

    function delete_conversion_material($data) {
        $execute = $this->mysql->delete('master_conversion_material', $data);
        return $this->commonutil->format_output($this->mysql->affected_rows(), $this->mysql->error(), $data);
    }
}
