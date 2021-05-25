<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

/**
 * Description of Verification
 *
 * @author Achmad Hafizh
 */
class Verification extends REST_Controller
{

    function __construct()
    {
        parent::__construct();

        //$this->_check_jwt();
        $this->load->database();
    }

    public function send_email_code_post()
    {
        $this->_prepare_basic_auth();

        $parameters = array(
            'app_id' => $this->post('app_id'),
            'email' => $this->post('email'),
            'exp_interval' => $this->post('exp_interval')
        );

        $this->form_validation->set_data($parameters);
        $this->form_validation->set_rules('app_id', 'app_id', 'required');
        $this->form_validation->set_rules('email', 'email', 'required');
        $this->form_validation->set_rules('exp_interval', 'exp_interval', 'required');

        if ($this->form_validation->run() == TRUE) {
            $generate_code = $this->db->replace('master_email_verification', $parameters);

            if ($generate_code === TRUE) {
                $query = $this->db->get_where('master_email_verification', $parameters, 1, 0);
                $get_detail_code = $query->row();

                $subject = "Email Verification Code: " . $get_detail_code->code;
                $message = $this->email_verification($get_detail_code);

                $send_mail = send_mail($get_detail_code->email, null, null, null, $subject, $message);

                if ($send_mail === TRUE) {
                    $output['status'] = REST_Controller::HTTP_OK;
                    $output['error'] = FALSE;
                    $output['message'] = "Success, verification code has been send.";
                    $output['data'] = $get_detail_code;

                    $this->response($output, REST_Controller::HTTP_OK);
                } else {
                    $output['status'] = REST_Controller::HTTP_NOT_FOUND;
                    $output['error'] = TRUE;
                    $output['message'] = "Failed, can't send email!.";

                    $this->response($output, REST_Controller::HTTP_NOT_FOUND);
                }
            } else {
                $output['status'] = REST_Controller::HTTP_NOT_FOUND;
                $output['error'] = TRUE;
                $output['message'] = "Failed, can't generate email verification code!.";

                $this->response($output, REST_Controller::HTTP_NOT_FOUND);
            }
        } else {
            $output['status'] = REST_Controller::HTTP_UNPROCESSABLE_ENTITY;
            $output['error'] = TRUE;
            $output['message'] = $this->form_validation->error_array();

            $this->response($output, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function validate_email_code_post()
    {
        $this->_prepare_basic_auth();
        
        $parameters = array(
            'app_id' => $this->post('app_id'),
            'email' => $this->post('email'),
            'code' => $this->post('code')
        );

        $this->form_validation->set_data($parameters);
        $this->form_validation->set_rules('app_id', 'app_id', 'required');
        $this->form_validation->set_rules('email', 'email', 'required');
        $this->form_validation->set_rules('code', 'code', 'required');

        if ($this->form_validation->run() == TRUE) {
            $where['app_id'] = $parameters['app_id'];
            $where['email'] = $parameters['email'];
            $where['code'] = $parameters['code'];

            $query = $this->db->get_where('master_email_verification', $where, 1, 0);
            $get_detail_code = $query->row();

            if (!empty($get_detail_code)) {
                $output['status'] = REST_Controller::HTTP_OK;
                $output['error'] = FALSE;
                $output['message'] = "Success, valid verification code.";
                $output['data'] = $get_detail_code;

                $this->response($output, REST_Controller::HTTP_OK);
            } else {
                $output['status'] = REST_Controller::HTTP_NOT_FOUND;
                $output['error'] = TRUE;
                $output['message'] = "Failed, invalid verification code!.";

                $this->response($output, REST_Controller::HTTP_NOT_FOUND);
            }
        } else {
            $output['status'] = REST_Controller::HTTP_UNPROCESSABLE_ENTITY;
            $output['error'] = TRUE;
            $output['message'] = $this->form_validation->error_array();

            $this->response($output, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    private function email_verification($data)
    {
        $message = "This is your verification code "
            . "<b>" . $data->code . "</b> "
            . "valid for <b>" . $data->exp_interval . "</b> minutes.";

        return $message;
    }
}