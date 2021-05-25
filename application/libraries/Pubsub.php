<?php

defined('BASEPATH') or exit('No direct script access allowed');

use Google\Cloud\PubSub\PubSubClient;

/**
 * Description of Pubsub
 *
 * @author Achmad Hafizh
 */
class Pubsub
{

    var $pubsub = null;

    public function __construct()
    {
        $this->pubsub = new PubSubClient();

        putenv('GOOGLE_APPLICATION_CREDENTIALS=./assets/custom/credentials/third-diorama-287108-b5d853e09837.json');
    }

    public function publish($topic, $data = null, $attributes = null)
    {
        $topic = $this->pubsub->topic($topic);
        return $topic->publish([
            'data' => $data,
            'attributes' => $attributes
        ]);
    }

    public function subscribe($subscription)
    {
        $subscription = $this->pubsub->subscription($subscription);
        return $subscription;
    }
}
