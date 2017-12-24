<?php
require './db.class.php';

class Chatlist extends DB {

    function chat_list() {

        if(!empty($_REQUEST['sender_id']) && !empty($_REQUEST['receive_id'])){
            
            $chatlist_query = "SELECT * FROM chat_messages WHERE (sender_id = '".$_REQUEST['sender_id']."' AND receive_id = '".$_REQUEST['receive_id']."') or (sender_id = '".$_REQUEST['receive_id']."' and receive_id = '".$_REQUEST['sender_id']."') ORDER BY date_time ASC";
            
            $lists = $this->query_result($chatlist_query);
            
            if($lists){
                //print_r($lists);
                for($i = 0; $i < count($lists); $i++){
                    $temp_list[] = array("message"=>$lists[$i]['message'],"datetime"=>$lists[$i]['date_time'],"sender_id"=>$lists[$i]['sender_id'],"receive_id"=>$lists[$i]['receive_id']);
                    
                    $where = array('sender_id' => $lists[$i]['sender_id'], 'receive_id' => $lists[$i]['receive_id']);
                    $update_data = array('r_checked' => '1');
                    $update_user = $this->update_records('chat_messages', $update_data, $where);
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

