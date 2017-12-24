<?php
require './db.class.php';

class Login extends DB {

    function user_login() {
        if(!empty($_REQUEST['type']) && !empty($_REQUEST['device_id'])){
            $type = (int)$_REQUEST['type'];
            $device_id = $_REQUEST['device_id'];
            if($type == 1){
                if(!empty($_REQUEST['email']) && !empty($_REQUEST['password'])){
                    $email = $_REQUEST['email'];
                    $password = md5($_REQUEST['password']);
                    
                    $where = array('u_email' => $email);
                    $user_details = $this->get_record_where('users', $where);
                    if($user_details){
                        $check_pass = array('u_email' => $email, 'u_password' => $password);
                        $details = $this->get_record_where('users', $check_pass);
                        if($details){
                            $userDetails = $details[0];
                            
                            if($userDetails['u_status'] == 1){
                                unset($userDetails['u_password']); unset($userDetails['u_device_type']); unset($userDetails['u_device_id']); unset($userDetails['u_fb_id']); unset($userDetails['u_datetime']);

                                $update_data = array('u_device_id' => $device_id);
                                $update_user = $this->update_records('users', $update_data, $where);

                                $where_pro_user = array('pro_u_id' => $userDetails['u_id']);
                                $provider_details = $this->get_record_where('service_provider', $where_pro_user, 'pro_id');
                                if(!empty($provider_details)){
                                    $userDetails['pro_id'] = $provider_details[0]['pro_id'];
                                } else {
                                    $userDetails['pro_id'] = '';
                                }
                                
                                if(!empty($userDetails['u_image']))
                                    $userDetails['u_image'] = $this->baseurl.UPLOADS.'/user_image/'.$userDetails['u_image'];

                                $response = array('status' => 'true', 'message' => 'Login successfully', 'user_details' => $userDetails);
                            } else {
                                $response = array('status' => 'false', 'message' => 'User is blocked by admin');
                            }
                        } else {
                            $response = array('status' => 'false', 'message' => 'Invalid password');
                        }
                    } else {
                        $response = array('status' => 'false', 'message' => 'User not found');
                    }
                } else {
                    $response = array('status' => 'false', 'message' => 'Invalid request parameter');
                }
            } elseif ($type == 2) {
                if(!empty($_REQUEST['email']) && !empty($_REQUEST['fb_id']) && !empty($_REQUEST['image_url']) && !empty($_REQUEST['name'])){
                    $email = $_REQUEST['email'];
                    $fb_id = $_REQUEST['fb_id'];
                    $image = $_REQUEST['image_url'];
                    $name = $_REQUEST['name'];
                    
                    $where = array('u_email' => $email);
                    $user_details = $this->get_record_where('users', $where);
                    if($user_details){
                        $userDetails = $user_details[0];
                        unset($userDetails['u_password']); unset($userDetails['u_device_type']); unset($userDetails['u_device_id']); unset($userDetails['u_fb_id']); unset($userDetails['u_datetime']);
                        
                        $update_data = array('u_device_type' => $device_type, 'u_device_id' => $device_id, 'u_fb_id' => $fb_id);
                        if($userDetails['u_type'] == 2){
                            $update_data['u_image'] = $image;
                        }
                        
                        $update_user = $this->update_records('users', $update_data, $where);
                        
                        $where_pro_user = array('pro_u_id' => $userDetails['u_id']);
                        $provider_details = $this->get_record_where('service_provider', $where_pro_user, 'pro_id');
                        if(!empty($provider_details)){
                            $userDetails['pro_id'] = $provider_details[0]['pro_id'];
                        } else {
                            $userDetails['pro_id'] = '';
                        }
                        
                        $response = array('status' => 'true', 'message' => 'Login successfully', 'user_details' => $userDetails);
                    } else {
                        $insert_data = array('u_email' => $email, 'u_fullname' => $name, 'u_image' => $image, 'u_device_id' => $device_id, 'u_fb_id' => $fb_id, 'u_type' => 2, 'u_datetime' => date('Y-m-d H:i:s'));
                        $user = $this->insert_records('users', $insert_data);
                        if($user > 0){
                            $where = array('u_email' => $email);
                            $user_details = $this->get_record_where('users', $where);
                            $userDetails = $user_details[0];
                            unset($userDetails['u_password']); unset($userDetails['u_device_type']); unset($userDetails['u_device_id']); unset($userDetails['u_fb_id']); unset($userDetails['u_datetime']);
                            
                            $where_pro_user = array('pro_u_id' => $userDetails['u_id']);
                            $provider_details = $this->get_record_where('service_provider', $where_pro_user, 'pro_id');
                            if(!empty($provider_details)){
                                $userDetails['pro_id'] = $provider_details[0]['pro_id'];
                            } else {
                                $userDetails['pro_id'] = '';
                            }
                            $response = array('status' => 'true', 'message' => 'Login successfully', 'user_details' => $userDetails);
                        } else {
                            $response = array('status' => 'false', 'message' => 'Error in insertion');
                        }
                    }
                } else {
                    $response = array('status' => 'false', 'message' => 'Invalid request parameter');
                }
            } else {
                $response = array('status' => 'false', 'message' => 'Invalid login type');
            }
        } else {
            $response = array('status' => 'false', 'message' => 'Invalid request parameter');
        }
        $this->json_output($response);
    }

}

$login = new Login();
$login->user_login();