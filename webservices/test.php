<?php

require './Twilio/autoload.php';

// Use the REST API Client to make requests to the Twilio REST API
use Twilio\Rest\Client;

// Your Account SID and Auth Token from twilio.com/console
$sid = 'ACbcd643228cc6ce82c848cbc52d83d260';
$token = '64710f8d15583dba3b8499100e0c9eec';
$client = new Client($sid, $token);

// Use the client to do fun stuff like send text messages!
$client->messages->create(
    // the number you'd like to send the message to
    '+8617604310903',
    array(
        // A Twilio phone number you purchased at twilio.com/console
        'from' => '+19042041550',
        // the body of the text message you'd like to send
        'body' => 'Hey Jenny! Good luck on the bar exam!'
    )
);

?>