<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

/**
 *  @OA\OpenApi(
 *      @OA\Info(
 *          version="2",
 *          title="Authentication Token",
 *          description="This is a documentation api.",
 *          termsOfService="https://metroindonesia.com/app/dev/terms/",
 *          @OA\Contact(
 *              email="achmadhafizhh@gmail.com"
 *          ),
 *          @OA\License(
 *              name="Apache 2.0",
 *              url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *          )
 *      ),
 *      @OA\Server(
 *          description="Development Host",
 *          url="http://34.101.87.71/smartretailapi/v1",
 *      ),
 *      @OA\ExternalDocumentation(
 *          description="Find out more about documentation",
 *          url="https://metroindonesia.com/app/dev/docs"
 *      )
 *  )
 */
class Token extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        // $this->generate_documentation($this->router->fetch_class());
    }

    /**
     *  @OA\Post(
     *      path="/token/generate",
     *      tags={"Token"},
     *      summary="Generate JWT authentication token",
     *      description="",
     *      operationId="generateToken",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="username",
     *                      description="",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="password",
     *                      description="",
     *                      type="string"
     *                  ),
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response="default",
     *          description=""
     *      ),
     *  )
     */

    function test_post()
    {
        echo "Test";
    }

    
    public function generate_post()
    {
        $this->form_validation->set_data([
            'username' => $this->post('username'),
            'password' => $this->post('password'),
        ]);

        $this->form_validation->set_rules('username', 'Username', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() == TRUE) {
            $login = $this->User_model->api_login($this->post('username'), md5(md5($this->post('password'))));

            if ($login['total_records'] > 0) {
                $date = new DateTime();
                $token['iat'] = $date->getTimestamp();
                $token['exp'] = $date->getTimestamp() + $this->config->item('jwt_token_expire');
                $token['data'] = $login['data'];

                $output['status'] = REST_Controller::HTTP_OK;
                $output['error'] = false;
                $output['message'] = "Token successfully generated.";
                $output['token'] = $this->jwt_encode($token);
                $this->response($output, REST_Controller::HTTP_OK);
            } else {
                $output['status'] = REST_Controller::HTTP_UNAUTHORIZED;
                $output['error'] = true;
                $output['message'] = "Invalid credentials!, please check username or password!";
                $this->response($output, REST_Controller::HTTP_UNAUTHORIZED);
            }
        } else {
            $output['status'] = REST_Controller::HTTP_UNPROCESSABLE_ENTITY;
            $output['error'] = true;
            $output['message'] = $this->form_validation->error_array();

            $this->response($output, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     *  @OA\Get(
     *      path="/token/refresh",
     *      tags={"Token"},
     *      summary="Refresh JWT authentication token using JWT Authorization Bearer Token",
     *      description="",
     *      operationId="refreshToken",
     *      parameters={},
     *      @OA\Response(
     *          response="default",
     *          description=""
     *      )
     *  )
     */
    public function refresh_get()
    {
        try {
            $decoded = $this->jwt_decode($this->jwt_token());

            $validate_id = $this->User_model->api_validate_id($decoded['data'][0]->user_api_username);

            if ($validate_id['total_records'] > 0) {
                $date = new DateTime();
                $token['iat'] = $date->getTimestamp();
                $token['exp'] = $date->getTimestamp() + $this->config->item('jwt_token_expire');
                $token['data'] = $validate_id['data'];

                $output['status'] = REST_Controller::HTTP_OK;
                $output['error'] = false; 
                $output['message'] = "Token successfully refreshed.";
                $output['token'] = $this->jwt_encode($token);
                $this->response($output, REST_Controller::HTTP_OK);
            } else {
                $output['status'] = REST_Controller::HTTP_UNAUTHORIZED;
                $output['error'] = true;
                $output['message'] = "Invalid user!, The token user id is not exist in the system!";
                $this->response($output, REST_Controller::HTTP_UNAUTHORIZED);
            }
        } catch (Exception $e) {
            $output['status'] = REST_Controller::HTTP_UNAUTHORIZED;
            $output['error'] = true;
            $output['message'] = "Invalid token!, " . $e->getMessage();
            $this->response($output, REST_Controller::HTTP_UNAUTHORIZED);
        }
    }

    /**
     *  @OA\Get(
     *      path="/token/info",
     *      tags={"Token"},
     *      summary="Decode JWT authentication token using JWT Authorization Bearer Token",
     *      description="",
     *      operationId="infoToken",
     *      parameters={},
     *      @OA\Response(
     *          response="default",
     *          description=""
     *      )
     *  )
     */
    public function info_get()
    {
        try {
            $output['status'] = REST_Controller::HTTP_OK;
            $output['error'] = false;
            $output['message'] = "Token successfully refreshed.";
            $output['token'] = $this->jwt_decode($this->jwt_token());
            $this->response($output, REST_Controller::HTTP_OK);
        } catch (Exception $e) {
            $output['status'] = REST_Controller::HTTP_UNAUTHORIZED;
            $output['error'] = true;
            $output['message'] = "Invalid token!, " . $e->getMessage();
            $this->response($output, REST_Controller::HTTP_UNAUTHORIZED);
        }
    }
}