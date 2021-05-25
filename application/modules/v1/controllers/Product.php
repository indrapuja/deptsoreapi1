<?php

defined('BASEPATH') or exit('No direct script access allowed');

ini_set('max_execution_time', 0);

require APPPATH . 'libraries/REST_Controller.php';

/**
 *  @OA\OpenApi(
 *      @OA\Info(
 *          version="2",
 *          title="Product",
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
class Product extends REST_Controller
{

    function __construct()
    {
        parent::__construct();

        //$this->_check_jwt();
        $this->load->model('Product_model');
        $this->generate_documentation($this->router->fetch_class());
    }

    /**
     *  @OA\Post(
     *      path="/Product/update",
     *      tags={"Product"},
     *      summary="Update product detail",
     *      description="",
     *      operationId="updateProduct",
     *      @OA\RequestBody(
     *          description="List of data object",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(
     *                          property="sku_no",
     *                          description="",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="article_name",
     *                          description="",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="article_group",
     *                          description="",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="short_description",
     *                          description="",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="long_description",
     *                          description="",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="other_description",
     *                          description="",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="retail_promo",
     *                          description="",
     *                          type="integer"
     *                      ),
     *                      @OA\Property(
     *                          property="weight",
     *                          description="",
     *                          type="integer"
     *                      ),
     *                      @OA\Property(
     *                          property="length",
     *                          description="",
     *                          type="integer"
     *                      ),
     *                      @OA\Property(
     *                          property="width",
     *                          description="",
     *                          type="integer"
     *                      ),
     *                      @OA\Property(
     *                          property="height",
     *                          description="",
     *                          type="integer"
     *                      ),
     *                      @OA\Property(
     *                          property="image",
     *                          description="",
     *                          type="integer"
     *                      )
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response="default",
     *          description=""
     *      ),
     *  )
     */
    public function update_post()
    {
        $this->benchmark->mark('code_start');

        $parameters = $this->post();

        if (!empty($parameters)) {
            $validate_param = $this->validate_parameter_update($parameters[0]);

            if ($validate_param->run() == TRUE) {
                $update = $this->Product_model->update_data($parameters);

                if (!empty($update) and $update['total_affected'] > 0) {
                    $this->benchmark->mark('code_end');

                    $output['status'] = REST_Controller::HTTP_OK;
                    $output['error'] = false;
                    $output['message'] = "Success update Product list.";
                    $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
                    $this->response($output, REST_Controller::HTTP_OK);
                } else {
                    $this->benchmark->mark('code_end');

                    $output['status'] = REST_Controller::HTTP_NOT_FOUND;
                    $output['error'] = true;
                    $output['error_detail'] = $update['error'];
                    $output['message'] = "Failed update Product list!";
                    $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
                    $this->response($output, REST_Controller::HTTP_NOT_FOUND);
                }
            } else {
                $this->benchmark->mark('code_end');

                $output['status'] = REST_Controller::HTTP_UNPROCESSABLE_ENTITY;
                $output['error'] = true;
                $output['error_detail'] = $validate_param->error_array();
                $output['message'] = "Required JSON Array! [{.....}]";
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

    function validate_parameter_update($parameters)
    {
        $this->form_validation->set_data($parameters);
        $this->form_validation->set_rules('sku_no', 'sku_no', 'required');
        $this->form_validation->set_rules('article_name', 'article_name', 'required');
        $this->form_validation->set_rules('short_description', 'short_description', 'required');
        $this->form_validation->set_rules('weight', 'weight', 'required');
        // $this->form_validation->set_rules('image_main', 'image_main', 'required');

        return $this->form_validation;
    }
}
