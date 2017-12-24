<?php

require './db.class.php';

class CompleteBooking extends DB {

    function complete_booking() {
        if(!empty($_REQUEST['book_id'])){
            
            $book_id = (int)$_REQUEST['book_id'];
                    
            $data = array('book_status' => 2);
            $where = array('book_id' => $book_id);
            $update = $this->update_records('bookings', $data, $where);
            
            $tables = array('bookings', 'services', 'service_category', 'service_provider');
            $keys_1 = array('services.s_id', 'service_category.s_cat_id', 'service_provider.pro_id');
            $keys_2 = array('bookings.book_service', 'services.s_category', 'services.s_provider');
            $join_type = array('inner', 'inner', 'inner', 'inner');
            $column = 'book_id, book_user, s_id, s_name, book_date, book_time, book_amount, book_status, book_rating, book_feedback, s_cat_id, s_cat_name, pro_name, pro_u_id, pro_image';
            $bookings = $this->get_record_join($tables, $keys_1, $keys_2, $join_type, $where, $column);
            
            $booking_details = $bookings[0];
            
            $booking_details['book_date'] = date('d-m-y', strtotime($booking_details['book_date']));
            $booking_details['book_time'] = date('h:i a', strtotime($booking_details['book_time']));
//            $booking_details['pro_image'] = $this->baseurl.UPLOADS.'/provider_image/'.$booking_details['pro_image'];
            
            $where_img = array('u_id' => $booking_details['pro_u_id']);
            $img = $this->get_record_where('users', $where_img, 'u_image, u_type');
            $image = $img[0]['u_image'];
            $u_type = $img[0]['u_type'];

            if(!empty($image) && $u_type == 1)
                $booking_details['pro_image'] = $this->baseurl.UPLOADS.'/user_image/'.$image;
            elseif (!empty($image) && $u_type == 2)
                $booking_details['pro_image'] = $image;
            else
                $booking_details['pro_image'] = '';
            
            $where_user = array('u_id' => $booking_details['book_user']);
            $user_details = $this->get_record_where('users', $where_user, 'u_device_id');
            $device_id = $user_details[0]['u_device_id'];
            
            $noti_status = 4;
            $noti_type = 'complete';
            $noti_msg = 'Your booking has been completed';
            
            $noti = array('noti_status' => $noti_status, 'noti_type' => $noti_type, 'noti_desc' => $booking_details);
            $send_noti = $this->push_iOS($device_id, $noti, $noti_msg);
            
            $insert_noti = array('noti_u_id' => $booking_details['book_user'], 'noti_msg' => $noti_msg, 'noti_book_id' => $book_id, 'noti_datetime' => date('Y-m-d H:i:s'), 'noti_type' => $noti_status);
            $this->insert_records('notifications', $insert_noti);
            
            $response = array('status' => 'true', 'message' => 'Booking completed successfully');
        } else {
            $response = array('status' => 'false', 'message' => 'Invalid request parameter');
        }
        $this->json_output($response);
    }

}

$com = new CompleteBooking();
$com->complete_booking();