<?php
require './db.class.php';

class ProviderDetails extends DB {

    function p_details() {
        if(!empty($_REQUEST['pro_id']) && !empty($_REQUEST['type'])){
            $pro_id = $_REQUEST['pro_id'];
            
            $type = (int)$_REQUEST['type']; // 1-Customer / 2-Provider
            
            if(!empty($_REQUEST['cat_id']))
                $cat_id = $_REQUEST['cat_id'];
            
            $where_pro = array('pro_id' => $pro_id);
            $provider_details = $this->get_record_where('service_provider', $where_pro);
            if(!empty($provider_details)){
                $pro_details = $provider_details[0];
                
                $where_img = array('u_id' => $pro_details['pro_u_id']);
                $img = $this->get_record_where('users', $where_img, 'u_image, u_type');
                $image = $img[0]['u_image'];
                $u_type = $img[0]['u_type'];  

                if(!empty($image) && $u_type == 1)
                    $pro_details['pro_image'] = $this->baseurl.UPLOADS.'/user_image/'.$image;
                elseif (!empty($image) && $u_type == 2)
                    $pro_details['pro_image'] = $image;
                else
                    $pro_details['pro_image'] = '';
                
//                unset($pro_details['pro_u_id']);
//                $pro_details['pro_image'] = $this->baseurl.UPLOADS.'/provider_image/'.$pro_details['pro_image'];
                $pro_details['pro_availability_from'] = date('h:i A', strtotime($pro_details['pro_availability_from']));
                $pro_details['pro_availability_to'] = date('h:i A', strtotime($pro_details['pro_availability_to']));
                
                $pro_details['pro_desc'] = $pro_details['pro_other_notes'];
                
                $pro_details['pro_rating'] = (int)$pro_details['pro_rating'];
                
                if(!empty($_REQUEST['cat_id'])){
                    $where_ser = array('s_cat_id' => $cat_id);
                    $service_cat = $this->get_record_where('service_category', $where_ser, 's_cat_name');
                    $pro_details['s_cat_name'] = $service_cat[0]['s_cat_name'];
                } else {
                    $pro_details['s_cat_name'] = "";
                }
                
                $where_ser = array('s_provider' => $pro_id, 's_status' => 1);
//                $services = $this->get_record_where('services', $where_ser);
                $tabels = array('services', 'service_category');
                $keys_1 = array('services.s_category');
                $keys_2 = array('service_category.s_cat_id');
                $join_type = array('inner', 'inner');
                $column = 's_id, s_name, s_cost, s_description, s_category, s_cat_name, s_cat_image';
                $get_services = $this->get_record_join($tabels, $keys_1, $keys_2, $join_type, $where_ser, $column);
                $services = array();
                if(!empty($get_services)){
                    foreach ($get_services as $s_value) {
                        $s_value['s_cat_image'] = $this->baseurl.UPLOADS.'/service_category/'.$s_value['s_cat_image'];
                        $services[] = $s_value;
                    }
                }
                
                $pro_details['services'] = $services;
                
                $portfolio_list = array();
                $where_provider = array('pp_provider' => $pro_id);
                $portfolio = $this->get_record_where('provider_portfolio', $where_provider);
                if(!empty($portfolio)){
                    foreach ($portfolio as $value) {
                        $value['pp_image'] = $this->baseurl.UPLOADS.'/portfolio/'.$value['pp_image'];
                        unset($value['pp_datetime']);
                        $portfolio_list[] = $value;
                    }
                }
                $pro_details['portfolio'] = $portfolio_list;
                
                $feedback_array = array();
                $where_feed = array('s_provider' => $pro_id, 'book_rating !=' => '');
                $tables = array('services', 'bookings', 'users');
                $keys_1 = array('services.s_id', 'users.u_id');
                $keys_2 = array('bookings.book_service', 'bookings.book_user');
                $join_type = array('inner', 'inner');
                $feedback = $this->get_record_join($tables, $keys_1, $keys_2, $join_type, $where_feed, 'u_id, u_fullname, u_image, u_type, book_feedback, book_rating');
                $recomm = array();
                if(!empty($feedback))
                {
                    foreach ($feedback as $value) {
                        if($value['u_type'] == 1 && !empty($value['u_image']))
                            $value['u_image'] = $this->baseurl.UPLOADS.'/user_image/'.$value['u_image'];
                        
                        unset($value['u_type']);
                        
                        $recomm[] = $value;
                    }
                }
                $pro_details['recommendation'] = $recomm;
                
                
                if($type == 2){
                    $where_bank = array('bd_provider' => $pro_id);
                    $bank_details = $this->get_record_where('bank_details', $where_bank);
                    $pro_details['bank_details'] = $bank_details;
                }
                
                $response = array('status' => 'true', 'message' => 'Provider details found', 'provider_details' => $pro_details);
            } else {
                $response = array('status' => 'false', 'message' => 'No records found');
            }
        } else {
            $response = array('status' => 'false', 'message' => 'Invalid request parameter');
        }
        $this->json_output($response);
    }

}

$p_details = new ProviderDetails();
$p_details->p_details();