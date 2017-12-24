<?php
require './db.class.php';
require './Twilio/autoload.php';  

// Use the REST API Client to make requests to the Twilio REST API
use Twilio\Rest\Client;

class SignUp extends DB {

    function user_signup() {
        if(!empty($_REQUEST['name']) && !empty($_REQUEST['email']) && !empty($_REQUEST['password']) && !empty($_REQUEST['device_id'])  && !empty($_REQUEST['user_type'])){
            $email = $_REQUEST['email'];
//            $mobile = $_REQUEST['mobile'];
$mobile = "+8613180836958";
            $pass = $_REQUEST['password'];
            $name = $_REQUEST['name'];
            $user_type = $_REQUEST['user_type'];
            
            $where = array('u_email' => $email);
            $check_email = $this->get_record_where('users', $where);
            if($check_email){
                $response = array('status' => 'false', 'message' => 'Email already exists');
            } else {
                
                $i = 0; //counter
                $pin = ""; //our default pin is blank.
                while($i < 4){
                    //generate a random number between 0 and 9.
                    $pin .= mt_rand(0, 9);
                    $i++;
                }
                
                $body = 'Please enter this code, '.$pin.' to continue.';
                // Your Account SID and Auth Token from twilio.com/console
                $sid = 'ACbcd643228cc6ce82c848cbc52d83d260';
                $token = '64710f8d15583dba3b8499100e0c9eec';
                $client = new Client($sid, $token);

                // Use the client to do fun stuff like send text messages!
                $client->messages->create(
                    // the number you'd like to send the message to
                    $mobile,
                    array(
                        // A Twilio phone number you purchased at twilio.com/console
                        'from' => '+19042041550',
                        // the body of the text message you'd like to send
                        'body' => $body
                    )
                );
    
                $insert_data = array('u_email' => $email, 'u_password' => md5($pass), 'u_fullname' => $name, 'u_datetime' => date('Y-m-d H:i:s'), 'u_device_id' => $_REQUEST['device_id'],'user_otp'=>$pin,'user_type'=>$user_type);
                $user_id = $this->insert_records('users', $insert_data);
                if($user_id > 0){

                    if($user_type == 2){
                        $insert_data = array('pro_u_id' => $user_id,'pro_email' => $email, 'pro_name' => $name);
                        $service_user_id = $this->insert_records('service_provider', $insert_data);
                    }

                    $user_details = $this->get_record_where('users', $where);
                    $userDetails = $user_details[0];
                    unset($userDetails['u_password']); unset($userDetails['u_device_type']); unset($userDetails['u_device_id']); unset($userDetails['u_fb_id']); unset($userDetails['u_datetime']);
                    $userDetails['pro_id'] = '';
                    
                    $response = array('status' => 'true', 'message' => 'Sign up successful', 'user_details' => $userDetails);
                } else {
                    $response = array('status' => 'false', 'message' => 'Error in insertion');
                }
            }
        } else {
            $response = array('status' => 'false', 'message' => 'Invalid request parameter');
        }
        $this->json_output($response);
    }
    
}

$signup = new SignUp();
$signup->user_signup();

