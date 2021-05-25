<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

/**
 * Description of Test
 *
 * @author Achmad Hafizh
 */
class Test extends REST_Controller
{

    function __construct()
    {
        parent::__construct();
        // $this->_check_jwt();

        // $this->load->model('Test_model'); 
    }

    public function test_get()
    {
        $this->load->library('encryption');

        $plain_text = '123456';
        $ciphertext = $this->encryption->encrypt($plain_text);

        echo $ciphertext;
        echo "<br>";
        echo $this->encryption->decrypt($ciphertext);
    }

    public function test_pusher_publish_get()
    {
        $options = array(
            'cluster' => PUSHER_CLUSTER,
            'useTLS' => true
        );
        $pusher = new Pusher\Pusher(
            PUSHER_KEY,
            PUSHER_SECRET,
            PUSHER_APP_ID,
            $options
        );

        $data['message'] = 'hello world';
        $pusher->trigger('my-channel', 'my-event', $data);
    }

    public function test_pubsub_publish_get()
    {
        $this->load->library('pubsub');

        $payload = json_encode(
            array(
                'timestamp' => date('Y-m-d H:i:s'),
                'message' => "This is notification from Test modules, please check this data",
                'data' => array(
                    'promo_no' => "PR1068202007221",
                    'promo_mechanism' => "Diskon 50%",
                    'sku_promo' => "95"
                )
            )
        );

        $this->pubsub->publish('force.logout', $payload);
        
        echo 'Message published' . PHP_EOL;

        $this->test_pubsub_subscribe_get();
    }

    public function test_pubsub_subscribe_get()
    {
        $this->load->library('pubsub');

        $subscription = $this->pubsub->subscribe('force.logout.web');

        foreach ($subscription->pull() as $message) {
            echo $message->data() . "\n";
            echo $message->attribute('location');
            $subscription->acknowledge($message);
        }
    }

}
