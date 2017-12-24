<?php
require './db.class.php';

class RemoveService extends DB {

    function remove_service() {
        if(!empty($_REQUEST['s_id'])){
            $where = array('s_id' => (int)$_REQUEST['s_id']);
            $data = array('s_status' => 2);
            $update = $this->update_records('services', $data, $where);
            
            $get_services = $this->query_result("SELECT * FROM `services` WHERE s_status = 1 AND s_provider IN (SELECT s_provider FROM services WHERE s_id = ".(int)$_REQUEST['s_id'].")");
            $services = array();
            if(!empty($get_services)){
                $services = $get_services;
            }
            
            $response = array('status' => 'true', 'message' => 'Successfully removed', 'services' => $services);
        } else {
            $response = array('status' => 'false', 'message' => 'Invalid request parameter');
        }
        $this->json_output($response);
    }

}

$remove_sr = new RemoveService();
$remove_sr->remove_service();