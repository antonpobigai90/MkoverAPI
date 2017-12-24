<?php
require './db.class.php';

class CardList extends DB {

    function card_list() {
        if(!empty($_REQUEST['user_id'])){
            $where = array('u_id' => (int)$_REQUEST['user_id']);
            $wallet_balance = $this->get_record_where('users', $where);
            
            $balance = (int)$wallet_balance[0]['u_wallet'];
            
            $where_card = array('card_u_id' => (int)$_REQUEST['user_id']);
            $credit_cards = $this->get_record_where('credit_card', $where_card, 'card_id, card_number');
            
            $response = array('status' => 'true', 'message' => 'Card Found', 'wallet_balance' => (string)$balance, 'credit_cards' => $credit_cards);
        } else {
            $response = array('status' => 'false', 'message' => 'Invalid request parameter');
        }
        $this->json_output($response);
    }

}

$booking = new CardList();
$booking->card_list();