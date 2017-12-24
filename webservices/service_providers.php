<?php
require './db.class.php';

class ServiceProvider extends DB {

    function provider_list() {
        if($_REQUEST['cat_id']){
            $category = $_REQUEST['cat_id'];
            $sql = "SELECT pro_id, pro_u_id, pro_name, pro_image, pro_rating, pro_house_call FROM service_provider WHERE pro_status = 1 AND pro_id IN (SELECT s_provider FROM services WHERE s_status = 1 AND s_category = $category GROUP BY s_provider) ORDER BY pro_name";
            $get_provider = $this->query_result($sql);
            if(!empty($get_provider)){
                foreach ($get_provider as $value) {
                    $where_img = array('u_id' => $value['pro_u_id']);
                    $img = $this->get_record_where('users', $where_img, 'u_image, u_type');
                    $image = $img[0]['u_image'];
                    $u_type = $img[0]['u_type'];

                    if(!empty($image) && $u_type == 1)
                        $value['pro_image'] = $this->baseurl.UPLOADS.'/user_image/'.$image;
                    elseif (!empty($image) && $u_type == 2)
                        $value['pro_image'] = $image;
                    else
                        $value['pro_image'] = '';
                    
//                    $value['pro_image'] = $this->baseurl.UPLOADS.'/provider_image/'.$value['pro_image'];
                    $providers[] = $value;
                }
                $response = array('status' => 'true', 'message' => 'Service providers found', 'providers' => $providers);
            } else {
                $response = array('status' => 'false', 'message' => 'No service provider found');
            }
        } else {
            $response = array('status' => 'false', 'message' => 'Invalid request parameter');
        }
        $this->json_output($response);
    }

}

$providers = new ServiceProvider();
$providers->provider_list();