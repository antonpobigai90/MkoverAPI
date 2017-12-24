<?php
require './db.class.php';

class ServiceType extends DB {

    function service_type() {
        $where = array('s_cat_status' => 1);
        $service_type = $this->get_record_where('service_category', $where);
        if(!empty($service_type)){
            foreach ($service_type as $value) {
                $value['s_cat_image'] = $this->baseurl.UPLOADS.'/service_category/'.$value['s_cat_image'];
                $types[] = $value;
            }
            $response = array('status' => 'true', 'message' => 'Service category found', 'service_category' => $types);
        } else {
            $response = array('status' => 'false', 'message' => 'No service category found');
        }
        $this->json_output($response);
    }

}

$s_type = new ServiceType();
$s_type->service_type();