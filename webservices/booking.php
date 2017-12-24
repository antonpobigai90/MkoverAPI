<?php

require './db.class.php';

class Booking extends DB {

    function service_booking() {
        
        if (!empty($_REQUEST['user_id']) && !empty($_REQUEST['s_id']) && !empty($_REQUEST['booking_date']) && !empty($_REQUEST['booking_time']) && !empty($_REQUEST['amount']) && !empty($_REQUEST['house_call'])) {
            //if ((!empty($_REQUEST['card_holder_name']) && !empty($_REQUEST['card_number']) && !empty($_REQUEST['expire_month']) && !empty($_REQUEST['expire_year']) && !empty($_REQUEST['card_type']) && !empty($_REQUEST['card_cvv'])) || !empty($_REQUEST['card_id'])) {
                //$card_token = '';
//                
//                /*---Sandbox Account---*/
//                $clientId = "AU7HqvXbs652uXKXycUaopbt6bXQBdcUn843CVv-iXPpJE4qdJRNLUnqcf2ewkvCeeZm8B4n2bVSZIh_";
//                $secret = "EGnozeEiKuEHh0rKyQj6_RPFAB9QzFFElRgyWYxsEoYo1eI2wEHZ6B_l9-DgYGUHr0GP_UDlOVXOenA4";
//                
//                $clientId = "AWn3BP7SUmFI9LHkEZl-hEr5dDC1Hu4-CO2NPLqaPnulDKjNlS-Q7u-UqRRnOlifj9ZM-Ip7Gpg0xwh6";
//                $secret = "EPTcuWXe3jvKbpUGpP54tupPfPSWG4Abx6dEcnQ7GLHjNb-VXfz7a3lQWZBsTZgHQBA_F67izapxQzHd";
//                
//                $clientId = "AaoJxLmNIHr_sXQeJa1pOGMS8LwIgfNasrDEnymrb5Tr-C0FtiEATKPCI4ZL1v5mzLmtB0XLRaK8cyJN";
//                $secret = "EH3f7HSlusjFzAhQO2x0s74iMyGwc3xW9Mr-gljlpV5XdFq0Mr9KXAa8A6aJ5nJxgqcdvSrYmL7-W9uU";

//                $ipnexec = curl_init();
//                curl_setopt($ipnexec, CURLOPT_URL, "https://api.sandbox.paypal.com/v1/oauth2/token");
//                curl_setopt($ipnexec, CURLOPT_URL, "https://api.paypal.com/v1/oauth2/token");

//                curl_setopt($ipnexec, CURLOPT_SSL_VERIFYPEER, false);
//                curl_setopt($ipnexec, CURLOPT_POST, true);
//                curl_setopt($ipnexec, CURLOPT_USERPWD, $clientId . ":" . $secret);
//                curl_setopt($ipnexec, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
//                curl_setopt($ipnexec, CURLOPT_RETURNTRANSFER, true);
//                $ipnresult = curl_exec($ipnexec);
//                $result = json_decode($ipnresult);
//                $access_token = $result->access_token;
//                curl_close($ipnexec);
//                
//                if (!empty($_REQUEST['card_holder_name']) && !empty($_REQUEST['card_number']) && !empty($_REQUEST['expire_month']) && !empty($_REQUEST['expire_year']) && !empty($_REQUEST['card_type'])) {
//                    
//                    $holder_name = $_REQUEST['card_holder_name'];
//                    $name_arr = explode(' ', $holder_name);
//                    $firstname = $name_arr[0];
//                    $lastname = 'customer';
//                    if(!empty($name_arr[1]))
//                        $lastname = $name_arr[1];
//                    
//                    $payer_id = 'user'.(int)$_REQUEST['user_id'];
//                    $card_type = strtolower($_REQUEST['card_type']);
//                    $card_number = $_REQUEST['card_number'];
//                    $month = $_REQUEST['expire_month'];
//                    $year = $_REQUEST['expire_year'];
//                    
//                    $data = '
//                    {
//                        "payer_id":"'.$payer_id.'",
//                        "type":"'.$card_type.'",
//                        "number":"'.$card_number.'",
//                        "expire_month":"'.$month.'",
//                        "expire_year":"'.$year.'",
//                        "first_name":"'.$firstname.'",
//                        "last_name":"'.$lastname.'"
//                   }
//                   ';
//                    $ch = curl_init();
//                    $scope = "https://api.sandbox.paypal.com/v1/vault/credit-card";
//                    $scope = "https://api.paypal.com/v1/vault/credit-card";
//                    curl_setopt($ch, CURLOPT_URL, $scope);
//                    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . $access_token));
//                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//                    curl_setopt($ch, CURLOPT_POST, true);
//                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
//                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//                    $result = curl_exec($ch);
//                    $err = curl_error($ch);
//                    if (empty($result)){
//                        header('Content-Type: application/json');
//                        echo json_encode(array('status' => 'false', 'error' => 'Error in clientId authentication'));
//                        die();
//                    } else {
//                        $json = json_decode($result);
//                    }
//                    curl_close($ch);
//                    
//                    if(strtolower($json->state) == 'ok'){
//                        $card_token = $json->id;
//                        $payer = $json->payer_id;
//                        $insert_data = array('card_u_id' => (int)$_REQUEST['user_id'], 'card_number' => $json->number, 'card_token' => $json->id, 'payer_id' => $json->payer_id, 'valid_until' => $json->valid_until);
//                        $insert_Card = $this->insert_records('credit_card', $insert_data);
//                    } else {
//                        header('Content-Type: application/json');
//                        echo json_encode(array('status' => 'false', 'error' => $json->message));
//                        die();
//                    }
//                } elseif (!empty($_REQUEST['card_id'])) {
//                    $where = array('card_id' => $_REQUEST['card_id']);
//                    $get_card = $this->get_record_where('credit_card', $where, 'card_token, payer_id');
//                    if(!empty($get_card)){
//                        $card_token = $get_card[0]['card_token'];
//                        $payer = $get_card[0]['payer_id'];
//                        $insert_Card = (int)$_REQUEST['card_id'];
//                    }
//                }
                
//                if(!empty($card_token)){
//                    $payment_data = '{
//                        "intent":"sale",
//                        "payer": {
//                          "payment_method": "credit_card",
//                          "funding_instruments": [
//                            {
//                              "credit_card_token":{
//                                "credit_card_id":"' . $card_token . '",
//                                "payer_id":"' . $payer . '"
//                              }
//                            }
//                          ]
//                        },
//                        "transactions":[
//                          {
//                            "amount":{
//                              "total":"'.$_REQUEST['amount'].'",
//                              "currency":"USD"
//                            },
//                            "description":"This transaction is made for service booking."
//                          }
//                        ]
//                      }
//                    ';
//                    
//                    $ch = curl_init();
//                    curl_setopt($ch, CURLOPT_URL, "https://api.sandbox.paypal.com/v1/payments/payment");
//                    curl_setopt($ch, CURLOPT_POST, true);
//                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//                    curl_setopt($ch, CURLOPT_POSTFIELDS, $payment_data);
//                    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . $access_token));
//                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//                    $result = curl_exec($ch);
//                    if (empty($result)){
//                        header('Content-Type: application/json');
//                        echo json_encode(array('status' => 'false', 'error' => 'Error in transaction'));
//                        die();
//                    } else {
//                        $payment_json = json_decode($result);
//                    }
//                    curl_close($ch);
//                    
//                    if(strtolower($payment_json->state) == 'approved'){
//                        $insert_payment = array('b_pay_gateway' => 'PAYPAL', 'b_pay_gateway_txno' => $payment_json->id, 'b_pay_response' => $result, 'b_pay_datetime' => date('Y-m-d H:i:s'));
//                        $b_pay_id = $this->insert_records('booking_payment', $insert_payment);
//                    } else {
//                        header('Content-Type: application/json');
//                        echo json_encode(array('status' => 'false', 'error' => 'Error in transaction'));
//                        die();
//                    }
//                }
                
//                if(!empty($insert_Card)){
                    $booking_date = date('Y-m-d', strtotime($_REQUEST['booking_date']));
                    $booking_time = date('H:i:s', strtotime($_REQUEST['time']));
                    $booking_to_time = date('H:i:s', strtotime($_REQUEST['totime']));  

                    $insert_data = array('book_user' => (int) $_REQUEST['user_id'], 'book_service' => (int) $_REQUEST['s_id'], 'book_date' => $booking_date, 'book_time' => $booking_time, 'book_to_time' => $booking_to_time, 'book_amount' => $_REQUEST['amount'], 'book_house_call' => (int) $_REQUEST['house_call'], 'book_addr'=> $_REQUEST['addr'], 'book_card_id' => $insert_Card, 'book_payment_status' => 1, 'book_status' => 1, 'book_datetime' => date('Y-m-d H:i:s'));

                    $get_providers_share = $this->query_result("SELECT * FROM `provider_share`");
                    if(!empty($get_providers_share)){
                        $amt = $_REQUEST['amount'];
                        $share_per = $get_providers_share[0]['share_value'];
                        $share_amt = ($share_per / 100) * $amt;

                        $insert_data['book_provider_share'] = $share_amt;
                    } else {
                        $insert_data['book_provider_share'] = $_REQUEST['amount'];
                    }
                    
                    $booking_id = $this->insert_records('bookings', $insert_data);
                    
                    
                    //------------Booking Notification---------------//
                    
                    $service = (int)$_REQUEST['s_id'];
                    $pro_details_query = "SELECT u.u_id, u.u_device_id, u.u_email, u.u_image, u.u_gender, u.u_mobile_no, s.s_id, s.s_name FROM services s JOIN service_provider sp ON sp.pro_id = s.s_provider JOIN users u ON u.u_id = sp.pro_u_id WHERE s.s_id = $service";
                    $get_pro_details = $this->query_result($pro_details_query);
                    $pro_details = $get_pro_details[0];
                    
                    $pro_u_id = $pro_details['u_id'];
                    $service_name = $pro_details['s_name'];
                    $device_id = $pro_details['u_device_id'];
                    $photo_url = $this->baseurl.UPLOADS.'/user_image/'.$pro_details['u_image'];
                    $email = $pro_details['u_email'];
                    $mobile = $pro_details['u_mobile_no'];
                    $gender = $pro_details['u_gender'];
                    
                    $u_id = (int)$_REQUEST['user_id'];
                    $where_u = array('u_id' => $u_id);
                    $user_details = $this->get_record_where('users', $where_u, 'u_id, u_fullname');
                    $user_fullname = $user_details[0]['u_fullname'];
                    
                    $noti_type = 'booking';
                    $noti_status = 5;
                    $noti_msg = "$user_fullname created booking for service $service_name";
                    
                    $data = array("aps" => array(
                        "alert" => $noti_msg,
                        "type" => "match",
                        "sender_id" => $pro_u_id, 
                        "book_id" => $booking_id,  
                        "u_id" => $_REQUEST['user_id'],                         
                        "s_id" => $service,                                  
                        "s_name" => $service_name,
                        "u_gender" => $gender,
                        "u_fullname" => $user_fullname,
                        "u_email" => $email,
                        "u_mobile_no" => $mobile,
                        "u_image" => $photo_url,
                        "book_date" => $_REQUEST['booking_date'],
                        "book_time" => $_REQUEST['booking_time'], 
                        "book_to_time" => $_REQUEST['totime'],
                        "book_addr"=> $_REQUEST['addr']  
                    ));
                    
                    //print_r($data);
                    //echo $device_id;
                    
                    $this -> push($device_id, $data); 
                    
                    $insert_noti = array('noti_u_id' => $pro_u_id, 'noti_msg' => $noti_msg, 'noti_book_id' => $booking_id, 'noti_datetime' => date('Y-m-d H:i:s'), 'noti_type' => $noti_status);
                    $this->insert_records('notifications', $insert_noti);
                    
                    $response = array('status' => 'true', 'message' => 'Successfully booked');
               // } else {
//                    $response = array('status' => 'false', 'message' => 'Error in card insertion');
//                }
           // } else {
//                $response = array('status' => 'false', 'message' => 'Invalid request parameter');
//            }
        } else {
            $response = array('status' => 'false', 'message' => 'Invalid request parameter');
        }
        
        $this->json_output($response);
    }
    
    function push($deviceToken, $body = array()) {
        
        $deviceToken = strtolower(str_replace(array(" ", "-", "_"), array("", "", ""), $deviceToken));

        // Put your private key's passphrase here:
        $passphrase = '123456';

        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', 'ckdist.pem');

        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
        stream_context_set_option($ctx, 'ssl', 'cafile', 'entrust_2048_ca.cer');


        //$fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);

        $fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
        
        if (!$fp) {
            return false;
        }

        // Encode the payload as JSON
        $payload = json_encode($body);

        // Build the binary notification
        $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

        // Send it to the server
        $result = fwrite($fp, $msg, strlen($msg));

        if (!$result)
            return false;

        // Close the connection to the server
        fclose($fp);
        return true;
    }

}

$booking = new Booking();
$booking->service_booking();
