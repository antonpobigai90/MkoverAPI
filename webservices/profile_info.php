<?php
require './db.class.php';

class ProfileInfo extends DB {

    function info_profile() {
        if(!empty($_REQUEST['u_id'])){
            $u_id = (int)$_REQUEST['u_id'];
            $where = array('u_id' => $u_id);
            $get_info = $this->get_record_where('users', $where);
            
            if(!empty($get_info)){
                $profile_info = $get_info[0];

                unset($profile_info['u_password']);
                unset($profile_info['u_device_type']);
                unset($profile_info['u_device_id']);
                unset($profile_info['u_fb_id']);
                unset($profile_info['u_datetime']);

                $type = $profile_info['u_type'];
                if($type == 1 && !empty($profile_info['u_image'])){
                    $profile_info['u_image'] = $this->baseurl.UPLOADS.'/user_image/'.$profile_info['u_image'];
                }

                unset($profile_info['u_type']);
                
                $response = array('status' => 'true', 'message' => 'Profile found', 'profile_info' => $profile_info);
            } else {
                $response = array('status' => 'false', 'message' => 'No records found');
            }
        } else {
            $response = array('status' => 'false', 'message' => 'Invalid request parameter');
        }
        $this->json_output($response);
    }

}

$info_profile = new ProfileInfo();
$info_profile->info_profile();
