<?php
require './db.class.php';

class NotificationRemove extends DB {

    function list_notification() {
        if(!empty($_REQUEST['u_id']) && !empty($_REQUEST['noti_id'])){
            $u_id = (int)$_REQUEST['u_id'];
            $noti_id = (int)$_REQUEST['noti_id'];

            $sql = "update notifications set noti_delete_status='2' where noti_id='".$noti_id."'";
            $this->query_result($sql);            
            
            $sql_notification = "SELECT n.noti_id, n.noti_msg, n.noti_type, n.noti_status, b.book_id, b.book_date, b.book_time, b.book_to_time, s.s_name, sc.s_cat_name FROM notifications n JOIN bookings b ON b.book_id = n.noti_book_id JOIN services s ON s.s_id = b.book_service JOIN service_category sc ON sc.s_cat_id = s.s_category WHERE noti_u_id = $u_id AND n.noti_delete_status = 1 ORDER BY n.noti_datetime DESC";
            $notification_list = $this->query_result($sql_notification);
            if(!empty($notification_list)){
                
                foreach ($notification_list as $value) {
                    if($value['noti_type'] == 3 || $value['noti_type'] == 5){
                        $booking_u_sql = "SELECT u.u_fullname, u.u_image, u.u_type FROM bookings b JOIN users u ON u.u_id = b.book_user WHERE b.book_id = ".$value['book_id'];
                        $booking_u = $this->query_result($booking_u_sql);
                        $u_details = $booking_u[0];
                        if($u_details['u_type'] == 1 && !empty($u_details['u_image'])){
                            $u_details['u_image'] = $this->baseurl.UPLOADS.'/user_image/'.$u_details['u_image'];
                        }
                        $value['u_fullname'] = $u_details['u_fullname'];
                        $value['u_image'] = $u_details['u_image'];
                    } else {
                        $get_booking_pro_sql = "SELECT u.u_fullname, u.u_image, u.u_type FROM bookings b JOIN services s ON s.s_id = b.book_service JOIN service_provider sp ON sp.pro_id = s.s_provider JOIN users u ON u.u_id = sp.pro_u_id WHERE b.book_id = ".$value['book_id'];
                        $get_booking_pro = $this->query_result($get_booking_pro_sql);
                        $pro_details = $get_booking_pro[0];
                        if($pro_details['u_type'] == 1 && !empty($pro_details['u_image'])){
                            $pro_details['u_image'] = $this->baseurl.UPLOADS.'/user_image/'.$pro_details['u_image'];
                        }
                        $value['u_fullname'] = $pro_details['u_fullname'];
                        $value['u_image'] = $pro_details['u_image'];
                    }
                    $value['book_date'] = date('d-m-Y', strtotime($value['book_date']));
                    $value['book_time'] = date('h:i a', strtotime($value['book_time']));
                    $value['book_to_time'] = date('h:i a', strtotime($value['book_to_time']));
                    
                    $notification[] = $value;
                }
                $response = array('status' => 'true', 'message' => 'Notification found', 'notification' => $notification);
            } else {
                $response = array('status' => 'false', 'message' => 'No records found');
            }
        } else {
            $response = array('status' => 'false', 'message' => 'Invalid request parameter');
        }
        $this->json_output($response);
    }

}

$noti = new NotificationRemove();
$noti->list_notification();