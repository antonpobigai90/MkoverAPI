<?php
require './db.class.php';

class DemoNoti extends DB {

    function noti_demo() {
        $ud_id = "b5e6403a12b93265ee511627830cc5f083a532f2573607ba3ba8227f5e21125e";
        
        $msg = array('message' => 'Demo notification from Bookmwah');
        
        $alert = "Demo notification from Bookmwah";
        
//        $this->send_notification_iphone($ud_id, $msg, $alert);
        $this->push_iOS($ud_id, $msg, $alert);
    }

}

$demo = new DemoNoti();
$demo->noti_demo();