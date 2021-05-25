<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

/**
 * KAWAN Digital 2020
 *
 * @author Achmad Hafizh
 */
class Brand extends REST_Controller
{

    function __construct()
    {
        parent::__construct();
        // $this->_check_jwt();

        $this->load->model('Brand_model');
        $this->load->library('encryption');
    }

    public function read_by_client_get() {
        $id = $this->get('id');

        if(empty($id)) {
            $output['status'] = FALSE;
            $output['message'] = "No records found.";

            $this->response($output, REST_Controller::HTTP_NOT_FOUND);
        } else {
            $data = $this->Brand_model->get_brand($id);
    
            if ($data['total_records'] > 0) {
                $output['status'] = TRUE;
                $output['message'] = "Records found.";

                $this->response(array_merge($output, $data), REST_Controller::HTTP_OK);
            } else {
                $output['status'] = FALSE;
                $output['message'] = "No records found.";

                $this->response($output, REST_Controller::HTTP_NOT_FOUND);
            }
        }
    }
}
