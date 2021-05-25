<?php

defined('BASEPATH') or exit('No direct script access allowed');

ini_set('max_execution_time', 0);

require APPPATH . 'libraries/REST_Controller.php';

/**
 *  @OA\OpenApi(
 *      @OA\Info(
 *          version="2",
 *          title="Delivery Order",
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
class Delivery_order extends REST_Controller
{

    function __construct()
    {
        parent::__construct();

        //$this->_check_jwt();
        $this->load->model('Delivery_order_model');
        $this->generate_documentation($this->router->fetch_class());
    }

    public function param_create_get()
    {
        $class_no = '1010';
        $data = $this->Delivery_order_model->read_data_for_do($class_no);
        $this->response($data, REST_Controller::HTTP_OK);
    }

    public function param_update_get()
    {
        $class_no = '2012';
        $data = $this->Delivery_order_model->read_data_for_udpate($class_no);
        $this->response($data, REST_Controller::HTTP_OK);
    }

    public function migrate_document_get()
    {
        $this->benchmark->mark('code_start');

        $data = $this->Delivery_order_model->read_data_class_from_stock();

        foreach ($data as $obj) {
            $parameters = $this->Delivery_order_model->read_data_for_do($obj->class_no);
            $this->Delivery_order_model->create_data($parameters);
        }

        $this->benchmark->mark('code_end');

        $data['message'] = "Finish migrate document DO";
        $data['execution_time'] = round($this->benchmark->elapsed_time('code_start', 'code_end')) . " seconds";
        $this->response($data, REST_Controller::HTTP_OK);
    }

    /**
     *  @OA\Post(
     *      path="/delivery_order/create",
     *      tags={"Delivery Order"},
     *      summary="Create product and generate document delivery order",
     *      description="",
     *      operationId="createDocument",
     *      @OA\RequestBody(
     *          description="List of data object",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(
     *                          property="store_no",
     *                          description="",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="dept_no",
     *                          description="",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="class_no",
     *                          description="",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="article_vendor",
     *                          description="",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="style",
     *                          description="",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="sku_promo",
     *                          description="",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="exp_promo",
     *                          description="",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="category_no",
     *                          description="",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="size_no",
     *                          description="",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="color_no",
     *                          description="",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="material_no",
     *                          description="",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="season_code",
     *                          description="",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="retail",
     *                          description="",
     *                          type="integer"
     *                      ),
     *                      @OA\Property(
     *                          property="tag_type",
     *                          description="",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="image",
     *                          description="",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="qty",
     *                          description="",
     *                          type="integer"
     *                      ),
     *                      @OA\Property(
     *                          property="nik",
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
    public function create_post()
    {
        $this->benchmark->mark('code_start');

        $parameters = $this->post();

        if (!empty($parameters)) {
            $validate_param = $this->validate_parameter_create($parameters[0]);

            if ($validate_param->run() == TRUE) {
                $save = $this->Delivery_order_model->create_data($parameters);

                if (!empty($save) and $save['total_affected'] > 0) {
                    $this->benchmark->mark('code_end');

                    $output['status'] = REST_Controller::HTTP_OK;
                    $output['error'] = false;
                    $output['message'] = "Success create delivery order list.";
                    $output['data'] = $save['data'];
                    $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
                    $this->response($output, REST_Controller::HTTP_OK);
                } else {
                    $this->benchmark->mark('code_end');

                    $output['status'] = REST_Controller::HTTP_NOT_MODIFIED;
                    $output['error'] = true;
                    $output['error_detail'] = $save['error'];
                    $output['message'] = "Failed create delivery order list!";
                    $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
                    $this->response($output, REST_Controller::HTTP_NOT_MODIFIED);
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

    /**
     *  @OA\Post(
     *      path="/delivery_order/update",
     *      tags={"Delivery Order"},
     *      summary="Update status and create mutation when doc_status = 6",
     *      description="",
     *      operationId="updateDocument",
     *      @OA\RequestBody(
     *          description="List of data object",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(
     *                          property="store_no",
     *                          description="",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="sku_no",
     *                          description="",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="status_old",
     *                          description="",
     *                          type="integer"
     *                      ),
     *                      @OA\Property(
     *                          property="status_new",
     *                          description="",
     *                          type="integer"
     *                      ),
     *                      @OA\Property(
     *                          property="status_item",
     *                          description="",
     *                          type="integer"
     *                      ),
     *                      @OA\Property(
     *                          property="qty_last_receive",
     *                          description="",
     *                          type="integer"
     *                      ),
     *                      @OA\Property(
     *                          property="nik",
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
                $update = $this->Delivery_order_model->update_data($parameters);
                
                if (!empty($update) and $update['total_affected'] > 0) {
                    $this->benchmark->mark('code_end');

                    $output['status'] = REST_Controller::HTTP_OK;
                    $output['error'] = false;
                    $output['message'] = "Success update delivery order list.";
                    $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
                    $this->response($output, REST_Controller::HTTP_OK);
                } else {
                    $this->benchmark->mark('code_end');

                    $output['status'] = REST_Controller::HTTP_NOT_FOUND;
                    $output['error'] = true;
                    $output['error_detail'] = $update['error'];
                    $output['message'] = "Failed update delivery order list!";
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

    /**
     *  @OA\Post(
     *      path="/delivery_order/delete",
     *      tags={"Delivery Order"},
     *      summary="Delete records from trans_do",
     *      description="",
     *      operationId="deleteDocument",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="doc_no",
     *                      description="",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="store_no",
     *                      description="",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="sku_no",
     *                      description="",
     *                      type="string"
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
    public function delete_post()
    {
        $this->benchmark->mark('code_start');

        $parameters = $this->post();

        $validate_param = $this->validate_parameter_delete($parameters);

        if ($validate_param->run() == TRUE) {
            $delete = $this->Delivery_order_model->delete_data($parameters);

            if (!empty($delete) and $delete['total_affected'] > 0) {
                $this->benchmark->mark('code_end');

                $output['status'] = REST_Controller::HTTP_OK;
                $output['error'] = false;
                $output['message'] = "Success delete delivery order list.";
                $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
                $this->response($output, REST_Controller::HTTP_OK);
            } else {
                $this->benchmark->mark('code_end');

                $output['status'] = REST_Controller::HTTP_NOT_MODIFIED;
                $output['error'] = true;
                $output['error_detail'] = $delete['error'];
                $output['message'] = "Failed delete delivery order list!";
                $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
                $this->response($output, REST_Controller::HTTP_NOT_MODIFIED);
            }
        } else {
            $this->benchmark->mark('code_end');

            $output['status'] = REST_Controller::HTTP_UNPROCESSABLE_ENTITY;
            $output['error'] = true;
            $output['error_detail'] = $validate_param->error_array();
            $output['message'] = "Required parameters!";
            $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
            $this->response($output, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    function validate_parameter_create($parameters)
    {
        $this->form_validation->set_data($parameters);
        $this->form_validation->set_rules('store_no', 'store_no', 'required');
        $this->form_validation->set_rules('dept_no', 'dept_no', 'required');
        $this->form_validation->set_rules('class_no', 'class_no', 'required');
        $this->form_validation->set_rules('article_vendor', 'article_vendor', 'required');
        $this->form_validation->set_rules('style', 'style', 'required');
        $this->form_validation->set_rules('sku_promo', 'sku_promo', 'required');
        //$this->form_validation->set_rules('exp_promo', 'exp_promo', 'required');
        $this->form_validation->set_rules('category_no', 'category_no', 'required');
        $this->form_validation->set_rules('size_no', 'size_no', 'required');
        $this->form_validation->set_rules('color_no', 'color_no', 'required');
        $this->form_validation->set_rules('material_no', 'material_no', 'required');
        $this->form_validation->set_rules('retail', 'retail', 'required');
        $this->form_validation->set_rules('tag_type', 'tag_type', 'required');
        //$this->form_validation->set_rules('image', 'image', 'required');
        $this->form_validation->set_rules('qty', 'qty', 'required');
        $this->form_validation->set_rules('nik', 'nik', 'required');
        //$this->form_validation->set_rules('note', 'note', 'required');

        return $this->form_validation;
    }

    function validate_parameter_update($parameters)
    {
        $this->form_validation->set_data($parameters);
        $this->form_validation->set_rules('doc_no', 'doc_no', 'required');
        // $this->form_validation->set_rules('store_no', 'store_no', 'required');
        // $this->form_validation->set_rules('sku_no', 'sku_no', 'required');
        $this->form_validation->set_rules('status_old', 'status_old', 'required');
        // $this->form_validation->set_rules('status_new', 'status_new', 'required');
        // $this->form_validation->set_rules('status_item', 'status_item', 'required');
        // $this->form_validation->set_rules('qty_last_receive', 'qty_last_receive', 'required');
        // $this->form_validation->set_rules('nik', 'nik', 'required');

        return $this->form_validation;
    }

    function validate_parameter_delete($parameters)
    {
        $this->form_validation->set_data($parameters);
        $this->form_validation->set_rules('doc_no', 'doc_no', 'required');
        // $this->form_validation->set_rules('store_no', 'store_no', 'required');
        // $this->form_validation->set_rules('sku_no', 'sku_no', 'required');

        return $this->form_validation;
    }
}
