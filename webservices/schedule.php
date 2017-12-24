<?php
require './db.class.php';

class Schedule extends DB {

    function schedule_for_you() {
        if(!empty($_REQUEST['pro_id'])){
            $pro_id = (int)$_REQUEST['pro_id'];
            $today = date('Y-m-d');
            
            $t_date = date('d-m-Y');
            $t_time = date('h:i a');
            
            $schedule_query = "SELECT b.book_id, u.u_id, u.u_email, u.u_fullname, u.u_image, u.u_gender, u.u_mobile_no, u.u_type, s.s_id, s.s_name, sc.s_cat_name, b.book_addr, b.book_date, b.book_time, b.book_to_time, b.book_status FROM bookings b JOIN users u ON u.u_id = b.book_user JOIN services s ON s.s_id = b.book_service JOIN service_category sc ON s.s_category = sc.s_cat_id WHERE book_status=3 AND book_service IN (SELECT s_id FROM `services` WHERE s_provider = $pro_id)";
            
            $schedules = $this->query_result($schedule_query);
            if(!empty($schedules)){
                foreach ($schedules as $value) {
                    if($value['u_type'] == 1 && !empty($value['u_image'])){
                        $value['u_image'] = $this->baseurl.UPLOADS.'/user_image/'.$value['u_image'];
                    }
                    unset($value['u_type']);
                    
                    $value['book_date'] = date('d-m-Y', strtotime($value['book_date']));
                    $value['book_time'] = date('h:i a', strtotime($value['book_time']));
                    $value['book_to_time'] = date('h:i a', strtotime($value['book_to_time']));
                    
                    /*if($value['book_status'] == 1 || $value['book_status'] == 3){
                        if($t_date > $value['book_date']){
                            $value['book_status'] = '2';
                        }

                        $time1 = strtotime($t_time);
                        $time2 = strtotime($value['book_time']);

                        if($t_date == $value['book_date'] && $time1 > $time2){
                            $value['book_status'] = '2';    
                        }
                    }*/ 
                    
                    $schedule[] = $value;
                }
                $response = array('status' => 'true', 'message' => 'Schedule found', 'schedule' => $schedule);
            } else {
                $response = array('status' => 'false', 'message' => 'No bookings found');
            }
        } else {
            $response = array('status' => 'false', 'message' => 'Invalid request parameter');
        }
        $this->json_output($response);
    }

}

$schedule = new Schedule();
$schedule->schedule_for_you();