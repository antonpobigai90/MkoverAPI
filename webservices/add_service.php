<?php
require './db.class.php';

class AddService extends DB {

    function service_add_update() {
        if(!empty($_REQUEST['pro_id']) && !empty($_REQUEST['name']) && !empty($_REQUEST['cost']) && !empty($_REQUEST['description']) && !empty($_REQUEST['category'])){
            
            $data = array('s_name' => $_REQUEST['name'], 's_cost' => $_REQUEST['cost'], 's_description' => $_REQUEST['description'], 's_category' => $_REQUEST['category'], 's_provider' => (int)$_REQUEST['pro_id'], 's_datetime' => date('Y-m-d H:i:s'));
            
            if(!empty($_REQUEST['s_id'])){
                $where = array('s_id' => (int)$_REQUEST['s_id']);
                $update = $this->update_records('services', $data, $where);
                $response = array('status' => 'true', 'message' => 'Service updated successfully');
            } else {
                $insert = $this->insert_records('services', $data);
                $response = array('status' => 'true', 'message' => 'Service added successfully');
            }
        } else {
            $response = array('status' => 'false', 'message' => 'Invalid request parameter');
        }
        $this->json_output($response);
    }

}

$service = new AddService();
$service->service_add_update();