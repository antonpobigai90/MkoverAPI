<?php
require './db.class.php';

class DeleteNoti extends DB {

    function delete_notif() {
        if(!empty($_REQUEST['noti_id'])){
            $where = array('noti_id' => (int)$_REQUEST['noti_id']);
            $data = array('noti_delete_status' => 2);
            $update = $this->update_records('notifications', $data, $where);
            $response = array('status' => 'true', 'message' => 'Notification deleted successfully');
        } else {
            $response = array('status' => 'false', 'message' => 'Invalid request parameter');
        }
        $this->json_output($response);
        
    }

}

$del = new DeleteNoti();
$del->delete_notif();
