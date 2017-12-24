<?php
require './db.class.php';

class Logout extends DB {

    function user_logout() {
        if(!empty($_REQUEST['user_id'])){
            $data = array('u_device_id' => '');
            $where = array('u_id' => (int)$_REQUEST['user_id']);
            $update = $this->update_records('users', $data, $where);
            if($update){
                $response = array('status' => 'true', 'message' => 'Logout Successfully');
            }
            else{
                $response = array('status' => 'false', 'message' => 'Error in insertion');
            }
        } 
        else {
            $response = array('status' => 'false', 'message' => 'Invalid request parameter');
        }
        $this->json_output($response);
    }

}

$logout = new Logout();
$logout->user_logout();
?>