<?php
require './db.class.php';

class EditProfile extends DB {

    function edit_profile() {
        if(!empty($_REQUEST['u_id'])){
            
//        if(!empty($_REQUEST['u_id']) && !empty($_REQUEST['name']) && !empty($_REQUEST['state']) && !empty($_REQUEST['gender']) && !empty($_REQUEST['mobile_no'])){
            
            if(!empty($_REQUEST['name']))
                $data['u_fullname'] = $_REQUEST['name'];
            
            if(!empty($_REQUEST['gender']))
                $data['u_gender'] = (int)$_REQUEST['gender'];
            
            if(!empty($_REQUEST['mobile_no']))
                $data['u_mobile_no'] = $_REQUEST['mobile_no'];
            
            if(!empty($_REQUEST['state']))
                $data['u_state'] = $_REQUEST['state'];
            
//            $data = array('u_fullname' => $_REQUEST['name'], 'u_gender' => (int)$_REQUEST['gender'], 'u_mobile_no' => $_REQUEST['mobile_no'], 'u_state' => $_REQUEST['state']);
            
            if(!empty($_REQUEST['old_pass']) && !empty($_REQUEST['new_pass'])){
                $where = array('u_id' => (int)$_REQUEST['u_id'], 'u_password' => md5($_REQUEST['old_pass']));
                $check_pass = $this->get_record_where('users', $where);
                if(!empty($check_pass)){
                    $data['u_password'] = md5($_REQUEST['new_pass']);
                } else {
                    $response = array('status' => 'false', 'message' => 'Invalid old password');
                    goto end_option;
                }
            }
            
            // Image Upload for profile
            if (!empty($_FILES['image'])) {

                $target_dir = "../uploads/user_image/";
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
                if ($_FILES["image"]["size"] > 2097152) {
                    $uploadOk = 0;
                }
                
                // Allow certain file formats
                if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                    $uploadOk = 0;
                }
                
                // Check if $uploadOk is set to 0 by an error
                if ($uploadOk != 0) {
                    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                        $data['u_image'] = $name;
                        $data['u_type'] = 1;
                    }
                }
            }
            
            $where_user = array('u_id' => (int)$_REQUEST['u_id']);
            $update = $this->update_records('users', $data, $where_user);
            
            $get_user = $this->get_record_where('users', $where_user, 'u_fullname, u_image');
            if(!empty($get_user[0]['u_image']))
                $get_user[0]['u_image'] = $this->baseurl.UPLOADS.'/user_image/'.$get_user[0]['u_image'];
            
            
            
            $response = array('status' => 'true', 'message' => 'User profile updated successfully', 'name' => $get_user[0]['u_fullname'], 'image' => $get_user[0]['u_image']);
        } else {
            $response = array('status' => 'false', 'message' => 'Invalid request parameter');
        }
        
        end_option: $this->json_output($response);
    }

}

$profile_edit = new EditProfile();
$profile_edit->edit_profile();