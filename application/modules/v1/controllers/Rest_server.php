<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Rest_server extends MY_Controller {

    public function index() {
        $this->load->view('rest_server');
    }

}