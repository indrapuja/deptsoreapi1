<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

/**
 * Description of Access_user
 *
 * @author Achmad Hafizh + KD Dev Team
 */
class Access_user extends REST_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('access_user_model');
    }

    function data_staff_spg_post()
    {
        $parameters = array(
            'nik' => $this->post('nik')
        );

        $this->form_validation->set_data($parameters);
        $this->form_validation->set_rules('nik', 'nik', 'required');

        if ($this->form_validation->run() == TRUE) {
            $data = $this->access_user_model->get_staff_spg_user_custom($this->access_user_model->format_string(strtolower($parameters['nik'])));

            if (!empty($data) and $data['total_records'] > 0) {
                $this->access_user_model->create_login_history($data['data'][0]->user_no);

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

    public function read_get()
    {
        $this->_check_jwt();

        $id = $this->get('id');

        if (empty($id)) {
            $this->response(array('status' => FALSE, 'message' => 'Bad Request!.'), REST_Controller::HTTP_BAD_REQUEST);
        } else {
            $id_no = strtolower($this->access_user_model->format_string($this->get('id')));

            $data = $this->access_user_model->get_user($id_no);

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

    function read_post()
    {
        $this->_check_jwt();

        $id = array('group_no' => $this->post('group_no'));

        $this->form_validation->set_data($id);
        $this->form_validation->set_rules('group_no', 'group_no', 'required');

        if ($this->form_validation->run() == TRUE) {
            if ($id['group_no'] >= '10' and $id['group_no'] <= '19') {
                $filter = array('store_list' => $this->post('store_list'));

                $this->form_validation->set_data($filter);
                $this->form_validation->set_rules('store_list', 'store_list', 'required');

                if ($this->form_validation->run() == TRUE) {
                    $data = $this->access_user_model->get_custom_user($this->access_user_model->format_string($filter['store_list']), NULL);

                    if ($data['total_records'] > 0) {
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
            } else if ($id['group_no'] == '80') {
                $data = $this->access_user_model->get_custom_user(NULL, NULL);

                if ($data['total_records'] > 0) {
                    $output['status'] = TRUE;
                    $output['message'] = "Records found.";

                    $this->response(array_merge($output, $data), REST_Controller::HTTP_OK);
                } else {
                    $output['status'] = FALSE;
                    $output['message'] = "No records found.";

                    $this->response($output, REST_Controller::HTTP_NOT_FOUND);
                }
            } else {
                $filter = array('hrddept_list' => $this->post('hrddept_list'));

                $this->form_validation->set_data($filter);
                $this->form_validation->set_rules('hrddept_list', 'hrddept_list', 'required');

                if ($this->form_validation->run() == TRUE) {
                    $data = $this->access_user_model->get_custom_user(NULL, $filter['hrddept_list']);

                    if ($data['total_records'] > 0) {
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
        } else {
            $output['status'] = FALSE;
            $output['message'] = $this->form_validation->error_array();

            $this->response($output, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    function detail_user_by_group_post()
    {
        $id = array('group_no' => $this->post('group_no'));

        $this->form_validation->set_data($id);
        $this->form_validation->set_rules('group_no', 'group_no', 'required');

        if ($this->form_validation->run() == TRUE) {
            $data = $this->access_user_model->get_user_by_group($id['group_no']);

            if ($data['total_records'] > 0) {
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

    public function group_internal_get()
    {
        $this->_check_jwt();

        $data = $this->access_user_model->get_group_internal();

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

    public function group_external_get()
    {
        $this->_check_jwt();

        $data = $this->access_user_model->get_group_external();

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

    function test_post()
    {
echo "OK";
    }

    function login_post()
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
            $data = $this->access_user_model->get_spesific_user($this->access_user_model->format_string(strtolower($parameters['user_id'])), $this->access_user_model->format_string(md5($parameters['user_password'])), $parameters['type']);

            if (!empty($data) and $data['total_records'] > 0) {
                $this->access_user_model->create_login_history($data['data'][0]->user_no);

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

    function login_staff_post()
    {
        $this->_check_jwt();

        $parameters = array(
            'user_id' => $this->post('user_id'),
            'user_password' => $this->post('user_password')
        );

        $this->form_validation->set_data($parameters);
        $this->form_validation->set_rules('user_id', 'user_id', 'required');
        $this->form_validation->set_rules('user_password', 'user_password', 'required');

        if ($this->form_validation->run() == TRUE) {
            $data = $this->access_user_model->get_staff_user($this->access_user_model->format_string(strtolower($parameters['user_id'])), $this->access_user_model->format_string($parameters['user_password']));

            if (!empty($data) and $data['total_records'] > 0) {
                $this->access_user_model->create_login_history($data['data'][0]->user_no);

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

    function login_staff_spg_post()
    {
        $this->_check_jwt();

        $parameters = array(
            'user_id' => $this->post('user_id'),
            'user_password' => $this->post('user_password')
        );

        $this->form_validation->set_data($parameters);
        $this->form_validation->set_rules('user_id', 'user_id', 'required');
        $this->form_validation->set_rules('user_password', 'user_password', 'required');

        if ($this->form_validation->run() == TRUE) {
            $data = $this->access_user_model->get_staff_spg_user($this->access_user_model->format_string(strtolower($parameters['user_id'])), $this->access_user_model->format_string($parameters['user_password']));

            if (!empty($data) and $data['total_records'] > 0) {
                $this->access_user_model->create_login_history($data['data'][0]->user_no);

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

    function zendesk_login_post()
    {
        $this->_check_jwt();

        $parameters = array(
            'user_id' => $this->post('user_id'),
            'user_password' => $this->post('user_password')
        );

        $this->form_validation->set_data($parameters);
        $this->form_validation->set_rules('user_id', 'user_id', 'required');
        $this->form_validation->set_rules('user_password', 'user_password', 'required');

        if ($this->form_validation->run() == TRUE) {
            $data = $this->access_user_model->get_spesific_user($this->access_user_model->format_string(strtolower($parameters['user_id'])), $this->access_user_model->format_string(md5($parameters['user_password'])));

            if (!empty($data) and $data['total_records'] > 0) {
                $output['status'] = TRUE;
                $output['message'] = "Records found.";

                foreach ($data['data'] as $obj) {
                    $group_no = $obj->group_no;
                }

                $output['group_no'] = $group_no;

                $this->response($output, REST_Controller::HTTP_OK);
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

    function create_internal_post()
    {
        $this->_check_jwt();

        $parameters = array(
            'user_no' => $this->post('user_no'),
            'user_name' => $this->post('user_name'),
            'email' => $this->post('email'),
            'group_no' => $this->post('group_no'),
            'dept_alias' => $this->post('dept_alias'),
            'store_list' => $this->post('store_list'),
            'hrddept_list' => $this->post('hrddept_list'),
            'category_list' => $this->post('category_list'),
            'foto' => $this->post('foto'),
            'user_status' => $this->post('user_status'),
            'user_admin' => $this->post('user_admin')
        );

        if ($this->validate_create_internal_parameters($parameters)->run() === TRUE) {
            $this->load->model('transaction_model');

            $foto = $parameters['user_no'] . ".jpg";
            $random_password = rand(111111, 999999);

            $data['user_no'] = "'" . $parameters['user_no'] . "'";
            $data['user_name'] = "'" . $parameters['user_name'] . "'";
            $data['user_password'] = "'" . md5($random_password) . "'";
            $data['email'] = "'" . $parameters['email'] . "'";
            $data['group_no'] = "'" . $parameters['group_no'] . "'";
            $data['dept_alias'] = "'" . $parameters['dept_alias'] . "'";
            $data['store_list'] = "'" . $parameters['store_list'] . "'";
            $data['hrddept_list'] = '"' . $this->access_user_model->format_string($parameters['hrddept_list']) . '"';
            $data['category_list'] = "'" . $parameters['category_list'] . "'";
            $data['foto'] = "'" . $foto . "'";
            $data['user_status'] = 2;
            $data['user_admin'] = "'" . $parameters['user_admin'] . "'";
            $data['ts_admin'] = "'" . date('Y-m-d H:i:s') . "'";
            $data['ts_activation'] = "'" . date('Y-m-d H:i:s') . "'";

            $insert = $this->transaction_model->insert('access_user', $data);

            if ($insert['total_affected'] > 0) {
                $this->upload_image('foto', $foto);

                $subject = "METRO Access Credentials";
                $message = "Dear User,
                            <br><br>
                            Your email has been registered for the metro access app.
                            <br>
                            Your username is <b><i>" . $parameters['user_name'] . "</i></b> and default password is <b><i>$random_password</i></b>
                            <p style='color: red;'>
                                <u>
                                    <i>
                                        Plase change your password in profile menu after first login.
                                    </i>    
                                </u>
                            </p>
                            <b>
                                <i>
                                    Regards
                                    <br>
                                    METRO Services
                                </i>
                            </b>";
                $this->send_mail($parameters['email'], $subject, $message);

                $output['status'] = TRUE;
                $output['message'] = "User successfully created";

                $this->response($output, REST_Controller::HTTP_CREATED);
            } else {
                $output['status'] = FALSE;
                $output['message'] = "User failed created";

                $this->response($output, REST_Controller::HTTP_CONFLICT);
            }
        } else {
            $output['status'] = FALSE;
            $output['message'] = $this->form_validation->error_array();

            $this->response($output, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    function create_external_post()
    {
        $this->_check_jwt();

        $parameters = array(
            'cred_no' => $this->post('cred_no'),
            'user_name' => $this->post('user_name'),
            'email' => $this->post('email'),
            'group_no' => $this->post('group_no'),
            'store_list' => $this->post('store_list'),
            'cred_name' => $this->post('cred_name'),
            'class_list' => $this->post('class_list'),
            'foto' => $this->post('foto'),
            'user_status' => $this->post('user_status'),
            'user_admin' => $this->post('user_admin')
        );

        if ($this->validate_create_external_parameters($parameters)->run() === TRUE) {
            $this->load->model('transaction_model');

            $user_no = $this->generate_user_no($parameters['cred_no']);
            $foto = $user_no . ".jpg";
            $random_password = rand(111111, 999999);

            $data['cred_no'] = "'" . $parameters['cred_no'] . "'";
            $data['cred_name'] = '"' . $parameters['cred_name'] . '"';
            $data['user_no'] = "'" . $user_no . "'";
            $data['user_name'] = "'" . $parameters['user_name'] . "'";
            $data['user_password'] = "'" . md5($random_password) . "'";
            $data['email'] = "'" . $parameters['email'] . "'";
            $data['group_no'] = "'" . $parameters['group_no'] . "'";
            $data['store_list'] = "'" . $parameters['store_list'] . "'";
            $data['class_list'] = "'" . $parameters['class_list'] . "'";
            $data['foto'] = "'" . $foto . "'";
            $data['user_status'] = 2;
            $data['user_admin'] = "'" . $parameters['user_admin'] . "'";
            $data['ts_admin'] = "'" . date('Y-m-d H:i:s') . "'";
            $data['ts_activation'] = "'" . date('Y-m-d H:i:s') . "'";

            $insert = $this->transaction_model->insert('access_user', $data);

            if ($insert['total_affected'] > 0) {
                $this->upload_image('foto', $foto);

                $subject = "METRO Access Credentials";
                $message = "Dear User,
                            <br><br>
                            Your email has been registered for the metro access app.
                            <br>
                            Your username is <b><i>" . $parameters['user_name'] . "</i></b> and default password is <b><i>$random_password</i></b>
                            <p style='color: red;'>
                                <u>
                                    <i>
                                        Plase change your password in profile menu after first login.
                                    </i>    
                                </u>
                            </p>
                            <b>
                                <i>
                                    Regards
                                    <br>
                                    METRO Services
                                </i>
                            </b>";
                $this->send_mail($parameters['email'], $subject, $message);

                $output['status'] = TRUE;
                $output['message'] = "User successfully created";

                $this->response($output, REST_Controller::HTTP_CREATED);
            } else {
                $output['status'] = FALSE;
                $output['message'] = "User failed created";

                $this->response($output, REST_Controller::HTTP_CONFLICT);
            }
        } else {
            $output['status'] = FALSE;
            $output['message'] = $this->form_validation->error_array();

            $this->response($output, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    function update_internal_post()
    {
        $this->_check_jwt();

        $parameters = array(
            'user_no' => $this->post('user_no'),
            'email' => $this->post('email'),
            'group_no' => $this->post('group_no'),
            'dept_alias' => $this->post('dept_alias'),
            'store_list' => $this->post('store_list'),
            'hrddept_list' => $this->post('hrddept_list'),
            'category_list' => $this->post('category_list'),
            'user_status' => $this->post('user_status'),
            'user_admin' => $this->post('user_admin'),
            'foto' => $this->post('foto')
        );

        if ($this->validate_update_internal_parameters($parameters)->run() === TRUE) {
            $this->load->model('transaction_model');

            $foto = $parameters['user_no'] . ".jpg";

            $keys['user_no'] = $parameters['user_no'];

            $data['email'] = "'" . trim($parameters['email']) . "'";
            $data['group_no'] = "'" . $parameters['group_no'] . "'";
            $data['dept_alias'] = "'" . trim($parameters['dept_alias']) . "'";
            $data['store_list'] = "'" . $parameters['store_list'] . "'";
            $data['hrddept_list'] = '"' . $this->access_user_model->format_string($parameters['hrddept_list']) . '"';
            $data['category_list'] = "'" . $parameters['category_list'] . "'";
            $data['foto'] = "'" . $foto . "'";
            $data['user_status'] = $parameters['user_status'];
            $data['user_admin'] = "'" . $parameters['user_admin'] . "'";
            $data['ts_admin'] = "'" . date('Y-m-d H:i:s') . "'";

            $update = $this->transaction_model->update('access_user', $keys, $data);

            if ($update['total_affected'] > 0) {
                $this->upload_image('foto', $foto);

                $output['status'] = TRUE;
                $output['message'] = "User successfully updated";

                $this->response($output, REST_Controller::HTTP_OK);
            } else {
                $output['status'] = FALSE;
                $output['message'] = "User failed updated";

                $this->response($output, REST_Controller::HTTP_CONFLICT);
            }
        } else {
            $output['status'] = FALSE;
            $output['message'] = $this->form_validation->error_array();

            $this->response($output, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    function update_external_post()
    {
        $this->_check_jwt();

        $parameters = array(
            'cred_no' => $this->post('cred_no'),
            'user_no' => $this->post('user_no'),
            //'user_name' => $this->post('user_name'),
            'email' => $this->post('email'),
            'group_no' => $this->post('group_no'),
            'store_list' => $this->post('store_list'),
            'cred_name' => $this->post('cred_name'),
            'class_list' => $this->post('class_list'),
            'user_status' => $this->post('user_status'),
            'user_admin' => $this->post('user_admin'),
            'foto' => $this->post('foto')
        );

        if ($this->validate_update_external_parameters($parameters)->run() === TRUE) {
            $this->load->model('transaction_model');

            $foto = $parameters['user_no'] . ".jpg";

            $keys['user_no'] = $parameters['user_no'];

            //$data['user_name'] = "'".$parameters['user_name']."'";
            $data['email'] = "'" . trim($parameters['email']) . "'";
            $data['group_no'] = "'" . $parameters['group_no'] . "'";
            $data['store_list'] = "'" . $parameters['store_list'] . "'";
            $data['cred_no'] = "'" . $parameters['cred_no'] . "'";
            $data['cred_name'] = "'" . trim($parameters['cred_name']) . "'";
            $data['class_list'] = "'" . $parameters['class_list'] . "'";
            $data['foto'] = "'" . $foto . "'";
            $data['user_status'] = $parameters['user_status'];
            $data['user_admin'] = "'" . $parameters['user_admin'] . "'";
            $data['ts_admin'] = "'" . date('Y-m-d H:i:s') . "'";

            $update = $this->transaction_model->update('access_user', $keys, $data);

            if ($update['total_affected'] > 0) {
                $this->upload_image('foto', $foto);

                $output['status'] = TRUE;
                $output['message'] = "User successfully updated";

                $this->response($output, REST_Controller::HTTP_OK);
            } else {
                $output['status'] = FALSE;
                $output['message'] = "User failed updated";

                $this->response($output, REST_Controller::HTTP_CONFLICT);
            }
        } else {
            $output['status'] = FALSE;
            $output['message'] = $this->form_validation->error_array();

            $this->response($output, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    function update_password_post()
    {
        $this->_check_jwt();

        $parameters = array(
            'user_no' => $this->post('user_no'),
            'user_password' => $this->post('user_password')
        );

        $this->form_validation->set_data($parameters);
        $this->form_validation->set_rules('user_no', 'user_no', 'required');
        $this->form_validation->set_rules('user_password', 'user_password', 'required');

        if ($this->form_validation->run() === TRUE) {
            $this->load->model('transaction_model');

            $keys['user_no'] = $parameters['user_no'];

            $data['user_password'] = "'" . md5(trim($parameters['user_password'])) . "'";

            $update = $this->transaction_model->update('access_user', $keys, $data);

            if ($update['total_affected'] > 0) {
                $output['status'] = TRUE;
                $output['message'] = "User successfully updated";

                $this->response($output, REST_Controller::HTTP_OK);
            } else {
                $output['status'] = FALSE;
                $output['message'] = "User failed updated";

                $this->response($output, REST_Controller::HTTP_CONFLICT);
            }
        } else {
            $output['status'] = FALSE;
            $output['message'] = $this->form_validation->error_array();

            $this->response($output, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    function update_profile_post()
    {
        $this->_check_jwt();

        $parameters = array(
            'user_no' => $this->post('user_no'),
            'user_name' => $this->post('user_name'),
            'email' => $this->post('email'),
            'foto' => $this->post('foto')
        );

        $this->form_validation->set_data($parameters);
        $this->form_validation->set_rules('user_no', 'user_no', 'required');
        $this->form_validation->set_rules('user_name', 'user_name', 'required');
        $this->form_validation->set_rules('email', 'email', 'required');

        if ($this->form_validation->run() === TRUE) {
            $this->load->model('transaction_model');

            $foto = $parameters['user_no'] . ".jpg";

            $keys['user_no'] = $parameters['user_no'];

            $data['user_name'] = "'" . $parameters['user_name'] . "'";
            $data['email'] = "'" . trim($parameters['email']) . "'";
            $data['foto'] = "'" . $foto . "'";

            $update = $this->transaction_model->update('access_user', $keys, $data);

            if ($update['total_affected'] > 0) {
                $this->upload_image('foto', $foto);

                $output['status'] = TRUE;
                $output['message'] = "User successfully updated";

                $this->response($output, REST_Controller::HTTP_OK);
            } else {
                $output['status'] = FALSE;
                $output['message'] = "User failed updated";

                $this->response($output, REST_Controller::HTTP_CONFLICT);
            }
        } else {
            $output['status'] = FALSE;
            $output['message'] = $this->form_validation->error_array();

            $this->response($output, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    function delete_delete()
    {
        $this->_check_jwt();

        $keys = array(
            'user_no' => $this->delete('user_no')
        );

        $this->form_validation->set_data($keys);
        $this->form_validation->set_rules('user_no', 'user_no', 'required');

        if ($this->form_validation->run() === TRUE) {
            $this->load->model('transaction_model');

            $delete = $this->transaction_model->delete('access_user', $keys);

            if ($delete['total_affected'] > 0) {
                $path = "uploads/users/" . $keys['user_no'] . ".jpg";

                if (file_exists($path)) {
                    unlink($path);
                }

                $output['status'] = TRUE;
                $output['message'] = "User successfully deleted";

                $this->response($output, REST_Controller::HTTP_OK);
            } else {
                $output['status'] = FALSE;
                $output['message'] = "User failed deleted";

                $this->response($output, REST_Controller::HTTP_ACCEPTED);
            }
        } else {
            $output['status'] = FALSE;
            $output['message'] = $this->form_validation->error_array();

            $this->response($output, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    function reset_password_post()
    {
        $this->_check_jwt();

        $parameters = array(
            'user_no' => $this->post('user_no'),
            'user_name' => $this->post('user_name'),
            'email' => $this->post('email')
        );

        $this->form_validation->set_data($parameters);
        $this->form_validation->set_rules('user_no', 'user_no', 'required');
        $this->form_validation->set_rules('user_name', 'user_name', 'required');
        $this->form_validation->set_rules('email', 'email', 'required');

        if ($this->form_validation->run() === TRUE) {
            $this->load->model('transaction_model');

            $random_password = rand(111111, 999999);

            $keys['user_no'] = $parameters['user_no'];
            $data['user_password'] = "'" . md5($random_password) . "'";

            $update = $this->transaction_model->update('access_user', $keys, $data);

            if ($update['total_affected'] > 0) {
                $subject = "METRO Access Reset Password";
                $message = "Dear User,
                            <br><br>
                            Your credentials has been reseted for the metro access app.
                            <br>
                            Your username is <b><i>" . $parameters['user_name'] . "</i></b> and default password is <b><i>$random_password</i></b>
                            <p style='color: red;'>
                                <u>
                                    <i>
                                        Plase change your password in profile menu after first login.
                                    </i>    
                                </u>
                            </p>
                            <b>
                                <i>
                                    Regards
                                    <br>
                                    METRO Services
                                </i>
                            </b>";
                $this->send_mail(strtolower($parameters['email']), $subject, $message);

                $output['status'] = TRUE;
                $output['message'] = "Reset password success";

                $this->response($output, REST_Controller::HTTP_OK);
            } else {
                $output['status'] = FALSE;
                $output['message'] = "Reset password failed";

                $this->response($output, REST_Controller::HTTP_CONFLICT);
            }
        } else {
            $output['status'] = FALSE;
            $output['message'] = $this->form_validation->error_array();

            $this->response($output, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    function generate_user_no($cred_no)
    {
        $this->_check_jwt();

        $get_last_no = $this->access_user_model->get_last_no($cred_no);

        if (!empty($get_last_no)) {
            $running_no = (int) substr($get_last_no->last_no, 4) + 1;

            if ($running_no < 10) {
                $running_no = "0" . $running_no;
            }

            $user_no = $cred_no . $running_no;
        } else {
            $user_no = $cred_no . "01";
        }

        return $user_no;
    }

    function upload_image($element_name, $image_name)
    {
        $this->load->library('upload');
        $this->load->library('image_lib');

        $config1['upload_path'] = 'uploads/users/';
        $config1['allowed_types'] = 'jpg|png';
        $config1['overwrite'] = TRUE;
        $config1['file_name'] = $image_name;

        $this->upload->initialize($config1);

        if ($this->upload->do_upload($element_name)) {
            $image = $this->upload->data('file_name');

            $config2['image_library'] = 'gd2';
            $config2['source_image'] = 'uploads/users/' . $image;
            $config2['create_thumb'] = FALSE;
            $config2['maintain_ratio'] = FALSE;
            $config2['width'] = 720;
            $config2['height'] = 720;
            $config2['new_image'] = 'uploads/users/' . $image;

            $this->image_lib->initialize($config2);
            $this->image_lib->resize();
        }
    }

    function send_mail($email, $subject, $message)
    {
        $this->load->library('email');

        $config['protocol'] = 'smtp';
        $config['smtp_crypto'] = 'ssl';
        $config['smtp_host'] = SMTP_HOST;
        $config['smtp_port'] = SMTP_PORT;
        $config['smtp_user'] = SMTP_USER;
        $config['smtp_pass'] = base64_decode(SMTP_PASS);
        $config['mailtype'] = 'html';

        $this->email->initialize($config);

        $this->email->from('no-reply@metroindonesia.com', 'METRO Services');
        $this->email->to($email);
        //$this->email->cc('another@another-example.com');
        //$this->email->bcc('them@their-example.com');

        $this->email->subject($subject);
        $this->email->message($message);

        return $this->email->send();
    }

    function validate_create_internal_parameters($parameters)
    {
        $this->form_validation->set_data($parameters);
        $this->form_validation->set_rules('user_no', 'user_no', 'required');
        $this->form_validation->set_rules('user_name', 'user_name', 'required');
        $this->form_validation->set_rules('email', 'email', 'required');
        $this->form_validation->set_rules('group_no', 'group_no', 'required');
        $this->form_validation->set_rules('dept_alias', 'dept_alias', 'required');
        $this->form_validation->set_rules('store_list', 'store_list', 'required');
        $this->form_validation->set_rules('hrddept_list', 'hrddept_list', 'required');
        $this->form_validation->set_rules('user_status', 'user_status', 'required');
        $this->form_validation->set_rules('user_admin', 'user_admin', 'required');

        return $this->form_validation;
    }

    function validate_create_external_parameters($parameters)
    {
        $this->form_validation->set_data($parameters);
        $this->form_validation->set_rules('cred_no', 'cred_no', 'required');
        $this->form_validation->set_rules('cred_name', 'cred_name', 'required');
        $this->form_validation->set_rules('user_name', 'user_name', 'required');
        $this->form_validation->set_rules('email', 'email', 'required');
        $this->form_validation->set_rules('group_no', 'group_no', 'required');
        $this->form_validation->set_rules('store_list', 'store_list', 'required');
        $this->form_validation->set_rules('class_list', 'class_list', 'required');
        $this->form_validation->set_rules('user_status', 'user_status', 'required');
        $this->form_validation->set_rules('user_admin', 'user_admin', 'required');

        return $this->form_validation;
    }

    function validate_update_internal_parameters($parameters)
    {
        $this->form_validation->set_data($parameters);
        $this->form_validation->set_rules('user_no', 'user_no', 'required');
        $this->form_validation->set_rules('email', 'email', 'required');
        $this->form_validation->set_rules('group_no', 'group_no', 'required');
        $this->form_validation->set_rules('dept_alias', 'dept_alias', 'required');
        $this->form_validation->set_rules('store_list', 'store_list', 'required');
        $this->form_validation->set_rules('hrddept_list', 'hrddept_list', 'required');
        $this->form_validation->set_rules('user_status', 'user_status', 'required');
        $this->form_validation->set_rules('user_admin', 'user_admin', 'required');

        return $this->form_validation;
    }

    function validate_update_external_parameters($parameters)
    {
        $this->form_validation->set_data($parameters);
        $this->form_validation->set_rules('cred_no', 'cred_no', 'required');
        $this->form_validation->set_rules('cred_name', 'cred_name', 'required');
        $this->form_validation->set_rules('user_no', 'user_no', 'required');
        //$this->form_validation->set_rules('user_name', 'user_name', 'required');
        $this->form_validation->set_rules('email', 'email', 'required');
        $this->form_validation->set_rules('group_no', 'group_no', 'required');
        $this->form_validation->set_rules('store_list', 'store_list', 'required');
        $this->form_validation->set_rules('class_list', 'class_list', 'required');
        $this->form_validation->set_rules('user_status', 'user_status', 'required');
        $this->form_validation->set_rules('user_admin', 'user_admin', 'required');

        return $this->form_validation;
    }
}