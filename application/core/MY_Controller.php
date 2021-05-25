<?php

/**
 * Description of MY_Controller
 *
 * @author achmad.hafizh
 */
class MY_Controller extends MX_Controller
{
    public function generate_documentation($controller_name)
    {
        $openapi = \OpenApi\scan('application/modules/v1/controllers/' . ucfirst($controller_name) . '.php');
        file_put_contents(FCPATH . "swagger-json/". ucfirst($controller_name) .".json", $openapi->toJson());
    }
}