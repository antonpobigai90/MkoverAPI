<?php
require './db.class.php';

class CancelBooking extends DB {

    function booking_cancel() {
        if(!empty($_REQUEST['book_id']) && !empty($_REQUEST['user_type'])){
            $type = (int)$_REQUEST['user_type'];
            $booking_id = (int)$_REQUEST['book_id'];
            
            $where_booking = array('book_id' => $booking_id);
            $get_booking_cost = $this->get_record_where('bookings', $where_booking, 'book_user, book_amount, book_service, book_status');
            $booking_status = $get_booking_cost[0]['book_status'];
            $u_id = $get_booking_cost[0]['book_user'];
            
            $where_user = array('u_id' => $u_id);
                    
            $wallet_balance = $this->get_record_where('users', $where_user, 'u_wallet');
            $balance = (int)$wallet_balance[0]['u_wallet'];
            
            if($booking_status == 3){
                $updated_balance = $balance + (int)$get_booking_cost[0]['book_amount'];
                $balance_data = array('u_wallet' => $updated_balance);
                $this->update_records('users', $balance_data, $where_user);
            }
            
            $service = $get_booking_cost[0]['book_service'];
            
            $where_service = array('s_id' => $service);
            $tables = array('services', 'service_provider');
            $keys_1 = array('s_provider');
            $keys_2 = array('pro_id');
            $join_type = array('inner', 'inner');
            $get_provider_user_id = $this->get_record_join($tables, $keys_1, $keys_2, $join_type, $where_service, 'pro_u_id');
            
            $pro_u_id = $get_provider_user_id[0]['pro_u_id'];
            
            
            $noti_msg = 'Your booking has been cancelled by Customer';
            $book_status = 5;
            $noti_user = $pro_u_id;
            
            $noti_status = 3;
            $noti_type = 'cancel_by_customer';
            
            // 1-Customer / 2-Provider
            if($type == 2){
                $noti_status = 6;
                $noti_msg = 'Your booking has been cancelled by Service Provider';
                $book_status = 6;
                $noti_user = $u_id;
                $noti_type = 'cancel_by_provider';
            }
            
            $where = array('book_id' => $booking_id);
            $data = array('book_status' => $book_status);
            $this->update_records('bookings', $data, $where);
            
            $where_user = array('u_id' => $noti_user);
            $user_details = $this->get_record_where('users', $where_user, 'u_device_id');
            $device_id = $user_details[0]['u_device_id'];
//            $alert = 'Bookmwah Notification';

        //    $send_noti = $this->push_iOS($device_id, $noti_msg, $alert);
            
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
            
            $noti = array('noti_status' => $noti_status, 'noti_type' => $noti_type, 'noti_desc' => $booking_details, 'book_id' => $booking_id);
            $send_noti = $this->push_iOS($device_id, $noti, $noti_msg);
            
            $insert_noti = array('noti_u_id' => $noti_user, 'noti_msg' => $noti_msg, 'noti_book_id' => $booking_id, 'noti_datetime' => date('Y-m-d H:i:s'), 'noti_type' => $noti_status);
            $this->insert_records('notifications', $insert_noti);
            $response = array('status' => 'true', 'message' => 'Booking cancelled successfully');
        } else {
            $response = array('status' => 'false', 'message' => 'Invalid request parameter');
        }
        $this->json_output($response);
    }

}

$cancel = new CancelBooking();
$cancel->booking_cancel();