<?php
require './db.class.php';

class SendMessage extends DB {

    function insert_chat_detail() {
        if(!empty($_REQUEST['receive_id']) && !empty($_REQUEST['sender_id']) && !empty($_REQUEST['message'])){

            $date_time = date('Y-m-d H:i:s');            
            $data = array('receive_id' => (int)$_REQUEST['receive_id'], 'sender_id' => $_REQUEST['sender_id'], 'message' => $_REQUEST['message'], 'date_time' => $date_time);
            //print_r($data);
            $chat_id = $this->insert_records('chat_messages', $data);

            $infor = array('u_id' => $_REQUEST['sender_id']);
            $details = $this->get_record_where('users', $infor);
            $userDetails = $details[0]; 
            
            $device_token = array('u_id' => $_REQUEST['receive_id']);
            $tokens = $this->get_record_where('users', $device_token);
            $token = $tokens[0];
                      
            $data= array("aps" => array(
                "alert" => $_REQUEST['message'],
                "chat_id" => $chat_id,
                "sender_id" => $_REQUEST['sender_id'],
                "receiver_id" => $_REQUEST['receive_id'],
                "username" => $userDetails['u_fullname'],
                "photo_url" => $userDetails['u_image'],
                "type" => "message"
            ));  
            
	    $this -> push($token['u_device_id'], $data);
                           
            echo json_encode(array("status" => "true"));                             
        } else {
            $response = array('status' => 'false', 'message' => 'Invalid request parameter');
        }
        $this->json_output($response);
    }

    function push($deviceToken, $body = array()) {
        $deviceToken = strtolower(str_replace(array(" ", "-", "_"), array("", "", ""), $deviceToken));

        // Put your private key's passphrase here:
        $passphrase = '123456';

        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', 'ckdist.pem');

        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
        stream_context_set_option($ctx, 'ssl', 'cafile', 'entrust_2048_ca.cer');


        //$fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);

        $fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
        
        if (!$fp) {
            return false;
        }

        // Encode the payload as JSON
        $payload = json_encode($body);

        // Build the binary notification
        $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

        // Send it to the server
        $result = fwrite($fp, $msg, strlen($msg));

        if (!$result)
            return false;

        // Close the connection to the server
        fclose($fp);
    }
}

$send_message = new SendMessage();
$send_message->insert_chat_detail();