<?php
require './db.class.php';

class Chatlist extends DB {

    function chat_list() {

        if(!empty($_REQUEST['sender_id'])){
            
            $chatlist_query = "SELECT * FROM users a LEFT JOIN chat_messages c ON (a.u_id=c.sender_id OR a.u_id=c.receive_id) WHERE a.u_id!='".$_REQUEST['sender_id']."' AND (sender_id = '".$_REQUEST['sender_id']."' OR receive_id = '".$_REQUEST['sender_id']."') GROUP By a.u_id ORDER BY c.date_time DESC";
            
            $lists = $this->query_result($chatlist_query);
            
            if($lists){
                //print_r($lists);
                for($i = 0; $i < count($lists); $i++){
                
                    $check_pass = array('r_checked' => '0', 'receive_id' => $_REQUEST['sender_id'], 'sender_id' => $lists[$i]['receive_id']);
                    $details = $this->get_record_where('chat_messages', $check_pass, "count(id) as ct");
                    
                    $photo_url = $this->baseurl.UPLOADS.'/user_image/'.$lists[$i]['u_image'];                        
                    $temp_list[] = array("u_id"=>$lists[$i]['u_id'], "name"=>$lists[$i]['u_fullname'], "u_image"=>$photo_url, "message"=>$lists[$i]['message'],"datetime"=>$lists[$i]['date_time'],"sender_id"=>$lists[$i]['sender_id'],"receive_id"=>$lists[$i]['receive_id'],"count"=>$details[0][ct]);
                }
                $response = array("status"=>"true","records"=>$temp_list);
            }
            else{
                $response = array('status' => 'false', 'message' => 'There is no data');
            }
            
        } else {
            $response = array('status' => 'false', 'message' => 'Invalid request parameter');
        }
        $this->json_output($response);
    }

}

$chatlist = new Chatlist();
$chatlist->chat_list();

