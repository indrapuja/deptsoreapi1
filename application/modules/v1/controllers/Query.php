<?php

defined('BASEPATH') or exit('No direct script access allowed');

ini_set('max_execution_time', 0);

require APPPATH . 'libraries/REST_Controller.php';

/**
 *  @OA\OpenApi(
 *      @OA\Info(
 *          version="1",
 *          title="Query",
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
 *          url="http://192.168.0.25/v2",
 *      ),
 *      @OA\ExternalDocumentation(
 *          description="Find out more about documentation",
 *          url="https://metroindonesia.com/app/dev/docs"
 *      )
 *  )
 */
class Query extends REST_Controller
{

    function __construct()
    {
        parent::__construct();

        $this->_check_jwt();
        $this->load->model('Query_model');
        $this->generate_documentation($this->router->fetch_class());
    }

    /**
     *  @OA\Post(
     *      path="/Query/execute",
     *      tags={"Query"},
     *      summary="execute data and generate document Query",
     *      description="",
     *      operationId="execute",
     *      @OA\RequestBody(
     *          description="List of data object",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(
     *                          property="execute",
     *                          description="",
     *                          type="string"
     *                      )
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response="default",
     *          description=""
     *      )
     *  )
     */
    public function execute_post()
    {
        $this->benchmark->mark('code_start');

        $parameters = $this->post();

        $this->form_validation->set_data($parameters);
        $this->form_validation->set_rules('sql', 'sql', 'required');

        if ($this->form_validation->run() == TRUE) {
            $sql = base64_decode(base64_decode($parameters['sql']));            
            $execute = $this->Query_model->executeQuery($sql);

            if (!empty($execute) and $execute['total_data'] > 0) {
                $this->benchmark->mark('code_end');

                $output['status'] = REST_Controller::HTTP_OK;
                $output['error'] = false;
                $output['message'] = "Records found.";
                $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
                $this->response(array_merge($output, $execute), REST_Controller::HTTP_OK);
            } else {
                $this->benchmark->mark('code_end');

                $output['status'] = REST_Controller::HTTP_OK;
                $output['error'] = true;
                $output['message'] = "No records found!";
                $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
                $this->response($output, REST_Controller::HTTP_OK);
            }
        } else {
            $this->benchmark->mark('code_end');

            $output['status'] = REST_Controller::HTTP_UNPROCESSABLE_ENTITY;
            $output['error'] = true;
            $output['error_detail'] = $this->form_validation->error_array();
            $output['message'] = "Required parameters!";
            $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
            $this->response($output, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function execute_transaction_post()
    {
        $this->benchmark->mark('code_start');

        $parameters = $this->post();

        $this->form_validation->set_data($parameters);
        $this->form_validation->set_rules('sql', 'sql', 'required');

        if ($this->form_validation->run() == TRUE) {
            $listSql = base64_decode(base64_decode($parameters['sql']));
            $execute = $this->Query_model->executeTransaction($listSql);

            if (!empty($execute) and $execute['affected_rows'] > 0) {
                $this->benchmark->mark('code_end');

                $output['status'] = REST_Controller::HTTP_OK;
                $output['error'] = false;
                $output['message'] = "Success execute transactions.";
                $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
                $this->response(array_merge($output, $execute), REST_Controller::HTTP_OK);
            } else {
                $this->benchmark->mark('code_end');

                $output['status'] = REST_Controller::HTTP_CONFLICT;
                $output['error'] = true;
                $output['message'] = "Failed execute transactions!";
                $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
                $this->response($output, REST_Controller::HTTP_CONFLICT);
            }
        } else {
            $this->benchmark->mark('code_end');

            $output['status'] = REST_Controller::HTTP_UNPROCESSABLE_ENTITY;
            $output['error'] = true;
            $output['error_detail'] = $this->form_validation->error_array();
            $output['message'] = "Required parameters!";
            $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
            $this->response($output, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

}
