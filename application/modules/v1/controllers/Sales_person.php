<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

/**
 * Description of User
 *
 * @author Achmad Hafizh & Hosea R Nando
 */
class Sales_person extends REST_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->_check_jwt();

        $this->load->model('Sales_person_model');
        $this->load->library('encryption');
    }

// --------------------------------------------------- START ----------------------------------------------------------------------------------------    
    function test_post(){
        echo "Test";
    }

    function read_biodata_by_ktp_post(){
        $this->_check_jwt();

        $parameters = array(
            'ktp_no' => $this->post('ktp_no')
        );

        $this->form_validation->set_data($parameters);
        $this->form_validation->set_rules('ktp_no', 'ktp_no', 'required');

        if ($this->form_validation->run() == TRUE) {
            $data = $this->Sales_person_model->read_biodata_by_ktp($parameters['ktp_no']);

            if (!empty($data) AND $data['total_records'] > 0) {
                // $this->access_user_model->create_login_history($data['data'][0]->user_no);
                
                $output['status'] = TRUE;
                $output['message'] = "Records found.";

                $this->response(array_merge($output, $data), REST_Controller::HTTP_OK);
            } else {
                $output['status'] = FALSE;
                $output['message'] = "No records found.";

                $this->response($output, REST_Controller::HTTP_NOT_FOUND);
            }
        } else {
            $output['status'] = FALSE;
            $output['message'] = $this->form_validation->error_array();

            $this->response($output, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        }

    }

    public function save_biodata_post()
    {
        $this->benchmark->mark('code_start');

        $parameters = $this->post();
        // print_r($parameters[0]);
        // print_r($parameters);
 
        if (!empty($parameters)) {
            $validate_param = $this->validate_parameter_save_biodata($parameters);

            if ($validate_param->run() == TRUE) {
                $save = $this->Sales_person_model->save_sales_person_biodata($parameters);
                print_r($save);
                // if (!empty($save) and $save['total_affected'] > 0) {
                //     $this->benchmark->mark('code_end');

                //     $output['status'] = REST_Controller::HTTP_OK;
                //     $output['error'] = false;
                //     $output['message'] = "Success create delivery order list.";
                //     $output['data'] = $save['data'];
                //     $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
                //     $this->response($output, REST_Controller::HTTP_OK);
                // } else {
                //     $this->benchmark->mark('code_end');

                //     $output['status'] = REST_Controller::HTTP_NOT_MODIFIED;
                //     $output['error'] = true;
                //     $output['error_detail'] = $save['error'];
                //     $output['message'] = "Failed create delivery order list!";
                //     $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
                //     $this->response($output, REST_Controller::HTTP_NOT_MODIFIED);
                // }
            } else {
                $this->benchmark->mark('code_end');

                $output['status'] = REST_Controller::HTTP_UNPROCESSABLE_ENTITY;
                $output['error'] = true;
                $output['error_detail'] = $validate_param->error_array();
                $output['message'] = "Required JSON Format! {.....}";
                $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
                $this->response($output, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
            }
        } else {
            $this->benchmark->mark('code_end');

            $output['status'] = REST_Controller::HTTP_UNPROCESSABLE_ENTITY;
            $output['error'] = true;
            $output['message'] = "Required parameters!";
            $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
            $this->response($output, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    function validate_parameter_save_biodata($parameters)
    {
        $this->form_validation->set_data($parameters);
        $this->form_validation->set_rules('ktp_no', 'ktp_no', 'required');
        $this->form_validation->set_rules('name', 'name', 'required');
        $this->form_validation->set_rules('gender', 'gender', 'required');
        $this->form_validation->set_rules('birth_city', 'birth_city', 'required');
        $this->form_validation->set_rules('birth_date', 'birth_date', 'required');
        $this->form_validation->set_rules('address_street', 'address_street', 'required');
        $this->form_validation->set_rules('address_city', 'address_city', 'required');
        $this->form_validation->set_rules('address_province', 'address_province', 'required');
        $this->form_validation->set_rules('address_postal_code', 'address_postal_code', 'required');
        $this->form_validation->set_rules('phone', 'phone', 'required');
        $this->form_validation->set_rules('email', 'email', 'required');
        $this->form_validation->set_rules('religion', 'religion', 'required');
        $this->form_validation->set_rules('education', 'education', 'required');
        $this->form_validation->set_rules('experience', 'experience', 'required');
        $this->form_validation->set_rules('is_merried', 'is_merried', 'required');

        return $this->form_validation;
    }

// --------------------------------------------------- END ------------------------------------------------------------------------------------------

    function login3_post() {
        $this->_check_jwt();
        
        $parameters = array(
            'user_id' => $this->post('user_id'),
            'user_password' => $this->post('user_password')
        );

        $this->form_validation->set_data($parameters);
        $this->form_validation->set_rules('user_id', 'user_id', 'required');
        $this->form_validation->set_rules('user_password', 'user_password', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $data = $this->User_model->get_user_data($this->User_model->format_string(strtolower($parameters['user_id'])), $this->User_model->format_string(md5($parameters['user_password'])));
print_r($data);
            // if (!empty($data) AND $data['total_records'] > 0) {
            //     // $this->access_user_model->create_login_history($data['data'][0]->user_no);
                
            //     $output['status'] = TRUE;
            //     $output['message'] = "Records found.";

            //     $this->response(array_merge($output, $data), REST_Controller::HTTP_OK);
            // } else {
            //     $output['status'] = FALSE;
            //     $output['message'] = "No records found.";

            //     $this->response($output, REST_Controller::HTTP_NOT_FOUND);
            // }
        } else {
            $output['status'] = FALSE;
            $output['message'] = $this->form_validation->error_array();

            $this->response($output, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    function login2_post() {
        // $this->_check_jwt();
        
        $parameters = array(
            'user_id' => $this->post('user_id'),
            'user_password' => $this->post('user_password')
        );

        $this->form_validation->set_data($parameters);
        $this->form_validation->set_rules('user_id', 'user_id', 'required');
        $this->form_validation->set_rules('user_password', 'user_password', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $data = $this->User_model->get_user_data($this->User_model->format_string(strtolower($parameters['user_id'])), $this->User_model->format_string(md5($parameters['user_password'])));

            if (!empty($data) AND $data['total_records'] > 0) {
                // $this->access_user_model->create_login_history($data['data'][0]->user_no);
                
                $output['status'] = TRUE;
                $output['message'] = "Records found.";

                $this->response(array_merge($output, $data), REST_Controller::HTTP_OK);
            } else {
                $output['status'] = FALSE;
                $output['message'] = "No records found.";

                $this->response($output, REST_Controller::HTTP_NOT_FOUND);
            }
        } else {
            $output['status'] = FALSE;
            $output['message'] = $this->form_validation->error_array();

            $this->response($output, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        }
    }



    public function force_logout_post()
    {
        $this->_prepare_basic_auth();

        $parameters = array(
            'user_id' => $this->post('user_id')
        );

        $this->form_validation->set_data($parameters);
        $this->form_validation->set_rules('user_id', 'user_id', 'required');

        if ($this->form_validation->run() == TRUE) {
            $options = array(
                'cluster' => PUSHER_CLUSTER,
                'useTLS' => true
            );
            $pusher = new Pusher\Pusher(
                PUSHER_KEY,
                PUSHER_SECRET,
                PUSHER_APP_ID,
                $options
            );

            $data['action'] = 'logout';
            $data['message'] = 'Force logout';
            $data['user_id'] = $parameters['user_id'];

            if ($pusher->trigger('my-channel', 'my-event', $data)) {
                $output['status'] = REST_Controller::HTTP_OK;
                $output['error'] = false;
                $output['message'] = "Success force logout.";
            } else {
                $output['status'] = REST_Controller::HTTP_CONFLICT;
                $output['error'] = true;
                $output['message'] = "Failed force logout!";
            }

            $this->response($output, REST_Controller::HTTP_OK);
        } else {
            $output['status'] = REST_Controller::HTTP_UNPROCESSABLE_ENTITY;
            $output['error'] = true;
            $output['message'] = $this->form_validation->error_array();

            $this->response($output, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function update_password_post()
    {
        $this->_prepare_basic_auth();

        $parameters = array(
            'user_id' => $this->post('user_id'),
            'user_password' => $this->post('user_password')
        );

        $this->form_validation->set_data($parameters);
        $this->form_validation->set_rules('user_id', 'user_id', 'required');
        $this->form_validation->set_rules('user_password', 'user_password', 'required');

        if ($this->form_validation->run() == TRUE) {
            $data = $this->User_model->get_user(strtolower($parameters['user_id']));

            if (!empty($data) and $data['total_records'] > 0) {
                $new_password = $this->encryption->encrypt($parameters['user_password']);

                $values = array('user_password' => $new_password);
                $keys = array('user_email' => $parameters['user_id']);

                $this->db->update('master_user_app', $values, $keys);

                if ($this->db->affected_rows() > 0) {
                    $data['password'] = $new_password;
                    send_mail(strtolower($parameters['user_id']), null, null, null, "Recover Password", $this->load->view('recovery_password', $data, true));

                    $output['status'] = REST_Controller::HTTP_OK;
                    $output['error'] = false;
                    $output['message'] = "Success reset password.";
                } else {
                    $output['status'] = REST_Controller::HTTP_CONFLICT;
                    $output['error'] = true;
                    $output['message'] = "Failed reset password!";
                }

                $this->response($output, REST_Controller::HTTP_OK);
            } else {
                $output['status'] = REST_Controller::HTTP_NOT_FOUND;
                $output['error'] = true;
                $output['message'] = "No records found.";

                $this->response($output, REST_Controller::HTTP_NOT_FOUND);
            }
        } else {
            $output['status'] = REST_Controller::HTTP_UNPROCESSABLE_ENTITY;
            $output['error'] = true;
            $output['message'] = $this->form_validation->error_array();

            $this->response($output, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function recreate_password_post()
    {
        $this->_prepare_basic_auth();

        $parameters = array(
            'user_id' => $this->post('user_id')
        );

        $this->form_validation->set_data($parameters);
        $this->form_validation->set_rules('user_id', 'user_id', 'required');

        if ($this->form_validation->run() == TRUE) {
            $data = $this->User_model->get_user(strtolower($parameters['user_id']));

            if (!empty($data) and $data['total_records'] > 0) {
                $this->load->helper('string');

                $random_password = random_string('alnum', 6);
                $ciphertext = $this->encryption->encrypt($random_password);

                $values = array('user_password' => $ciphertext);
                $keys = array('user_email' => $parameters['user_id']);

                $this->db->update('master_user_app', $values, $keys);

                if($this->db->affected_rows() > 0) {
                    $data['password'] = $random_password;
                    send_mail(strtolower($parameters['user_id']), null, null, null, "Recover Password", $this->load->view('recovery_password', $data, true));

                    $output['status'] = REST_Controller::HTTP_OK;
                    $output['error'] = false;
                    $output['message'] = "Success reset password.";
                } else {
                    $output['status'] = REST_Controller::HTTP_CONFLICT;
                    $output['error'] = true;
                    $output['message'] = "Failed reset password!";
                }

                $this->response($output, REST_Controller::HTTP_OK);
            } else {
                $output['status'] = REST_Controller::HTTP_NOT_FOUND;
                $output['error'] = true;
                $output['message'] = "No records found.";

                $this->response($output, REST_Controller::HTTP_NOT_FOUND);
            }
        } else {
            $output['status'] = REST_Controller::HTTP_UNPROCESSABLE_ENTITY;
            $output['error'] = true;
            $output['message'] = $this->form_validation->error_array();

            $this->response($output, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function login_post()
    {
        $this->_prepare_basic_auth();

        $parameters = array(
            'user_id' => $this->post('user_id'),
            'user_password' => $this->post('user_password')
        );

        $this->form_validation->set_data($parameters);
        $this->form_validation->set_rules('user_id', 'user_id', 'required');
        $this->form_validation->set_rules('user_password', 'user_password', 'required');

        if ($this->form_validation->run() == TRUE) {
            $data = $this->User_model->validate_user(strtolower($parameters['user_id']), $parameters['user_password']);
            
            if (!empty($data) and $data['total_records'] > 0) {
                // $menuList = $this->User_model->getAccessMenu($data['data']->client_no, $data['data']->menu_privilege);
                $menuList = $this->User_model->getAccessMenu($data['data']->user_id);
                // $this->User_model->create_login_history($data['data'][0]->user_no);
                // print_r($menuList);/*   SAMPAI DISINI     
                $output['status'] = REST_Controller::HTTP_OK;
                $output['error'] = false;
                $output['message'] = "Records found.";
                $output['data'] = $data['data'];
                $output['data']->menu_list = $menuList['data'];

                $this->response($output, REST_Controller::HTTP_OK);
            } else {
                $output['status'] = REST_Controller::HTTP_NOT_FOUND;
                $output['error'] = true;
                $output['message'] = "No records found.";

                $this->response($output, REST_Controller::HTTP_NOT_FOUND);
            }
        } else {
            $output['status'] = REST_Controller::HTTP_UNPROCESSABLE_ENTITY;
            $output['error'] = true;
            $output['message'] = $this->form_validation->error_array();

            $this->response($output, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    function login_old_post()
    {
        $this->_check_jwt();

        $parameters = array(
            'user_id' => $this->post('user_id'),
            'user_password' => $this->post('user_password'),
            'type' => $this->post('type')
        );

        $this->form_validation->set_data($parameters);
        $this->form_validation->set_rules('user_id', 'user_id', 'required');
        $this->form_validation->set_rules('user_password', 'user_password', 'required'); 

        if ($this->form_validation->run() == TRUE) {
            $data = $this->User_model->get_spesific_user($this->User_model->format_string(strtolower($parameters['user_id'])), $this->User_model->format_string(md5($parameters['user_password'])), $parameters['type']);
      
            if (!empty($data) and $data['total_records'] > 0) {
                // $this->access_user_model->create_login_history($data['data'][0]->user_no);

                $output['status'] = TRUE;
                $output['message'] = "Records found.";

                $this->response(array_merge($output, $data), REST_Controller::HTTP_OK);
            } else {
                $output['status'] = FALSE;
                $output['message'] = "No records found.";

                $this->response($output, REST_Controller::HTTP_NOT_FOUND);
            }
        } else {
            $output['status'] = FALSE;
            $output['message'] = $this->form_validation->error_array();

            $this->response($output, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function register_post()
    {
        $this->_prepare_basic_auth();

        $parameters = array(
            'email' => $this->post('email'),
            'phone' => $this->post('phone'),
            'name' => $this->post('name'),
            'address' => $this->post('address'),
            'pic' => $this->post('pic'),
            'package' => $this->post('package'),
            'password' => $this->post('password')
        );

        $this->form_validation->set_data($parameters);
        $this->form_validation->set_rules('email', 'email', 'required');
        $this->form_validation->set_rules('phone', 'phone', 'required');
        $this->form_validation->set_rules('name', 'name', 'required');
        $this->form_validation->set_rules('address', 'address', 'required');
        $this->form_validation->set_rules('pic', 'pic', 'required');
        $this->form_validation->set_rules('package', 'package', 'required');
        $this->form_validation->set_rules('password', 'password', 'required');

        if ($this->form_validation->run() == TRUE) {
            $data = $this->User_model->save_data($parameters);

            if (!empty($data) and empty($data['error'])) {
                $output['status'] = REST_Controller::HTTP_OK;
                $output['error'] = false;
                $output['message'] = "Success save data.";

                $this->response($output, REST_Controller::HTTP_OK);
            } else {
                $output['status'] = REST_Controller::HTTP_NOT_FOUND;
                $output['error'] = true;
                $output['message'] = "Failed save data!";

                $this->response($output, REST_Controller::HTTP_NOT_FOUND);
            }
        } else {
            $output['status'] = REST_Controller::HTTP_UNPROCESSABLE_ENTITY;
            $output['error'] = true;
            $output['message'] = $this->form_validation->error_array();

            $this->response($output, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function test1_get(){
echo $this->encryption->encrypt('123456');

    }
    

    public function package_get()
    {
        $this->_prepare_basic_auth();

        $data = $this->User_model->fetchPackage();

        if (!empty($data) and $data['total_records'] > 0) {
            $output['status'] = REST_Controller::HTTP_OK;
            $output['error'] = false;
            $output['message'] = "Records found.";
            $output['data'] = $data['data'];

            $this->response($output, REST_Controller::HTTP_OK);
        } else {
            $output['status'] = REST_Controller::HTTP_NOT_FOUND;
            $output['error'] = true;
            $output['message'] = "No records found.";

            $this->response($output, REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function package_detail_get()
    {
        $this->_prepare_basic_auth();

        $data = $this->User_model->fetchPackageDetail();

        if (!empty($data) and $data['total_records'] > 0) {
            $output['status'] = REST_Controller::HTTP_OK;
            $output['error'] = false;
            $output['message'] = "Records found.";
            $output['data'] = $data['data'];

            $this->response($output, REST_Controller::HTTP_OK);
        } else {
            $output['status'] = REST_Controller::HTTP_NOT_FOUND;
            $output['error'] = true;
            $output['message'] = "No records found.";

            $this->response($output, REST_Controller::HTTP_NOT_FOUND);
        }
    }
}
