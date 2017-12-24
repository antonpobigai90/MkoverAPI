<?php
require './db.class.php';

class Verification extends DB {

    function user_verification() {
        if(!empty($_REQUEST['user_id']) && !empty($_REQUEST['user_otp'])){
            
            $where = array('u_id'=>$_REQUEST['user_id']);
            $user_details = $this->get_record_where('users', $where);
            if(!empty($user_details)){
                $where1 = array('user_otp' => (int)$_REQUEST['user_otp'],'u_id'=>$_REQUEST['user_id']);
                $otp_details = $this->get_record_where('users', $where1);
                if(!empty($otp_details))
                {
                    $user_id=$otp_details[0]['u_id'];
                    $email=$otp_details[0]['u_email'];
                    $name=$otp_details[0]['u_fullname'];
                    if(!empty($otp_details[0]['u_image']))
                    {
                        $image= $this->baseurl.UPLOADS.'/user_image/'.$otp_details[0]['u_image'];
                    }else{
                         $image='';
                    }
                    
                    $mobile_no=$otp_details[0]['u_mobile_no'];
                    $w=array('u_id'=>$_REQUEST['user_id']);
                    $data['verify_status']='1';
                    $update = $this->update_records('users', $data, $w);
                     $response = array('status' => 'true', 'message' => 'OTP Verifed Successfully','u_id'=>$user_id,'email'=>$email,'u_fullname'=>$name,'u_image'=>$image,'u_mobile_no'=>$mobile_no); 
                }else{
                    $response = array('status' => 'false', 'message' => 'Invalid OTP'); 
                }

               
               
            } else {
               
                $response = array('status' => 'false', 'message' => 'Invalid User Id');
            }
        } else {
            $response = array('status' => 'false', 'message' => 'Invalid request parameter');
        }
        $this->json_output($response);
    }

}

$service = new Verification();
$service->user_verification();