<?php
require './db.class.php';

class Appointments extends DB {

    function get_appointments() {
        if($_REQUEST['user_id']){
            $user = $_REQUEST['user_id'];
            
            $t_date = date('d-m-Y');
            $t_time = date('h:i a');
            
            $where = array('book_user' => $user);
            $tables = array('bookings', 'services', 'service_category', 'service_provider');
            $keys_1 = array('services.s_id', 'service_category.s_cat_id', 'service_provider.pro_id');
            $keys_2 = array('bookings.book_service', 'services.s_category', 'services.s_provider');
            $join_type = array('inner', 'inner', 'inner', 'inner');
            $column = 'book_id, s_id, s_name, book_date, book_time, book_amount, book_status, book_rating, book_feedback, s_cat_id, s_cat_name, pro_name, pro_u_id, pro_image';
            $bookings = $this->get_record_join($tables, $keys_1, $keys_2, $join_type, $where, $column);
            
            if(!empty($bookings)){
                foreach ($bookings as $value) {
                    $value['book_date'] = date('d-m-Y', strtotime($value['book_date']));
                    $value['book_time'] = date('h:i a', strtotime($value['book_time']));
//                    $value['pro_image'] = $this->baseurl.UPLOADS.'/provider_image/'.$value['pro_image'];
                    
                    $where_img = array('u_id' => $value['pro_u_id']);
                    $img = $this->get_record_where('users', $where_img, 'u_image, u_type');
                    $image = $img[0]['u_image'];
                    $u_type = $img[0]['u_type'];
                    
                    if(!empty($image) && $u_type == 1)
                        $value['pro_image'] = $this->baseurl.UPLOADS.'/user_image/'.$image;
                    elseif (!empty($image) && $u_type == 2)
                        $value['pro_image'] = $image;
                    else
                        $value['pro_image'] = '';
                    
                    if($value['book_status'] == 1 || $value['book_status'] == 3){
                        if($t_date > $value['book_date']){
                            $value['book_status'] = '2';
                        }

                        $time1 = strtotime($t_time);
                        $time2 = strtotime($value['book_time']);

                        if($t_date == $value['book_date'] && $time1 > $time2){
                            $value['book_status'] = '2';    
                        }
                    }
                    
                    $appointments[] = $value;
                }
                $response = array('status' => 'true', 'message' => 'Appointments found', 'appointments' => $appointments);
            } else {
                $response = array('status' => 'false', 'message' => 'No appointments found');
            }
        } else {
            $response = array('status' => 'false', 'message' => 'Invalid request parameter');
        }
        $this->json_output($response);
    }

}

$app = new Appointments();
$app->get_appointments();