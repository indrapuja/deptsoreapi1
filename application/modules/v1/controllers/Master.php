<?php

defined('BASEPATH') or exit('No direct script access allowed');

ini_set('max_execution_time', 0);

require APPPATH . 'libraries/REST_Controller.php';

/**
 *  @OA\OpenApi(
 *      @OA\Info(
 *          version="2",
 *          title="Master",
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
class Master extends REST_Controller
{

    function __construct()
    {
        parent::__construct();

        //$this->_check_jwt();
        $this->load->model('Master_model');
        $this->generate_documentation($this->router->fetch_class());
    }

    /**
     *  @OA\Post(
     *      path="/Master/create_conversion_category",
     *      tags={"Master"},
     *      summary="Create conversion category",
     *      description="",
     *      operationId="createConversionCategory",
     *      @OA\RequestBody(
     *          description="List of data object",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(
     *                          property="class_no",
     *                          description="",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="category_vendor",
     *                          description="",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="category_metro",
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
    public function create_conversion_category_post()
    {
        $this->benchmark->mark('code_start');

        $parameters = $this->post();

        $this->form_validation->set_data($parameters[0]);
        $this->form_validation->set_rules('class_no', 'class_no', 'required');
        $this->form_validation->set_rules('category_vendor', 'category_vendor', 'required');
        $this->form_validation->set_rules('category_metro', 'category_metro', 'required');

        if ($this->form_validation->run() == TRUE) {
            $save = $this->Master_model->create_conversion_category($parameters);

            if (!empty($save) and $save['total_affected'] > 0) {
                $this->benchmark->mark('code_end');

                $output['status'] = REST_Controller::HTTP_OK;
                $output['error'] = false;
                $output['message'] = "Success create conversion list.";
                $output['data'] = $save['data'];
                $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
                $this->response($output, REST_Controller::HTTP_OK);
            } else {
                $this->benchmark->mark('code_end');

                $output['status'] = REST_Controller::HTTP_NOT_MODIFIED;
                $output['error'] = true;
                $output['error_detail'] = $save['error'];
                $output['message'] = "Failed create conversion list!";
                $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
                $this->response($output, REST_Controller::HTTP_NOT_MODIFIED);
            }
        } else {
            $this->benchmark->mark('code_end');

            $output['status'] = REST_Controller::HTTP_UNPROCESSABLE_ENTITY;
            $output['error'] = true;
            $output['error_detail'] = $this->form_validation->error_array();
            $output['message'] = "Required JSON Array! [{.....}]";
            $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
            $this->response($output, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     *  @OA\Post(
     *      path="/Master/update_conversion_category",
     *      tags={"Master"},
     *      summary="Update conversion category",
     *      description="",
     *      operationId="updateConversionCategory",
     *      @OA\RequestBody(
     *          description="List of data object",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(
     *                          property="class_no",
     *                          description="",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="category_vendor",
     *                          description="",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="category_metro",
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
    public function update_conversion_category_post()
    {
        $this->benchmark->mark('code_start');

        $parameters = $this->post();

        $this->form_validation->set_data($parameters);
        $this->form_validation->set_rules('class_no', 'class_no', 'required');
        $this->form_validation->set_rules('category_vendor', 'category_vendor', 'required');
        $this->form_validation->set_rules('category_metro', 'category_metro', 'required');

        if ($this->form_validation->run() == TRUE) {
            $save = $this->Master_model->update_conversion_category($parameters);

            if (!empty($save) and $save['total_affected'] > 0) {
                $this->benchmark->mark('code_end');

                $output['status'] = REST_Controller::HTTP_OK;
                $output['error'] = false;
                $output['message'] = "Success update conversion list.";
                $output['data'] = $save['data'];
                $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
                $this->response($output, REST_Controller::HTTP_OK);
            } else {
                $this->benchmark->mark('code_end');

                $output['status'] = REST_Controller::HTTP_NOT_MODIFIED;
                $output['error'] = true;
                $output['error_detail'] = $save['error'];
                $output['message'] = "Failed update conversion list!";
                $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
                $this->response($output, REST_Controller::HTTP_NOT_MODIFIED);
            }
        } else {
            $this->benchmark->mark('code_end');

            $output['status'] = REST_Controller::HTTP_UNPROCESSABLE_ENTITY;
            $output['error'] = true;
            $output['error_detail'] = $this->form_validation->error_array();
            $output['message'] = "Required Form Body Parameters";
            $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
            $this->response($output, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     *  @OA\Post(
     *      path="/Master/delete_conversion_category",
     *      tags={"Master"},
     *      summary="Delete conversion category",
     *      description="",
     *      operationId="deleteConversionCategory",
     *      @OA\RequestBody(
     *          description="List of data object",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(
     *                          property="class_no",
     *                          description="",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="category_vendor",
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
    public function delete_conversion_category_post()
    {
        $this->benchmark->mark('code_start');

        $parameters = $this->post();

        $this->form_validation->set_data($parameters);
        $this->form_validation->set_rules('class_no', 'class_no', 'required');
        $this->form_validation->set_rules('category_vendor', 'category_vendor', 'required');

        if ($this->form_validation->run() == TRUE) {
            $save = $this->Master_model->delete_conversion_category($parameters);

            if (!empty($save) and $save['total_affected'] > 0) {
                $this->benchmark->mark('code_end');

                $output['status'] = REST_Controller::HTTP_OK;
                $output['error'] = false;
                $output['message'] = "Success delete conversion list.";
                $output['data'] = $save['data'];
                $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
                $this->response($output, REST_Controller::HTTP_OK);
            } else {
                $this->benchmark->mark('code_end');

                $output['status'] = REST_Controller::HTTP_NOT_MODIFIED;
                $output['error'] = true;
                $output['error_detail'] = $save['error'];
                $output['message'] = "Failed delete conversion list!";
                $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
                $this->response($output, REST_Controller::HTTP_NOT_MODIFIED);
            }
        } else {
            $this->benchmark->mark('code_end');

            $output['status'] = REST_Controller::HTTP_UNPROCESSABLE_ENTITY;
            $output['error'] = true;
            $output['error_detail'] = $this->form_validation->error_array();
            $output['message'] = "Required Form Body Parameters";
            $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
            $this->response($output, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     *  @OA\Post(
     *      path="/Master/create_conversion_size",
     *      tags={"Master"},
     *      summary="Create conversion size",
     *      description="",
     *      operationId="createConversionSize",
     *      @OA\RequestBody(
     *          description="List of data object",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(
     *                          property="class_no",
     *                          description="",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="size_vendor",
     *                          description="",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="size_metro",
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
    public function create_conversion_size_post()
    {
        $this->benchmark->mark('code_start');

        $parameters = $this->post();

        $this->form_validation->set_data($parameters[0]);
        $this->form_validation->set_rules('class_no', 'class_no', 'required');
        $this->form_validation->set_rules('size_vendor', 'size_vendor', 'required');
        $this->form_validation->set_rules('size_metro', 'size_metro', 'required');

        if ($this->form_validation->run() == TRUE) {
            $save = $this->Master_model->create_conversion_size($parameters);

            if (!empty($save) and $save['total_affected'] > 0) {
                $this->benchmark->mark('code_end');

                $output['status'] = REST_Controller::HTTP_OK;
                $output['error'] = false;
                $output['message'] = "Success create conversion list.";
                $output['data'] = $save['data'];
                $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
                $this->response($output, REST_Controller::HTTP_OK);
            } else {
                $this->benchmark->mark('code_end');

                $output['status'] = REST_Controller::HTTP_NOT_MODIFIED;
                $output['error'] = true;
                $output['error_detail'] = $save['error'];
                $output['message'] = "Failed create conversion list!";
                $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
                $this->response($output, REST_Controller::HTTP_NOT_MODIFIED);
            }
        } else {
            $this->benchmark->mark('code_end');

            $output['status'] = REST_Controller::HTTP_UNPROCESSABLE_ENTITY;
            $output['error'] = true;
            $output['error_detail'] = $this->form_validation->error_array();
            $output['message'] = "Required JSON Array! [{.....}]";
            $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
            $this->response($output, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     *  @OA\Post(
     *      path="/Master/update_conversion_size",
     *      tags={"Master"},
     *      summary="Update conversion size",
     *      description="",
     *      operationId="updateConversionSize",
     *      @OA\RequestBody(
     *          description="List of data object",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(
     *                          property="class_no",
     *                          description="",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="size_vendor",
     *                          description="",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="size_metro",
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
    public function update_conversion_size_post()
    {
        $this->benchmark->mark('code_start');

        $parameters = $this->post();

        $this->form_validation->set_data($parameters);
        $this->form_validation->set_rules('class_no', 'class_no', 'required');
        $this->form_validation->set_rules('size_vendor', 'size_vendor', 'required');
        $this->form_validation->set_rules('size_metro', 'size_metro', 'required');

        if ($this->form_validation->run() == TRUE) {
            $save = $this->Master_model->update_conversion_size($parameters);

            if (!empty($save) and $save['total_affected'] > 0) {
                $this->benchmark->mark('code_end');

                $output['status'] = REST_Controller::HTTP_OK;
                $output['error'] = false;
                $output['message'] = "Success update conversion list.";
                $output['data'] = $save['data'];
                $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
                $this->response($output, REST_Controller::HTTP_OK);
            } else {
                $this->benchmark->mark('code_end');

                $output['status'] = REST_Controller::HTTP_NOT_MODIFIED;
                $output['error'] = true;
                $output['error_detail'] = $save['error'];
                $output['message'] = "Failed update conversion list!";
                $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
                $this->response($output, REST_Controller::HTTP_NOT_MODIFIED);
            }
        } else {
            $this->benchmark->mark('code_end');

            $output['status'] = REST_Controller::HTTP_UNPROCESSABLE_ENTITY;
            $output['error'] = true;
            $output['error_detail'] = $this->form_validation->error_array();
            $output['message'] = "Required Form Body Parameters";
            $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
            $this->response($output, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     *  @OA\Post(
     *      path="/Master/delete_conversion_size",
     *      tags={"Master"},
     *      summary="Delete conversion size",
     *      description="",
     *      operationId="deleteConversionSize",
     *      @OA\RequestBody(
     *          description="List of data object",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(
     *                          property="class_no",
     *                          description="",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="size_vendor",
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
    public function delete_conversion_size_post()
    {
        $this->benchmark->mark('code_start');

        $parameters = $this->post();

        $this->form_validation->set_data($parameters);
        $this->form_validation->set_rules('class_no', 'class_no', 'required');
        $this->form_validation->set_rules('size_vendor', 'size_vendor', 'required');

        if ($this->form_validation->run() == TRUE) {
            $save = $this->Master_model->delete_conversion_size($parameters);

            if (!empty($save) and $save['total_affected'] > 0) {
                $this->benchmark->mark('code_end');

                $output['status'] = REST_Controller::HTTP_OK;
                $output['error'] = false;
                $output['message'] = "Success delete conversion list.";
                $output['data'] = $save['data'];
                $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
                $this->response($output, REST_Controller::HTTP_OK);
            } else {
                $this->benchmark->mark('code_end');

                $output['status'] = REST_Controller::HTTP_NOT_MODIFIED;
                $output['error'] = true;
                $output['error_detail'] = $save['error'];
                $output['message'] = "Failed delete conversion list!";
                $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
                $this->response($output, REST_Controller::HTTP_NOT_MODIFIED);
            }
        } else {
            $this->benchmark->mark('code_end');

            $output['status'] = REST_Controller::HTTP_UNPROCESSABLE_ENTITY;
            $output['error'] = true;
            $output['error_detail'] = $this->form_validation->error_array();
            $output['message'] = "Required Form Body Parameters";
            $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
            $this->response($output, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     *  @OA\Post(
     *      path="/Master/create_conversion_color",
     *      tags={"Master"},
     *      summary="Create conversion color",
     *      description="",
     *      operationId="createConversionColor",
     *      @OA\RequestBody(
     *          description="List of data object",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(
     *                          property="class_no",
     *                          description="",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="color_vendor",
     *                          description="",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="color_metro",
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
    public function create_conversion_color_post()
    {
        $this->benchmark->mark('code_start');

        $parameters = $this->post();

        $this->form_validation->set_data($parameters[0]);
        $this->form_validation->set_rules('class_no', 'class_no', 'required');
        $this->form_validation->set_rules('color_vendor', 'color_vendor', 'required');
        $this->form_validation->set_rules('color_metro', 'color_metro', 'required');

        if ($this->form_validation->run() == TRUE) {
            $save = $this->Master_model->create_conversion_color($parameters);

            if (!empty($save) and $save['total_affected'] > 0) {
                $this->benchmark->mark('code_end');

                $output['status'] = REST_Controller::HTTP_OK;
                $output['error'] = false;
                $output['message'] = "Success create conversion list.";
                $output['data'] = $save['data'];
                $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
                $this->response($output, REST_Controller::HTTP_OK);
            } else {
                $this->benchmark->mark('code_end');

                $output['status'] = REST_Controller::HTTP_NOT_MODIFIED;
                $output['error'] = true;
                $output['error_detail'] = $save['error'];
                $output['message'] = "Failed create conversion list!";
                $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
                $this->response($output, REST_Controller::HTTP_NOT_MODIFIED);
            }
        } else {
            $this->benchmark->mark('code_end');

            $output['status'] = REST_Controller::HTTP_UNPROCESSABLE_ENTITY;
            $output['error'] = true;
            $output['error_detail'] = $this->form_validation->error_array();
            $output['message'] = "Required JSON Array! [{.....}]";
            $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
            $this->response($output, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     *  @OA\Post(
     *      path="/Master/update_conversion_color",
     *      tags={"Master"},
     *      summary="Update conversion color",
     *      description="",
     *      operationId="updateConversionColor",
     *      @OA\RequestBody(
     *          description="List of data object",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(
     *                          property="class_no",
     *                          description="",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="color_vendor",
     *                          description="",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="color_metro",
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
    public function update_conversion_color_post()
    {
        $this->benchmark->mark('code_start');

        $parameters = $this->post();

        $this->form_validation->set_data($parameters);
        $this->form_validation->set_rules('class_no', 'class_no', 'required');
        $this->form_validation->set_rules('color_vendor', 'color_vendor', 'required');
        $this->form_validation->set_rules('color_metro', 'color_metro', 'required');

        if ($this->form_validation->run() == TRUE) {
            $save = $this->Master_model->update_conversion_color($parameters);

            if (!empty($save) and $save['total_affected'] > 0) {
                $this->benchmark->mark('code_end');

                $output['status'] = REST_Controller::HTTP_OK;
                $output['error'] = false;
                $output['message'] = "Success update conversion list.";
                $output['data'] = $save['data'];
                $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
                $this->response($output, REST_Controller::HTTP_OK);
            } else {
                $this->benchmark->mark('code_end');

                $output['status'] = REST_Controller::HTTP_NOT_MODIFIED;
                $output['error'] = true;
                $output['error_detail'] = $save['error'];
                $output['message'] = "Failed update conversion list!";
                $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
                $this->response($output, REST_Controller::HTTP_NOT_MODIFIED);
            }
        } else {
            $this->benchmark->mark('code_end');

            $output['status'] = REST_Controller::HTTP_UNPROCESSABLE_ENTITY;
            $output['error'] = true;
            $output['error_detail'] = $this->form_validation->error_array();
            $output['message'] = "Required Form Body Parameters";
            $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
            $this->response($output, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     *  @OA\Post(
     *      path="/Master/delete_conversion_color",
     *      tags={"Master"},
     *      summary="Delete conversion color",
     *      description="",
     *      operationId="deleteConversionColor",
     *      @OA\RequestBody(
     *          description="List of data object",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(
     *                          property="class_no",
     *                          description="",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="color_vendor",
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
    public function delete_conversion_color_post()
    {
        $this->benchmark->mark('code_start');

        $parameters = $this->post();

        $this->form_validation->set_data($parameters);
        $this->form_validation->set_rules('class_no', 'class_no', 'required');
        $this->form_validation->set_rules('color_vendor', 'color_vendor', 'required');

        if ($this->form_validation->run() == TRUE) {
            $save = $this->Master_model->delete_conversion_color($parameters);

            if (!empty($save) and $save['total_affected'] > 0) {
                $this->benchmark->mark('code_end');

                $output['status'] = REST_Controller::HTTP_OK;
                $output['error'] = false;
                $output['message'] = "Success delete conversion list.";
                $output['data'] = $save['data'];
                $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
                $this->response($output, REST_Controller::HTTP_OK);
            } else {
                $this->benchmark->mark('code_end');

                $output['status'] = REST_Controller::HTTP_NOT_MODIFIED;
                $output['error'] = true;
                $output['error_detail'] = $save['error'];
                $output['message'] = "Failed delete conversion list!";
                $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
                $this->response($output, REST_Controller::HTTP_NOT_MODIFIED);
            }
        } else {
            $this->benchmark->mark('code_end');

            $output['status'] = REST_Controller::HTTP_UNPROCESSABLE_ENTITY;
            $output['error'] = true;
            $output['error_detail'] = $this->form_validation->error_array();
            $output['message'] = "Required Form Body Parameters";
            $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
            $this->response($output, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     *  @OA\Post(
     *      path="/Master/create_conversion_material",
     *      tags={"Master"},
     *      summary="Create conversion material",
     *      description="",
     *      operationId="createConversionMaterial",
     *      @OA\RequestBody(
     *          description="List of data object",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(
     *                          property="class_no",
     *                          description="",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="material_vendor",
     *                          description="",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="material_metro",
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
    public function create_conversion_material_post()
    {
        $this->benchmark->mark('code_start');

        $parameters = $this->post();

        $this->form_validation->set_data($parameters[0]);
        $this->form_validation->set_rules('class_no', 'class_no', 'required');
        $this->form_validation->set_rules('material_vendor', 'material_vendor', 'required');
        $this->form_validation->set_rules('material_metro', 'material_metro', 'required');

        if ($this->form_validation->run() == TRUE) {
            $save = $this->Master_model->create_conversion_material($parameters);

            if (!empty($save) and $save['total_affected'] > 0) {
                $this->benchmark->mark('code_end');

                $output['status'] = REST_Controller::HTTP_OK;
                $output['error'] = false;
                $output['message'] = "Success create conversion list.";
                $output['data'] = $save['data'];
                $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
                $this->response($output, REST_Controller::HTTP_OK);
            } else {
                $this->benchmark->mark('code_end');

                $output['status'] = REST_Controller::HTTP_NOT_MODIFIED;
                $output['error'] = true;
                $output['error_detail'] = $save['error'];
                $output['message'] = "Failed create conversion list!";
                $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
                $this->response($output, REST_Controller::HTTP_NOT_MODIFIED);
            }
        } else {
            $this->benchmark->mark('code_end');

            $output['status'] = REST_Controller::HTTP_UNPROCESSABLE_ENTITY;
            $output['error'] = true;
            $output['error_detail'] = $this->form_validation->error_array();
            $output['message'] = "Required JSON Array! [{.....}]";
            $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
            $this->response($output, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     *  @OA\Post(
     *      path="/Master/update_conversion_material",
     *      tags={"Master"},
     *      summary="Update conversion material",
     *      description="",
     *      operationId="updateConversionMaterial",
     *      @OA\RequestBody(
     *          description="List of data object",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(
     *                          property="class_no",
     *                          description="",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="material_vendor",
     *                          description="",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="material_metro",
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
    public function update_conversion_material_post()
    {
        $this->benchmark->mark('code_start');

        $parameters = $this->post();

        $this->form_validation->set_data($parameters);
        $this->form_validation->set_rules('class_no', 'class_no', 'required');
        $this->form_validation->set_rules('material_vendor', 'material_vendor', 'required');
        $this->form_validation->set_rules('material_metro', 'material_metro', 'required');

        if ($this->form_validation->run() == TRUE) {
            $save = $this->Master_model->update_conversion_material($parameters);

            if (!empty($save) and $save['total_affected'] > 0) {
                $this->benchmark->mark('code_end');

                $output['status'] = REST_Controller::HTTP_OK;
                $output['error'] = false;
                $output['message'] = "Success update conversion list.";
                $output['data'] = $save['data'];
                $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
                $this->response($output, REST_Controller::HTTP_OK);
            } else {
                $this->benchmark->mark('code_end');

                $output['status'] = REST_Controller::HTTP_NOT_MODIFIED;
                $output['error'] = true;
                $output['error_detail'] = $save['error'];
                $output['message'] = "Failed update conversion list!";
                $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
                $this->response($output, REST_Controller::HTTP_NOT_MODIFIED);
            }
        } else {
            $this->benchmark->mark('code_end');

            $output['status'] = REST_Controller::HTTP_UNPROCESSABLE_ENTITY;
            $output['error'] = true;
            $output['error_detail'] = $this->form_validation->error_array();
            $output['message'] = "Required Form Body Parameters";
            $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
            $this->response($output, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     *  @OA\Post(
     *      path="/Master/delete_conversion_material",
     *      tags={"Master"},
     *      summary="Delete conversion material",
     *      description="",
     *      operationId="deleteConversionMaterial",
     *      @OA\RequestBody(
     *          description="List of data object",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(
     *                          property="class_no",
     *                          description="",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="material_vendor",
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
    public function delete_conversion_material_post()
    {
        $this->benchmark->mark('code_start');

        $parameters = $this->post();

        $this->form_validation->set_data($parameters);
        $this->form_validation->set_rules('class_no', 'class_no', 'required');
        $this->form_validation->set_rules('material_vendor', 'material_vendor', 'required');

        if ($this->form_validation->run() == TRUE) {
            $save = $this->Master_model->delete_conversion_material($parameters);

            if (!empty($save) and $save['total_affected'] > 0) {
                $this->benchmark->mark('code_end');

                $output['status'] = REST_Controller::HTTP_OK;
                $output['error'] = false;
                $output['message'] = "Success delete conversion list.";
                $output['data'] = $save['data'];
                $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
                $this->response($output, REST_Controller::HTTP_OK);
            } else {
                $this->benchmark->mark('code_end');

                $output['status'] = REST_Controller::HTTP_NOT_MODIFIED;
                $output['error'] = true;
                $output['error_detail'] = $save['error'];
                $output['message'] = "Failed delete conversion list!";
                $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
                $this->response($output, REST_Controller::HTTP_NOT_MODIFIED);
            }
        } else {
            $this->benchmark->mark('code_end');

            $output['status'] = REST_Controller::HTTP_UNPROCESSABLE_ENTITY;
            $output['error'] = true;
            $output['error_detail'] = $this->form_validation->error_array();
            $output['message'] = "Required Form Body Parameters";
            $output['execution_time'] = $this->benchmark->elapsed_time('code_start', 'code_end') . " seconds.";
            $this->response($output, REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
