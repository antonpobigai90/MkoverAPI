<?php
require './db.class.php';

class GetMissedBadge extends DB {

    function getBadge() {
        if(!empty($_REQUEST['account_id'])){
            
            $check_pass = array('r_checked' => '0', 'receive_id' => $_REQUEST['account_id']);
            $details = $this->get_record_where('chat_messages', $check_pass, "count(id) as ct");
            
            $notif_pass = array('noti_status' => '2', 'noti_u_id' => $_REQUEST['account_id'], 'noti_delete_status' => '1');
            $notif_details = $this->get_record_where('notifications', $notif_pass, "count(noti_id) as ct");
            
            $response = array("status"=>"true", "msgBadge"=>$details[0]['ct'], "notiBadge"=>$notif_details[0]['ct']);
            
        } else {
            $response = array('status' => 'false', 'message' => 'Invalid request parameter');
        }
        $this->json_output($response);
    }

}

$p_details = new GetMissedBadge();
$p_details->getBadge();