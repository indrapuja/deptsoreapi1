<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('send_mail')) {
    function send_mail($email, $alias = null, $cc = null, $bcc = null, $subject, $message)
    {
        $ci = &get_instance();
        $ci->load->library('email');

        $ci->email->clear();

        $config['protocol'] = 'smtp';
        $config['charset'] = 'utf-8';
        $config['newline'] = '\r\n';
        $config['crlf'] = '\n';
        $config['smtp_crypto'] = 'ssl';
        $config['smtp_host'] = SMTP_HOST;
        $config['smtp_port'] = SMTP_PORT;
        $config['smtp_user'] = SMTP_USER;
        $config['smtp_pass'] = SMTP_PASS;
        $config['mailtype'] = 'html';

        $ci->email->initialize($config);

        if (empty($alias)) {
            $alias = "KawanDigital.id Platform";
        }

        $ci->email->from('noreply@kawandigital.id', $alias);
        $ci->email->to($email);

        if (!empty($cc)) {
            $ci->email->cc($cc);
        }

        if (!empty($bcc)) {
            $ci->email->bcc($bcc);
        }

        $ci->email->subject($subject);
        $ci->email->message($message);

        return $ci->email->send();
    }
}

if (!function_exists('format_output_select')) {
    function format_output_select($count, $data)
    {
        $output = array();
        $output['total_records'] = $count;
        $output['data'] = $data;

        return $output;
    }
}

if (!function_exists('format_output')) {
    function format_output($affected, $error, $data = null)
    {
        $output = array();
        $output['total_affected'] = $affected;
        $output['error'] = $error;

        if (!empty($data)) {
            $output['data'] = $data;
        }

        return $output;
    }
}

if (!function_exists('addDoubleQuotes')) {
    function addDoubleQuotes($string)
    {
        return '"' . $string . '"';
    }
}

if (!function_exists('addSingleQuotes')) {
    function addSingleQuotes($string)
    {
        return "'" . $string . "'";
    }
}

if (!function_exists('addDoubleQuotesWhereDelimiter')) {
    function addDoubleQuotesWhereDelimiter($delimiter, $string)
    {
        $new_string = "";
        $listString = explode($delimiter, $string);
        foreach ($listString as $row) {
            $new_string .= '"' . $row . '",';
        }

        return $new_string;
    }
}

if (!function_exists('addSingleQuotesWhereDelimiter')) {
    function addSingleQuotesWhereDelimiter($delimiter, $string)
    {
        $new_string = "";
        $listString = explode($delimiter, $string);
        foreach ($listString as $row) {
            $new_string .= "'" . $row . "',";
        }

        return $new_string;
    }
}

if (!function_exists('search_in_string')) {
    function search_in_string($delimiter, $string, $search_value)
    {
        $listString = explode($delimiter, $string);

        if (in_array($search_value, $listString)) {
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('remove_duplicate_string')) {
    function remove_duplicate_string($delimiter, $string)
    {
        $arrGroupList = explode($delimiter, substr($string, 0, -1));
        $strUniqueGroup = "";

        foreach (array_unique($arrGroupList) as $row) {
            $strUniqueGroup .= $row . ",";
        }

        return substr($strUniqueGroup, 0, -1);
    }
}