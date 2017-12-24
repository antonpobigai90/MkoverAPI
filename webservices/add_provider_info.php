<?php

require './db.class.php';

class AddProviderInfo extends DB {

    function add_edit_info() {
        if(!empty($_REQUEST['user_id']) && !empty($_REQUEST['availability_from']) && !empty($_REQUEST['availability_to']) && isset($_REQUEST['house_call'])){
            
            $data = array('pro_u_id' => (int) $_REQUEST['user_id'], 'pro_house_call' => (int) $_REQUEST['house_call'], 'pro_availability_from' => date('H:i:s', strtotime($_REQUEST['availability_from'])), 'pro_availability_to' => date('H:i:s', strtotime($_REQUEST['availability_to'])));
            
            if(!empty($_REQUEST['name']))
                $data['pro_name'] = $_REQUEST['name'];
            
            if(!empty($_REQUEST['business_name']))
                $data['pro_business_name'] = $_REQUEST['business_name'];
            
            if(!empty($_REQUEST['role']))
                $data['pro_role'] = $_REQUEST['role'];
            
            if(!empty($_REQUEST['email']))
                $data['pro_email'] = $_REQUEST['email'];
            
            if(!empty($_REQUEST['mobile_no']))
                $data['pro_mobile_no'] = $_REQUEST['mobile_no'];
            
            if(!empty($_REQUEST['website']))
                $data['pro_website'] = $_REQUEST['website'];
            
            if(!empty($_REQUEST['address']))
                $data['pro_addr'] = $_REQUEST['address'];
            
            if(!empty($_REQUEST['latitude']) && !empty($_REQUEST['longitude'])){
                $data['pro_lat'] = $_REQUEST['latitude'];
                $data['pro_long'] = $_REQUEST['longitude'];
            }
            
            if(!empty($_REQUEST['zipcode']))
                $data['pro_zipcode'] = $_REQUEST['zipcode'];
            
            if(!empty($_REQUEST['state']))
                $data['pro_state'] = $_REQUEST['state'];
            
            if(!empty($_REQUEST['c_policy']))
                $data['pro_cancel_policy'] = $_REQUEST['c_policy'];
            
            if(!empty($_REQUEST['other_notes']))
                $data['pro_other_notes'] = $_REQUEST['other_notes'];
            
            if(!empty($_REQUEST['other_notes']))
                $data['pro_other_notes'] = $_REQUEST['other_notes'];
            
            if(!empty($_REQUEST['city']))
                $data['pro_city'] = $_REQUEST['city'];
            
        
//        if (!empty($_REQUEST['name']) && !empty($_REQUEST['email']) && !empty($_REQUEST['business_name']) && !empty($_REQUEST['role']) && !empty($_REQUEST['address']) && !empty($_REQUEST['latitude']) && !empty($_REQUEST['longitude']) && !empty($_REQUEST['zipcode']) && !empty($_REQUEST['state']) && !empty($_REQUEST['mobile_no']) && !empty($_REQUEST['website']) && !empty($_REQUEST['c_policy']) && !empty($_REQUEST['other_notes']) && isset($_REQUEST['house_call']) && !empty($_REQUEST['user_id']) && !empty($_REQUEST['availability_from']) && !empty($_REQUEST['availability_to'])) {
            
//            $data = array('pro_u_id' => (int) $_REQUEST['user_id'], 'pro_name' => $_REQUEST['name'], 'pro_business_name' => $_REQUEST['business_name'], 'pro_role' => $_REQUEST['role'], 'pro_email' => $_REQUEST['email'], 'pro_mobile_no' => $_REQUEST['mobile_no'], 'pro_website' => $_REQUEST['website'], 'pro_addr' => $_REQUEST['address'], 'pro_lat' => $_REQUEST['latitude'], 'pro_long' => $_REQUEST['longitude'], 'pro_zipcode' => $_REQUEST['zipcode'], 'pro_state' => $_REQUEST['state'], 'pro_cancel_policy' => $_REQUEST['c_policy'], 'pro_other_notes' => $_REQUEST['other_notes'], 'pro_house_call' => (int) $_REQUEST['house_call'], 'pro_availability_from' => date('H:i:s', strtotime($_REQUEST['availability_from'])), 'pro_availability_to' => date('H:i:s', strtotime($_REQUEST['availability_to'])));
            
            if(!empty($_REQUEST['direction'])){
                $data['pro_direction'] = $_REQUEST['direction'];
            }

            if (!empty($_FILES['image'])) {

                $target_dir = "../uploads/provider_image/";
                $name = time() . basename($_FILES["image"]["name"]);
                $target_file = $target_dir . $name;
                $uploadOk = 1;
                $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
                
                // Check if image file is a actual image or fake image
                $check = getimagesize($_FILES["image"]["tmp_name"]);
                if ($check !== false) {
                    $uploadOk = 1;
                } else {
                    $uploadOk = 0;
                }
                
                // Check if file already exists
                if (file_exists($target_file)) {
                    $uploadOk = 0;
                }
                
                // Check file size
                if ($_FILES["image"]["size"] > 500000) {
                    $uploadOk = 0;
                }
                
                // Allow certain file formats
                if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                    $uploadOk = 0;
                }
                
                // Check if $uploadOk is set to 0 by an error
                if ($uploadOk != 0) {
                    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                        $data['pro_image'] = $name;
                    }
                }
            }

            if (!empty($_REQUEST['pro_id'])) {
                $pro_id = (int) $_REQUEST['pro_id'];
                $where = array('pro_id' => $pro_id);
                $this->update_records('service_provider', $data, $where);
            } else {
                $where_u = array('pro_u_id' => (int) $_REQUEST['user_id']);
                $check_provider = $this->get_record_where('service_provider', $where_u);
                if(!empty($check_provider)){
                    $response = array('status' => 'false', 'message' => 'Provider details already exists');
                    goto end_option;
                }
                    
                
                $pro_id = $this->insert_records('service_provider', $data);
            }

            if ($pro_id > 0) {
                $response = array('status' => 'true', 'message' => 'Successfully submitted', 'pro_id' => $pro_id);
            } else {
                $response = array('status' => 'false', 'message' => 'Error in insertion');
            }
        } else {
            $response = array('status' => 'false', 'message' => 'Invalid request parameter');
        }
        end_option: $this->json_output($response);
    }

}

$provider_info = new AddProviderInfo();
$provider_info->add_edit_info();
