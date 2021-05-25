<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

/**
 * Description of Image
 *
 * @author Achmad Hafizh
 */
class Image extends REST_Controller
{

    function __construct()
    {
        parent::__construct();

        //$this->_check_jwt();
        $this->load->model('image_model');
    }

    public function create_post()
    {
        $parameters = array(
            'image_string' => $this->post('image_string'),
            'image_name' => $this->post('image_name') 
        );

        $this->form_validation->set_data($parameters);
        $this->form_validation->set_rules('image_string', 'image_string', 'required');
        //$this->form_validation->set_rules('image_name', 'image_name', 'required');

        if ($this->form_validation->run() == TRUE) {
            $success_decode = array();
            $failed_decode = array();

            if (is_array($parameters['image_name'])) {
                foreach ($parameters['image_name'] as $row) {
                    $output_path = "uploads/product/" . $row;

                    $convert = $this->image_model->convert_base64_to_image($parameters['image_string'], $output_path);

                    if (!empty($convert)) {
                        $success_decode[] = $row;
                    } else {
                        $failed_decode[] = $row;
                    }
                }

                if (count($parameters['image_name']) == count($success_decode)) {
                    $this->response(array("error" => false, "message" => "Success", "success_data" => $success_decode, "failed_data" => $failed_decode), REST_Controller::HTTP_OK);
                } else {
                    $this->response(array("error" => true, "message" => "Failed", "success_data" => $success_decode, "failed_data" => $failed_decode), REST_Controller::HTTP_OK);
                }
            } else {
                $output_path = "uploads/product/" . $parameters['image_name'];
                $convert = $this->image_model->convert_base64_to_image($parameters['image_string'], $output_path);

                if (!empty($convert)) {
                    $this->response(array("error" => false, "message" => "Success", "data" => $parameters['image_name']), REST_Controller::HTTP_OK);
                } else {
                    $this->response(array("error" => true, "message" => "Failed"), REST_Controller::HTTP_OK);
                }
            }
        } else {
            $output['error'] = true;
            $output['message'] = array(
                "error_msg" => "Required parameters!",
                "error_details" => $this->form_validation->error_array()
            );

            $this->response($output, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function create_bulk_post()
    {
        $parameters = $this->post();
        
        $this->form_validation->set_data($parameters[0]);
        $this->form_validation->set_rules('image_string', 'image_string', 'required');
        $this->form_validation->set_rules('image_name', 'image_name', 'required');

        if ($this->form_validation->run() == TRUE) {
            $success_decode = array();
            $failed_decode = array();

            foreach ($parameters as $row) {
                $output_path = "uploads/product/" . $row['image_name'];
                $convert = $this->image_model->convert_base64_to_image($row['image_string'], $output_path);

                if (!empty($convert)) {
                    $success_decode[] = $row;
                } else {
                    $failed_decode[] = $row;
                }
            } 

            if (count($parameters) == count($success_decode)) {
                $this->response(array("error" => false, "message" => "Success", "success_data" => $success_decode, "failed_data" => $failed_decode), REST_Controller::HTTP_OK);
            } else {
                foreach ($parameters as $row) {
                    $output_path = "uploads/product/" . $row['image_name'];
                    unlink($output_path);
                }
                $this->response(array("error" => true, "message" => "Failed", "success_data" => $success_decode, "failed_data" => $failed_decode), REST_Controller::HTTP_OK);
            }
        } else {
            $output['error'] = true;
            $output['message'] = array(
                "error_msg" => "Required parameters!",
                "error_details" => $this->form_validation->error_array()
            );

            $this->response($output, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function create_employee_post()
    {
        $parameters = array(
            'image_string' => $this->post('image_string'),
            'image_name' => $this->post('image_name')
        );

        $this->form_validation->set_data($parameters);
        $this->form_validation->set_rules('image_string', 'image_string', 'required');
        //$this->form_validation->set_rules('image_name', 'image_name', 'required');

        if ($this->form_validation->run() == TRUE) {
            $success_decode = array();
            $failed_decode = array();

            if (is_array($parameters['image_name'])) {
                foreach ($parameters['image_name'] as $row) {
                    $output_path = "uploads/images_employee/" . $row;

                    $convert = $this->image_model->convert_base64_to_image($parameters['image_string'], $output_path);

                    if (!empty($convert)) {
                        $success_decode[] = $row;
                    } else {
                        $failed_decode[] = $row;
                    }
                }

                if (count($parameters['image_name']) == count($success_decode)) {
                    $this->response(array("status" => true, "message" => "Success", "success_data" => $success_decode, "failed_data" => $failed_decode), REST_Controller::HTTP_OK);
                } else {
                    $this->response(array("status" => false, "message" => "Failed", "success_data" => $success_decode, "failed_data" => $failed_decode), REST_Controller::HTTP_OK);
                }
            } else {
                $output_path = "uploads/images_employee/" . $parameters['image_name'];
                $convert = $this->image_model->convert_base64_to_image($parameters['image_string'], $output_path);

                if (!empty($convert)) {
                    $this->response(array("status" => true, "message" => "Success", "data" => $parameters['image_name']), REST_Controller::HTTP_OK);
                } else {
                    $this->response(array("status" => false, "message" => "Failed"), REST_Controller::HTTP_OK);
                }
            }
        } else {
            $output['status'] = FALSE;
            $output['message'] = array(
                "error_msg" => "Required parameters!",
                "error_details" => $this->form_validation->error_array()
            );

            $this->response($output, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function delete_post()
    {
        $parameters = array(
            'image_name' => $this->post('image_name')
        );

        //        $this->form_validation->set_data($parameters);
        //        $this->form_validation->set_rules('image_name', 'image_name', 'required');
        //
        //        if ($this->form_validation->run() == TRUE) {
        $success_delete = array();
        $failed_delete = array();

        if (is_array($parameters['image_name'])) {
            foreach ($parameters['image_name'] as $row) {
                $output_path = "uploads/product/" . $row;

                if (file_exists($output_path)) {
                    if (unlink($output_path)) {
                        $success_delete[] = $row;
                    } else {
                        $failed_delete[] = $row;
                    }
                } else {
                    $failed_delete[] = $row;
                }
            }

            if (count($parameters['image_name']) == count($success_delete)) {
                $this->response(array("error" => false, "message" => "Success", "success_data" => $success_delete, "failed_data" => $failed_delete), REST_Controller::HTTP_OK);
            } else {
                $this->response(array("error" => false, "message" => "Failed", "success_data" => $success_delete, "failed_data" => $failed_delete), REST_Controller::HTTP_OK);
            }
        } else {
            $output_path = "uploads/product/" . $parameters['image_name'];

            if (file_exists($output_path)) {
                if (unlink($output_path)) {
                    $this->response(array("error" => false, "message" => "Success", "data" => $parameters['image_name']), REST_Controller::HTTP_OK);
                } else {
                    $this->response(array("error" => false, "message" => "Failed"), REST_Controller::HTTP_OK);
                }
            } else {
                $this->response(array("error" => false, "message" => "Skip, No images found."), REST_Controller::HTTP_OK);
            }
        }
        //        } else {
        //            $output['status'] = FALSE;
        //            $output['message'] = array(
        //                "error_msg" => "Required parameters!",
        //                "error_details" => $this->form_validation->error_array()
        //            );
        //
        //            $this->response($output, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        //        }
    }
}
