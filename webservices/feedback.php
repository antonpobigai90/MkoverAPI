<?php
require './db.class.php';

class Feedback extends DB 
{

    function feedback_rating()
    {
        if(!empty($_REQUEST['book_id']) && !empty($_REQUEST['rating']) && !empty($_REQUEST['feedback']))
        {
            $today = date('Y-m-d');
            $time = date('H:i:s');
            
            $where = array('book_id' => (int)$_REQUEST['book_id']);
            $get_booking = $this->get_record_where('bookings', $where, 'book_id, book_user, book_date, book_time, book_service, book_status, book_rating');
            $booking_details = $get_booking[0];
            
            $status = 1;
            
            if($booking_details['book_status'] == 1 || $booking_details['book_status'] == 3){
                if($today > $booking_details['book_date']){
                    $status = 2;
                }

                $time1 = strtotime($time);
                $time2 = strtotime($booking_details['book_time']);

                if($today == $booking_details['book_date'] && $time1 > $time2){
                    $status = 2;
                }
            }
            
            if($booking_details['book_status'] == 2)
                $status = 2;
            
            if($status == 2)
            {
                
                if(!empty($booking_details['book_rating']))
                {
                    $response = array('status' => 'false', 'message' => 'Feedback already provided');
                }
                else
                {
                    $given_rating = $_REQUEST['rating'];
                    $data = array('book_rating' => $given_rating, 'book_feedback' => $_REQUEST['feedback']);
                    $update_rating = $this->update_records('bookings', $data, $where);
                    
                    $where_service = array('s_id' => (int)$booking_details['book_service']);
                    $tables = array('services', 'service_provider');
                    $keys_1 = array('services.s_provider');
                    $keys_2 = array('service_provider.pro_id');
                    $join_type = array('inner', 'inner');
                    $column = 'pro_id, pro_rating';
                    $get_service = $this->get_record_join($tables, $keys_1, $keys_2, $join_type, $where_service, $column);
                    
                    $pro_id = $get_service[0]['pro_id'];
                    $pro_rating = $get_service[0]['pro_rating'];
                    
                    $where_provider = array('pro_id' => $pro_id);
                    if(!empty($pro_rating))
                    {
                        $rating = ($pro_rating + $given_rating)/2;
                    }
                    else
                    {
                        $rating = $given_rating;
                    }
                    $update_data = array('pro_rating' => $rating);
                    $update_provider = $this->update_records('service_provider', $update_data, $where_provider);
                    
                    $response = array('status' => 'true', 'message' => 'Feedback submitted successfully');
                }
            }
            else
            {
                $response = array('status' => 'false', 'message' => 'Appointment is not completed yet');
            }
        }
        else
        {
            $response = array('status' => 'false', 'message' => 'Invalid request parameter');
        }
        $this->json_output($response);
    }

}

$feed = new Feedback();
$feed->feedback_rating();