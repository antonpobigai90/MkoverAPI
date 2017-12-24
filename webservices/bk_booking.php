<?php

require './db.class.php';

class Booking extends DB {

    function service_booking() {
        if (!empty($_REQUEST['user_id']) && !empty($_REQUEST['s_id']) && !empty($_REQUEST['date']) && !empty($_REQUEST['time']) && !empty($_REQUEST['amount']) && !empty($_REQUEST['house_call'])) {
            if ((!empty($_REQUEST['card_holder_name']) && !empty($_REQUEST['card_number']) && !empty($_REQUEST['expiry_date']) && !empty($_REQUEST['card_cvv'])) || !empty($_REQUEST['card_id'])) {
                $booking_date = date('Y-m-d', strtotime($_REQUEST['date']));
                $booking_time = date('H:i:s', strtotime($_REQUEST['time']));

                $insert_data = array('book_user' => (int) $_REQUEST['user_id'], 'book_service' => (int) $_REQUEST['s_id'], 'book_date' => $booking_date, 'book_time' => $booking_time, 'book_amount' => $_REQUEST['amount'], 'book_house_call' => (int) $_REQUEST['house_call'], 'book_payment_status' => 2, 'book_status' => 1, 'book_datetime' => date('Y-m-d H:i:s'));
                if (!empty($_REQUEST['addr'])) {
                    $insert_data['book_addr'] = $_REQUEST['addr'];
                }
                
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

                if (!empty($_REQUEST['card_holder_name']) && !empty($_REQUEST['card_number']) && !empty($_REQUEST['expiry_date']) && !empty($_REQUEST['card_cvv'])) {
                    $data = array('card_number' => $_REQUEST['card_number'], 'card_u_id' => $_REQUEST['user_id']);
                    $check_record = $this->get_record_where('credit_card', $data);
                    if(!empty($check_record)){
                        
                    } else {
                        $insert_Card = $this->insert_records('credit_card', $data);
                    }
                } elseif (!empty($_REQUEST['card_id'])) {
                    
                }
                $response = array('status' => 'true', 'message' => 'Successfully booked');
            } else {
                $response = array('status' => 'false', 'message' => 'Invalid request parameter');
            }
        } else {
            $response = array('status' => 'false', 'message' => 'Invalid request parameter');
        }
        
        $this->json_output($response);
    }

}

$booking = new Booking();
$booking->service_booking();
