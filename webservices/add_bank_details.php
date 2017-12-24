<?php
require './db.class.php';

class BankDetails extends DB {

    function add_update_details() {
        if(!empty($_REQUEST['pro_id']) && !empty($_REQUEST['bank']) && !empty($_REQUEST['holder_name']) && !empty($_REQUEST['account_no'])){
            $data = array('bd_provider' => (int)$_REQUEST['pro_id'], 'bd_bank' => $_REQUEST['bank'], 'bd_holder_name' => $_REQUEST['holder_name'], 'bd_account_no' => $_REQUEST['account_no'], 'bd_datetime' => date('Y-m-d H:i:s'));
            
            if(!empty($_REQUEST['bd_id'])){
                $where = array('bd_id' => (int)$_REQUEST['bd_id']);
                $update = $this->update_records('bank_details', $data, $where);
                $response = array('status' => 'true', 'message' => 'Bank details updated successfully');
            } else {
                $where = array('bd_provider' => (int)$_REQUEST['pro_id']);
                $check_details = $this->get_record_where('bank_details', $where);
                if(!empty($check_details)){
                    $response = array('status' => 'false', 'message' => 'Bank details already exist');
                } else {
                    $insert = $this->insert_records('bank_details', $data);
                    $response = array('status' => 'true', 'message' => 'Bank details inserted successfully', 'bd_id' => $insert);
                }
            }
        } else {
            $response = array('status' => 'false', 'message' => 'Invalid request parameter');
        }
        $this->json_output($response);
    }

}

$bank_details = new BankDetails();
$bank_details->add_update_details();