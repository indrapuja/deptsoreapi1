<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Description of CommonUtil
 *
 * @author Achmad Hafizh
 */
class Commonutil
{

    protected $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
    }

    public function send_mail($email, $subject, $message, $alias = null, $bcc = null)
    {
        $this->CI->load->library('email');

        $this->CI->email->clear();

        $config['protocol'] = 'smtp';
        $config['charset'] = 'utf-8';
        $config['newline'] = '\r\n';
        $config['crlf'] = '\n';
        $config['smtp_crypto'] = 'ssl';
        $config['smtp_host'] = SMTP_HOST;
        $config['smtp_port'] = SMTP_PORT;
        $config['smtp_user'] = SMTP_USER;
        $config['smtp_pass'] = base64_decode(SMTP_PASS);
        $config['mailtype'] = 'html';

        $this->CI->email->initialize($config);

        if (empty($alias)) {
            $alias = "METRO Services";
        }

        $this->CI->email->from('no-reply@metroindonesia.com', $alias);
        $this->CI->email->to($email);
        //$this->email->cc('another@another-example.com');

        if (!empty($bcc)) {
            $this->CI->email->bcc($bcc);
        }

        $this->CI->email->subject($subject);
        $this->CI->email->message($message);

        return $this->CI->email->send();
    }

    public function format_output_select($count, $data)
    {
        $output = array();
        $output['total_records'] = $count;
        $output['data'] = $data;

        return $output;
    }

    function format_output($affected, $error, $data = null)
    {
        $output = array();
        $output['total_affected'] = $affected;
        $output['error'] = $error;
        
        if(!empty($data)) {
            $output['data'] = $data;
        }

        return $output;
    }
}