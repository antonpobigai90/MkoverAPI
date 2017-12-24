<?php
require './db.class.php';

class AcceptReject extends DB {

    function accept_reject() {
        if(!empty($_REQUEST['book_id']) && !empty($_REQUEST['status']) && !empty($_REQUEST['u_id'])){
            $booking_id = (int)$_REQUEST['book_id'];
            $status = (int)$_REQUEST['status'];
            if($status == 1 || $status == 2){
                if($status == 1)
                    $accept_status = 3;
                else
                    $accept_status = 4;
                
                $noti_msg = 'Your booking has been accepted';
                
                $noti_status = 1;
                $noti_type = 'accept';
                
                if($status == 2){
                    $noti_msg = 'Your booking has been rejected';
                    
                    $noti_status = 2;
                    $noti_type = 'reject';
                }
                
                $where = array('book_id' => $booking_id);
                $data = array('book_status' => $accept_status);
                $this->update_records('bookings', $data, $where);
                
                $where_user = array('u_id' => $_REQUEST['u_id']);
                $user_details = $this->get_record_where('users', $where_user, 'u_device_id');
                $device_id = $user_details[0]['u_device_id'];
                
                $data = array("aps" => array(
                        "alert" => $noti_msg,
                        "type" => $noti_type,
                        "book_id" => $booking_id
                ));
                    
                $this -> push($device_id, $data); 
                
                $insert_noti = array('noti_u_id' => $_REQUEST['u_id'], 'noti_msg' => $noti_msg, 'noti_book_id' => $booking_id, 'noti_datetime' => date('Y-m-d H:i:s'), 'noti_type' => $noti_status);
                $this->insert_records('notifications', $insert_noti);
                
                $response = array('status' => 'true', 'message' => 'Successfully submitted');
            } else {
                $response = array('status' => 'false', 'message' => 'Invalid status');
            }
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
        return true;
    }
}

$accept_reject = new AcceptReject();
$accept_reject->accept_reject();