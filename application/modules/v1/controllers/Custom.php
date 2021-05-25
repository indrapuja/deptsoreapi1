<?php

defined('BASEPATH') or exit('No direct script access allowed');

ini_set('max_execution_time', 0);

require APPPATH . 'libraries/REST_Controller.php';

class Custom extends REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        
    }

    public function send_notice_to_supplier_get()
    {
        $error = 0;
        $success = 0;

		$this->load->database('informix');
		$execute = $this->db->query("select * from cred_mail19 order by email_add asc");
		$list_email = $execute->result();

        foreach ($list_email as $email) {
            $this->email->clear(TRUE);

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

            $this->email->initialize($config);

            $this->email->from('no-reply@metroindonesia.com', 'METRO Dept Store Indonesia');
            $this->email->to(trim(strtolower($email->email_add)));
            $this->email->cc('fina@metroindonesia.com');
            $this->email->bcc(array('achmad.hafizh@metroindonesia.com', 'lina@metroindonesia.com', 'irawan@metroindonesia.com'));

            $this->email->subject("Pemberitahuan Penutupan Sementara Head Office Metro");
            $this->email->message("Dear all supplier, 
                                   <br><br>
                                   Terlampir surat pemberitahuan penutupan sementara Head Office Metro. 
                                   <br>
                                   Mohon maaf atas ketidaknyamanannya.
                                   <br><br>
                                   Terima kasih atas perhatian dan kerjasamanya.
                                   <br><br>
                                   Regards,
                                   <br>
                                   Fina");

            $this->email->attach("./uploads/custom/Surat All Supplier Metro.pdf");

            if ($this->email->send()) {
                // generate success
                $this->db->query("update cred_mail19 set status = 'TERKIRIM' where email_add = '" . $email->email_add."'");
                $success++;
            } else {
                // generate error
                $this->db->query("update cred_mail19 set status = 'GAGAL' where email_add = '" . $email->email_add."'");
                $error++;
            }
        }

        echo "Finish send notice with Error: $error records and Success: $success records";
        
    }
}
