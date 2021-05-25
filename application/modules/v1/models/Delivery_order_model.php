<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Description of Delivery_order_model
 *
 * @author Achmad Hafizh
 */
class Delivery_order_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->mysql = $this->load->database('default', TRUE);
        $this->informix = $this->load->database('informix', TRUE);
    }

    function read_data_class_from_stock()
    {
        $query = $this->informix->select("class_no")
            ->from('access_stock')
            ->where('article_group')
            ->group_by("class_no")
            ->order_by('1', 'ASC')
            ->get();

        //$query1 = 'select class_no from access_stock where article_group is null group by 1 order by 1 skip 21';
        //$result = $this->informix->query($query1);

        return $query->result();
    }

    function read_data_for_do($class_no)
    {
        $query = $this->informix->select("store_no, dept_no, class_no, TRIM(REPLACE(article_vendor, '" . '"' . "', '')) AS article_vendor, 
                                          category_no || size_no || color_no || material_no AS style, sku_promo[2,2] AS sku_promo, exp_promo, 
                                          category_no, size_no, color_no, material_no, season_code, CAST(retail AS INTEGER) AS retail, 
                                          tag_type, '' AS image, qty_current AS qty, '808080' AS nik, sku_no AS ref_no")
            ->from('access_stock')
            ->where('class_no', $this->informix->escape_str($class_no))
            ->where('article_group')
            ->order_by('1,2,3,4', 'ASC')
            ->get();

        return $query->result_array();
    }

    function read_data_for_udpate($class_no)
    {
        $query = $this->mysql->select("a.doc_no, a.store_no, a.sku_no, a.status AS status_old, '6' AS status_new, a.status_item, a.qty_last_receive, a.user_register as nik")
            ->from('trans_do a')
            ->join('master_product b', 'b.sku_no = a.sku_no', 'left')
            ->where('b.class_no', $this->mysql->escape_str($class_no))
            ->get();

        return $query->result_array();
    }

    function create_data($data)
    {
        $sku = array();
        $error = array();
        $affected_rows = 0;

        $this->mysql->trans_start();

        foreach ($data as $row) {
            $sql_insert_product = "";

            $class_no = $row['class_no'];
            $article_vendor = $row['article_vendor'];
            $style = $row['style'];
            $sku_promo = $row['sku_promo'];
            $retail = $row['retail'];
            $unique_id = $this->generate_unique_id($class_no, $article_vendor, $style, $sku_promo, $retail);
            $sku_no = $class_no . $unique_id . $style . $sku_promo . $retail;
            $article_no = $class_no . $style . $sku_promo . $retail;
            $res_field = $unique_id . $style . $sku_promo;

            $format = array();
            $format['dept_no'] = $row['dept_no'];
            $format['class_no'] = $class_no;
            $format['sku_no'] = $sku_no;

            if (!empty($row['ref_no'])) {
                $format['ref_no'] = $row['ref_no'];
            }

            $format['article_vendor'] = $article_vendor;
            $format['article_no'] = $article_no;
            $format['res_field'] = $res_field;
            $format['style'] = $style;
            $format['category_no'] = $row['category_no'];
            $format['size_no'] = $row['size_no'];
            $format['color_no'] = $row['color_no'];
            $format['material_no'] = $row['material_no'];
            $format['unique_id'] = $unique_id;
            $format['sku_promo'] = $sku_promo;

            if (!empty($row['exp_promo']) and $row['sku_promo'] != "0") {
                $format['exp_promo'] = date('Y-m-d', strtotime(str_replace('/', '-', str_replace("'", "", $row['exp_promo']))));
            }

            $format['season_code'] = date('ym');
            $format['retail'] = $retail;
            $format['tag_type'] = $row['tag_type'];

            if (!empty($row['image'])) {
                if ($row['image'] == "default") {
                    $format['image'] = $sku_no . ".jpg";
                } else {
                    $format['image'] = $row['image'];
                }
            }

            $format['time_create'] = date('Y-m-d H:i:s');

            $sql_insert_product = $this->mysql->insert_ignore_string("master_product", $format);

            // Transaction 1 -> INSERT master_product
            $this->mysql->query($sql_insert_product);

            $sql_insert_do = "";
            $format_doc = "DO" . $class_no . date('Ymd');
            $do_id = $this->generate_do_id($format_doc);
            $doc_no = $format_doc . $do_id;
            $store_no = $row['store_no'];

            // INSERT trans_do
            $format_insert = array();
            $format_insert['doc_no'] = $doc_no;
            $format_insert['store_no'] = $store_no;
            $format_insert['sku_no'] = $sku_no;

            if (!empty($row['note'])) {
                $format_insert['note'] = $row['note'];
            }

            $format_insert['start_date'] = date('Y-m-d', strtotime(date('Ymd')));
            $format_insert['end_date'] = date('Y-m-d', strtotime('60 day', strtotime(date('Ymd'))));
            $format_insert['status'] = 1;
            $format_insert['user_register'] = $row['nik'];
            $format_insert['time_register'] = date('Y-m-d H:i:s');
            $format_insert['status_item'] = 0;
            $format_insert['qty_store'] = $row['qty'];

            $format_update['qty_store'] = "qty_store + " . $row['qty'];

            $sql_insert_do = $this->mysql->insert_on_duplicate_update_string("trans_do", $format_insert, $format_update, true, false);

            // Transaction 2 -> INSERT trans_do
            $this->mysql->query($sql_insert_do);

            $sku[] = (object) array('sku_no' => $sku_no);
            $affected_rows += $this->mysql->affected_rows();
        }

        $this->mysql->trans_complete();

        if ($this->mysql->trans_status() === FALSE) {
            $sku = array();
            $error = $this->mysql->error();
            $error['sql_product'] = $sql_insert_product;
            $error['sql_do'] = $sql_insert_do;
        }

        return $this->commonutil->format_output($affected_rows, $error, $sku);
    }

    function generate_unique_id($class_no, $article_vendor, $style, $sku_promo, $retail)
    {
        // get last sku with reference from class_no, article_vendor, style, sku_promo dan retail 
        $last_id1 = $this->read_last_sku_id($class_no, $article_vendor, $style, $sku_promo, $retail);

        if (!empty($last_id1)) {
            // sku found
            $next_id = (int) $last_id1;

            if (strlen($next_id) == 1) {
                $unique_id = "00000" . $next_id;
            } else if (strlen($next_id) == 2) {
                $unique_id = "0000" . $next_id;
            } else if (strlen($next_id) == 3) {
                $unique_id = "000" . $next_id;
            } else if (strlen($next_id) == 4) {
                $unique_id = "00" . $next_id;
            } else if (strlen($next_id) == 5) {
                $unique_id = "0" . $next_id;
            } else {
                $unique_id = $next_id;
            }
        } else {
            // sku not found
            // get last sku with reference from class_no, article_vendor, style dan sku_promo
            $last_id2 = $this->read_last_sku_id($class_no, $article_vendor, $style, $sku_promo, NULL);

            if (!empty($last_id2)) {
                // sku found
                $next_id = (int) $last_id2 + 1;

                if (strlen($next_id) == 1) {
                    $unique_id = "00000" . $next_id;
                } else if (strlen($next_id) == 2) {
                    $unique_id = "0000" . $next_id;
                } else if (strlen($next_id) == 3) {
                    $unique_id = "000" . $next_id;
                } else if (strlen($next_id) == 4) {
                    $unique_id = "00" . $next_id;
                } else if (strlen($next_id) == 5) {
                    $unique_id = "0" . $next_id;
                } else {
                    $unique_id = $next_id;
                }
            } else {
                // sku not found
                // get last sku with reference from class_no, style dan sku_promo
                $last_id3 = $this->read_last_sku_id($class_no, NULL, $style, $sku_promo, NULL);

                if (!empty($last_id3)) {
                    // sku found
                    $next_id = (int) $last_id3 + 1;

                    if (strlen($next_id) == 1) {
                        $unique_id = "00000" . $next_id;
                    } else if (strlen($next_id) == 2) {
                        $unique_id = "0000" . $next_id;
                    } else if (strlen($next_id) == 3) {
                        $unique_id = "000" . $next_id;
                    } else if (strlen($next_id) == 4) {
                        $unique_id = "00" . $next_id;
                    } else if (strlen($next_id) == 5) {
                        $unique_id = "0" . $next_id;
                    } else {
                        $unique_id = $next_id;
                    }
                } else {
                    // sku not found
                    $unique_id = "000001";
                }
            }
        }

        return $unique_id;
    }

    function read_last_sku_id($class_no, $article_vendor = NULL, $style, $sku_promo, $retail = NULL)
    {
        if (!empty($article_vendor) and !empty($retail)) {
            $query = $this->mysql->select_max('unique_id', 'last_id')
                ->from('master_product')
                ->where('class_no', $this->mysql->escape_str($class_no))
                ->where('style', $this->mysql->escape_str($style))
                ->where('sku_promo', $this->mysql->escape_str($sku_promo))
                ->where('UPPER(article_vendor)', $this->mysql->escape_str(strtoupper($article_vendor)))
                ->where('retail', $this->mysql->escape_str(strtoupper($retail)))
                ->limit(1)
                ->get();
        } else if (!empty($article_vendor) and empty($retail)) {
            $query = $this->mysql->select_max('unique_id', 'last_id')
                ->from('master_product')
                ->where('class_no', $this->mysql->escape_str($class_no))
                ->where('style', $this->mysql->escape_str($style))
                ->where('sku_promo', $this->mysql->escape_str($sku_promo))
                ->where('UPPER(article_vendor)', $this->mysql->escape_str(strtoupper($article_vendor)))
                ->limit(1)
                ->get();
        } else if (!empty($retail) and empty($article_vendor)) {
            $query = $this->mysql->select_max('unique_id', 'last_id')
                ->from('master_product')
                ->where('class_no', $this->mysql->escape_str($class_no))
                ->where('style', $this->mysql->escape_str($style))
                ->where('sku_promo', $this->mysql->escape_str($sku_promo))
                ->where('retail', $this->mysql->escape_str(strtoupper($retail)))
                ->limit(1)
                ->get();
        } else {
            $query = $this->mysql->select_max('unique_id', 'last_id')
                ->from('master_product')
                ->where('class_no', $this->mysql->escape_str($class_no))
                ->where('style', $this->mysql->escape_str($style))
                ->where('sku_promo', $this->mysql->escape_str($sku_promo))
                ->limit(1)
                ->get();
        }

        $data = $query->row();

        return $data->last_id;
    }

    function generate_do_id($format_doc)
    {
        $query = $this->mysql->select('CAST(SUBSTRING(doc_no, 15) AS SIGNED) AS last_id')
            ->from('trans_do')
            ->where('status >', $this->mysql->escape_str(1))
            ->where('SUBSTRING(doc_no, 1, 14) = ', $this->mysql->escape_str($format_doc))
            ->order_by(1, 'DESC')
            ->limit(1)
            ->get();

        $data = $query->row();

        if (!empty($data)) {
            $increment = (int) $data->last_id + 1;
        } else {
            $increment = 1;
        }

        return $increment;
    }

    function update_data($data)
    {
        $affected_rows = 0;

        $this->mysql->trans_begin();

        foreach ($data as $row) {
            if (isset($row['status_old']) and $row['status_old'] == "5") {
                if (isset($row['qty_last_receive']) and isset($row['status_new'])) {
                    // INSERT trans_mutation, trans_product, trans_inventory & UPDATE trans_do
                    $data_mutation = array();
                    $data_mutation['tr_code'] = "11";
                    $data_mutation['tgl'] = date('Y-m-d');
                    $data_mutation['doc_no'] = $row['doc_no'];
                    $data_mutation['store_no'] = $row['store_no'];
                    $data_mutation['sku_no'] = $row['sku_no'];
                    $data_mutation['qty'] = $row['qty_last_receive'];
                    $data_mutation['user_id'] = $row['nik'];

                    $sql_insert_mutation = $this->mysql->insert_string("trans_mutation", $data_mutation);

                    // Transaction 1 -> INSERT trans_mutation
                    $this->mysql->query($sql_insert_mutation);

                    if ($this->mysql->trans_status() === FALSE) {
                        $error = $this->mysql->error();
                        $error['sql'] = $sql_insert_mutation;
                        $this->mysql->trans_rollback();
                    }

                    $data_insert_stock = array();
                    $data_insert_stock['store_no'] = $this->mysql->escape($row['store_no']);
                    $data_insert_stock['sku_no'] = $this->mysql->escape($row['sku_no']);
                    $data_insert_stock['do'] = $this->mysql->escape($row['qty_last_receive']);
                    $data_insert_stock['soh'] = "do + ro + adj_in + trf_in + gr + pc_in - sales - retur - adj_out - trf_out - pc_out";

                    $data_update_stock['do'] = "do + " . $row['qty_last_receive'];
                    $data_update_stock['soh'] = "do + ro + adj_in + trf_in + gr + pc_in - sales - retur - adj_out - trf_out - pc_out";
                    $data_update_stock['time_modified'] = $this->mysql->escape(date('Y-m-d H:i:s'));

                    $sql_insert_or_update_stock = $this->mysql->insert_on_duplicate_update_string("trans_product", $data_insert_stock, $data_update_stock, false, false);

                    // Transaction 2 -> INSERT or UPDATE trans_product
                    $this->mysql->query($sql_insert_or_update_stock);

                    if ($this->mysql->trans_status() === FALSE) {
                        $error = $this->mysql->error();
                        $error['sql'] = $sql_insert_or_update_stock;
                        $this->mysql->trans_rollback();
                    }

                    $data_insert_inventory = array();
                    $data_insert_inventory['period'] = $this->mysql->escape(date('Ym'));
                    $data_insert_inventory['store_no'] = $this->mysql->escape($row['store_no']);
                    $data_insert_inventory['sku_no'] = $this->mysql->escape($row['sku_no']);
                    $data_insert_inventory['bom'] = $this->read_last_eom($row['store_no'], $row['sku_no']);
                    $data_insert_inventory['do'] = $this->mysql->escape($row['qty_last_receive']);
                    $data_insert_inventory['eom'] = "bom + do + ro + adj_in + trf_in + gr + pc_in - sales - retur - adj_out - trf_out - pc_out";

                    $data_update_inventory['do'] = "do + " . $row['qty_last_receive'];
                    $data_update_inventory['eom'] = "bom + do + ro + adj_in + trf_in + gr + pc_in - sales - retur - adj_out - trf_out - pc_out";
                    $data_update_inventory['time_modified'] = $this->mysql->escape(date('Y-m-d H:i:s'));

                    $sql_insert_or_update_inventory = $this->mysql->insert_on_duplicate_update_string("trans_inventory", $data_insert_inventory, $data_update_inventory, false, false);

                    // Transaction 3 -> INSERT or UPDATE trans_inventory
                    $this->mysql->query($sql_insert_or_update_inventory);

                    if ($this->mysql->trans_status() === FALSE) {
                        $error = $this->mysql->error();
                        $error['sql'] = $sql_insert_or_update_inventory;
                        $this->mysql->trans_rollback();
                    }

                    $data_update_do = array();
                    $data_update_do['status'] = $this->mysql->escape($row['status_new']);
                    $data_update_do['user_approve'] = $this->mysql->escape($row['nik']);
                    $data_update_do['time_approve'] = $this->mysql->escape(date('Y-m-d H:i:s'));
                    $data_update_do['qty_receive'] = "qty_receive + " . $row['qty_last_receive'];

                    $where_update_do = "doc_no = " . $this->mysql->escape($row['doc_no']) . " and store_no = " . $this->mysql->escape($row['store_no']) . " and sku_no = " . $this->mysql->escape($row['sku_no']);

                    $sql_update_do = $this->mysql->update_string("trans_do", $data_update_do, $where_update_do, false);
                    
                    // Transaction 4 -> UPDATE trans_do
                    $this->mysql->query($sql_update_do);

                    if ($this->mysql->trans_status() === FALSE) {
                        $error = $this->mysql->error();
                        $error['sql'] = $sql_update_do;
                        $this->mysql->trans_rollback();
                    }

                    $data_update_do2 = array();

                    if (!empty($row['status_new'])) {
                        $data_update_do2['status'] = $this->mysql->escape($row['status_new']);
                    }

                    if (!empty($row['nik'])) {
                        $data_update_do2['user_last_receive'] = $this->mysql->escape($row['nik']);
                        $data_update_do2['date_last_receive'] = $this->mysql->escape(date('Y-m-d H:i:s'));
                    }

                    $where_update_do2 = "doc_no = " . $this->mysql->escape($row['doc_no']) . " and store_no = " . $this->mysql->escape($row['store_no']);
                    
                    $sql_update_do2 = $this->mysql->update_string("trans_do", $data_update_do2, $where_update_do2, false);
                    
                    // Transaction 4 -> UPDATE trans_do
                    $this->mysql->query($sql_update_do2);
                    $affected_rows += $this->mysql->affected_rows();

                    if ($this->mysql->trans_status() === FALSE) {
                        $error = $this->mysql->error();
                        $error['sql'] = $sql_update_do2;
                        $this->mysql->trans_rollback();
                    }
                } else {
                    return $this->commonutil->format_output(0, array('error_message' => "Required parameters qty_last_receive and status_new!"));
                }
            } else {
                // UPDATE trans_do & master_product
                $data_update_do = array();
                $data_update_product = array();

                if (!empty($row['tag_type'])) {
                    $data_update_product['tag_type'] = $this->mysql->escape($row['tag_type']);

                    $where_update_product = "sku_no = " . $this->mysql->escape($row['sku_no']);
                    $sql_update_product = $this->mysql->update_string("master_product", $data_update_product, $where_update_product, false);

                    // Transaction 1 -> UPDATE product
                    $this->mysql->query($sql_update_product);
                    $affected_rows += $this->mysql->affected_rows();
                }

                if (!empty($row['status_new'])) {
                    $data_update_do['status'] = $this->mysql->escape($row['status_new']);
                }

                if (!empty($row['status_item'])) {
                    $data_update_do['status_item'] = $this->mysql->escape($row['status_item']);
                }

                if (!empty($row['qty_store'])) {
                    $data_update_do['qty_store'] = $this->mysql->escape($row['qty_store']);
                }

                if (!empty($row['qty_last_receive'])) {
                    $data_update_do['qty_last_receive'] = $this->mysql->escape($row['qty_last_receive']);
                }

                if (!empty($row['qty_last_delivery'])) {
                    $data_update_do['qty_last_delivery'] = $this->mysql->escape($row['qty_last_delivery']);
                }

                if (!empty($row['store_no'])) {
                    $where_store = " and store_no = " . $this->mysql->escape($row['store_no']);
                } else {
                    $where_store = "";
                }

                if (!empty($row['sku_no'])) {
                    $where_sku = " and sku_no = " . $this->mysql->escape($row['sku_no']);
                } else {
                    $where_sku = "";
                }

                if (!empty($row['status_old'])) {
                    $where_status = " and status = " . $this->mysql->escape($row['status_old']);
                } else {
                    $where_status = "";
                }

                $where_update_do = "doc_no = " . $this->mysql->escape($row['doc_no']) . $where_sku . $where_store . $where_status;

                $sql_update_do = $this->mysql->update_string("trans_do", $data_update_do, $where_update_do, false);
                
                // Transaction 2 -> UPDATE trans_do
                $this->mysql->query($sql_update_do);
                $affected_rows += $this->mysql->affected_rows();

                if ($this->mysql->trans_status() === FALSE) {
                    $error = $this->mysql->error();
                    $error['sql'] = $sql_update_do;
                    $this->mysql->trans_rollback();
                }

                if (!empty($row['status_new']) and ($row['status_new'] == "3" or $row['status_new'] == "4" or $row['status_new'] == "5")) {
                    $data_update_do = array();

                    if (!empty($row['status_new'])) {
                        $data_update_do['status'] = $this->mysql->escape($row['status_new']);
                    }

                    if (!empty($row['nik'])) {
                        if (!empty($row['status_new']) and $row['status_new'] == "3") {
                            $data_update_do['user_approve'] = $this->mysql->escape($row['nik']);
                            $data_update_do['time_approve'] = $this->mysql->escape(date('Y-m-d H:i:s'));
                        } else if (!empty($row['status_new']) and $row['status_new'] == "4") {
                            $data_update_do['user_last_delivery'] = $this->mysql->escape($row['nik']);
                            $data_update_do['date_last_delivery'] = $this->mysql->escape(date('Y-m-d H:i:s'));
                        } else if (!empty($row['status_new']) and $row['status_new'] == "5") {
                            $data_update_do['user_last_receive'] = $this->mysql->escape($row['nik']);
                            $data_update_do['date_last_receive'] = $this->mysql->escape(date('Y-m-d H:i:s'));
                        }
                    }

                    if ($row['status_new'] == "3") {
                        if ($row['status_old'] == "2") {
                            $where_update_do = "doc_no = " . $this->mysql->escape($row['doc_no']);
                        } else if ($row['status_old'] == "4") {
                            $where_update_do = "doc_no = " . $this->mysql->escape($row['doc_no']) . " and store_no = " . $this->mysql->escape($row['store_no']);
                        }
                    } else {
                        $where_update_do = "doc_no = " . $this->mysql->escape($row['doc_no']) . " and store_no = " . $this->mysql->escape($row['store_no']);
                    }

                    $sql_update_do = $this->mysql->update_string("trans_do", $data_update_do, $where_update_do, false);

                    // Transaction 3 -> UPDATE trans_do
                    $this->mysql->query($sql_update_do);
                    $affected_rows += $this->mysql->affected_rows();

                    if ($this->mysql->trans_status() === FALSE) {
                        $error = $this->mysql->error();
                        $error['sql'] = $sql_update_do;
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

    function delete_data($data)
    {
        $this->mysql->trans_start();

        if (!empty($data['doc_no']) and !empty($data['store_no']) and !empty($data['sku_no'])) {
            $this->mysql->query('DELETE FROM trans_do WHERE doc_no = ' . $this->mysql->escape($data['doc_no']) . ' AND store_no = ' . $this->mysql->escape($data['store_no']) . ' AND sku_no = ' . $this->mysql->escape($data['sku_no']) . ' AND status = 1');
        } else if (!empty($data['doc_no']) and !empty($data['store_no'])) {
            $this->mysql->query('DELETE FROM trans_do WHERE doc_no = ' . $this->mysql->escape($data['doc_no']) . ' AND store_no = ' . $this->mysql->escape($data['store_no']) . ' AND status = 1');
        } else {
            $this->mysql->query('DELETE FROM trans_do WHERE doc_no = ' . $this->mysql->escape($data['doc_no']) . ' AND status = 1');
        }

        $affected_rows = $this->mysql->affected_rows();

        $this->mysql->trans_complete();

        if ($this->mysql->trans_status() === FALSE) {
            $error = $this->mysql->error();
            $error['sql'] = $this->mysql->last_query();
        } else {
            $error = array();
        }

        return $this->commonutil->format_output($affected_rows, $error);
    }

    function read_last_eom($store_no, $sku_no)
    {
        $query = $this->mysql->select('period', 'eom')
            ->from('trans_inventory')
            ->where('store_no', $this->mysql->escape_str($store_no))
            ->where('sku_no', $this->mysql->escape_str($sku_no))
            ->order_by('period', 'DESC')
            ->limit(1, 1)
            ->get();

        $data = $query->row();

        if (!empty($data->eom)) {
            return $data->eom;
        } else {
            return 0;
        }
    }

    // function is_product_exist($sku_no)
    // {
    //     $query = $this->informix->select("sku_no")
    //         ->from('master_product')
    //         ->where('sku_no', $this->informix->escape_str($sku_no))
    //         ->limit(1)
    //         ->get();

    //     return empty($query->row()->sku_no);
    // }

    // function is_document_exist($doc_no, $store_no, $sku_no)
    // {
    //     $query = $this->informix->select("doc_no, store_no, sku_no, qty_store")
    //         ->from('trans_do')
    //         ->where('doc_no', $this->informix->escape_str($doc_no))
    //         ->where('store_no', $this->informix->escape_str($store_no))
    //         ->where('sku_no', $this->informix->escape_str($sku_no))
    //         ->limit(1)
    //         ->get();

    //     return $query->row();
    // }

    // function create_data_informix($data)
    // {
    //     $error = array();
    //     $affected_rows = 0;

    //     $this->informix->trans_start();

    //     foreach ($data as $row) {
    //         $sql_insert_product = "";

    //         $class_no = $row['class_no'];
    //         $article_vendor = $row['article_vendor'];
    //         $style = $row['style'];
    //         $sku_promo = $row['sku_promo'];
    //         $retail = $row['retail'];
    //         $unique_id = $this->generate_unique_id_informix($class_no, $article_vendor, $style, $sku_promo, $retail);
    //         $sku_no = $class_no . $unique_id . $style . $sku_promo . $retail;
    //         $article_no = $class_no . $style . $sku_promo . $retail;
    //         $res_field = $unique_id . $style . $sku_promo;

    //         $is_product_exist = $this->is_product_exist($sku_no);

    //         if ($is_product_exist) {
    //             $format = array();
    //             $format['dept_no'] = $row['dept_no'];
    //             $format['class_no'] = $class_no;
    //             $format['sku_no'] = $sku_no;

    //             if (!empty($row['ref_no'])) {
    //                 $format['ref_no'] = $row['ref_no'];
    //             }

    //             $format['article_vendor'] = $article_vendor;
    //             $format['article_no'] = $article_no;
    //             $format['res_field'] = $res_field;
    //             $format['style'] = $style;
    //             $format['category_no'] = $row['category_no'];
    //             $format['size_no'] = $row['size_no'];
    //             $format['color_no'] = $row['color_no'];
    //             $format['material_no'] = $row['material_no'];
    //             $format['unique_id'] = $unique_id;
    //             $format['sku_promo'] = $sku_promo;

    //             if (!empty($row['exp_promo']) and $row['sku_promo'] != "0") {
    //                 $format['exp_promo'] = date('Y-m-d', strtotime(str_replace('/', '-', str_replace("'", "", $row['exp_promo']))));
    //             }

    //             $format['season_code'] = date('ym');
    //             $format['retail'] = $retail;
    //             $format['tag_type'] = $row['tag_type'];

    //             if (!empty($row['image'])) {
    //                 $format['image'] = $row['image'];
    //             }

    //             $format['time_create'] = date('Y-m-d H:i:s');

    //             $sql_insert_product = $this->informix->insert_string("master_product", $format);

    //             // Transaction 1 -> INSERT master_product
    //             $this->informix->query($sql_insert_product);
    //         }

    //         $sql_insert_or_update_do = "";
    //         $format_doc = "DO" . $class_no . date('Ymd');
    //         $do_id = $this->generate_do_id_informix($format_doc);
    //         $doc_no = $format_doc . $do_id;
    //         $store_no = $row['store_no'];

    //         $is_document_exist = $this->is_document_exist($doc_no, $store_no, $sku_no);

    //         if (!empty($is_document_exist->doc_no)) {
    //             // UPDATE trans_do
    //             $format_update['qty_store'] = (int) $is_document_exist->qty_store + (int) $row['qty'];
    //             $where_update = "doc_no = '$doc_no' and store_no = '$store_no' AND sku_no = '$sku_no'";

    //             $sql_insert_or_update_do = $this->informix->update_string("trans_do", $format_update, $where_update);
    //         } else {
    //             // INSERT trans_do
    //             $format_insert = array();
    //             $format_insert['doc_no'] = $doc_no;
    //             $format_insert['store_no'] = $store_no;
    //             $format_insert['sku_no'] = $sku_no;
    //             $format_insert['start_date'] = date('d-m-Y', strtotime(date('Ymd')));
    //             $format_insert['end_date'] = date('d-m-Y', strtotime('60 day', strtotime(date('Ymd'))));
    //             $format_insert['status'] = 1;
    //             $format_insert['user_register'] = $row['nik'];
    //             $format_insert['time_register'] = date('Y-m-d H:i:s');
    //             $format_insert['status_item'] = 0;
    //             $format_insert['qty_store'] = $row['qty'];

    //             $sql_insert_or_update_do = $this->informix->insert_string("trans_do", $format_insert);
    //         }

    //         // Transaction 2 -> INSERT trans_do
    //         $this->informix->query($sql_insert_or_update_do);

    //         $affected_rows += $this->informix->affected_rows();
    //     }

    //     $this->informix->trans_complete();

    //     if ($this->informix->trans_status() === FALSE) {
    //         $error = $this->informix->error();
    //         $error['sql_product'] = $sql_insert_product;
    //         // $error['sql_do'] = $sql_insert_or_update_do;
    //     }

    //     return $this->commonutil->format_output($affected_rows, $error);
    // }

    // function generate_unique_id_informix($class_no, $article_vendor, $style, $sku_promo, $retail)
    // {
    //     // get last sku with reference from class_no, article_vendor, style, sku_promo dan retail 
    //     $last_id1 = $this->read_last_sku_id_informix($class_no, $article_vendor, $style, $sku_promo, $retail);

    //     if (!empty($last_id1)) {
    //         // sku found
    //         $next_id = (int) $last_id1;

    //         if (strlen($next_id) == 1) {
    //             $unique_id = "00000" . $next_id;
    //         } else if (strlen($next_id) == 2) {
    //             $unique_id = "0000" . $next_id;
    //         } else if (strlen($next_id) == 3) {
    //             $unique_id = "000" . $next_id;
    //         } else if (strlen($next_id) == 4) {
    //             $unique_id = "00" . $next_id;
    //         } else if (strlen($next_id) == 5) {
    //             $unique_id = "0" . $next_id;
    //         } else {
    //             $unique_id = $next_id;
    //         }
    //     } else {
    //         // sku not found
    //         // get last sku with reference from class_no, article_vendor, style dan sku_promo
    //         $last_id2 = $this->read_last_sku_id_informix($class_no, $article_vendor, $style, $sku_promo, NULL);

    //         if (!empty($last_id2)) {
    //             // sku found
    //             $next_id = (int) $last_id2 + 1;

    //             if (strlen($next_id) == 1) {
    //                 $unique_id = "00000" . $next_id;
    //             } else if (strlen($next_id) == 2) {
    //                 $unique_id = "0000" . $next_id;
    //             } else if (strlen($next_id) == 3) {
    //                 $unique_id = "000" . $next_id;
    //             } else if (strlen($next_id) == 4) {
    //                 $unique_id = "00" . $next_id;
    //             } else if (strlen($next_id) == 5) {
    //                 $unique_id = "0" . $next_id;
    //             } else {
    //                 $unique_id = $next_id;
    //             }
    //         } else {
    //             // sku not found
    //             // get last sku with reference from class_no, style dan sku_promo
    //             $last_id3 = $this->read_last_sku_id_informix($class_no, NULL, $style, $sku_promo, NULL);

    //             if (!empty($last_id3)) {
    //                 // sku found
    //                 $next_id = (int) $last_id3 + 1;

    //                 if (strlen($next_id) == 1) {
    //                     $unique_id = "00000" . $next_id;
    //                 } else if (strlen($next_id) == 2) {
    //                     $unique_id = "0000" . $next_id;
    //                 } else if (strlen($next_id) == 3) {
    //                     $unique_id = "000" . $next_id;
    //                 } else if (strlen($next_id) == 4) {
    //                     $unique_id = "00" . $next_id;
    //                 } else if (strlen($next_id) == 5) {
    //                     $unique_id = "0" . $next_id;
    //                 } else {
    //                     $unique_id = $next_id;
    //                 }
    //             } else {
    //                 // sku not found
    //                 $unique_id = "000001";
    //             }
    //         }
    //     }

    //     return $unique_id;
    // }

    // function read_last_sku_id_informix($class_no, $article_vendor = NULL, $style, $sku_promo, $retail = NULL)
    // {
    //     if (!empty($article_vendor) and !empty($retail)) {
    //         $query = $this->informix->select_max('unique_id', 'last_id')
    //             ->from('master_product')
    //             ->where('class_no', $this->informix->escape_str($class_no))
    //             ->where('style', $this->informix->escape_str($style))
    //             ->where('sku_promo', $this->informix->escape_str($sku_promo))
    //             ->where('UPPER(article_vendor)', $this->informix->escape_str(strtoupper($article_vendor)))
    //             ->where('retail', $this->informix->escape_str(strtoupper($retail)))
    //             ->limit(1)
    //             ->get();
    //     } else if (!empty($article_vendor) and empty($retail)) {
    //         $query = $this->informix->select_max('unique_id', 'last_id')
    //             ->from('master_product')
    //             ->where('class_no', $this->informix->escape_str($class_no))
    //             ->where('style', $this->informix->escape_str($style))
    //             ->where('sku_promo', $this->informix->escape_str($sku_promo))
    //             ->where('UPPER(article_vendor)', $this->informix->escape_str(strtoupper($article_vendor)))
    //             ->limit(1)
    //             ->get();
    //     } else if (!empty($retail) and empty($article_vendor)) {
    //         $query = $this->informix->select_max('unique_id', 'last_id')
    //             ->from('master_product')
    //             ->where('class_no', $this->informix->escape_str($class_no))
    //             ->where('style', $this->informix->escape_str($style))
    //             ->where('sku_promo', $this->informix->escape_str($sku_promo))
    //             ->where('retail', $this->informix->escape_str(strtoupper($retail)))
    //             ->limit(1)
    //             ->get();
    //     } else {
    //         $query = $this->informix->select_max('unique_id', 'last_id')
    //             ->from('master_product')
    //             ->where('class_no', $this->informix->escape_str($class_no))
    //             ->where('style', $this->informix->escape_str($style))
    //             ->where('sku_promo', $this->informix->escape_str($sku_promo))
    //             ->limit(1)
    //             ->get();
    //     }

    //     $data = $query->row();

    //     return $data->last_id;
    // }

    // function generate_do_id_informix($format_doc)
    // {
    //     $query = $this->mysql->select('CAST(SUBSTR(doc_no, 15) AS INT) AS last_id')
    //         ->from('trans_do')
    //         ->where('status >', $this->mysql->escape_str(1))
    //         ->where('SUBSTR(doc_no, 1, 14) = ', $this->mysql->escape_str($format_doc))
    //         ->order_by(1, 'DESC')
    //         ->limit(1)
    //         ->get();

    //     $data = $query->row();

    //     if (!empty($data)) {
    //         $increment = (int) $data->last_id + 1;
    //     } else {
    //         $increment = 1;
    //     }

    //     return $increment;
    // }
}
