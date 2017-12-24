<?php
require './db.class.php';

class DeleteCard extends DB {

    function card_delete() {
        if(!empty($_REQUEST['card_id'])){
            $where = array('card_id' => $_REQUEST['card_id']);
            $card_Details = $this->get_record_where('credit_card', $where, 'card_token');
            $token = $card_Details[0]['card_token'];
            
            /*---Sandbox Account---*/
//            $clientId = "AU7HqvXbs652uXKXycUaopbt6bXQBdcUn843CVv-iXPpJE4qdJRNLUnqcf2ewkvCeeZm8B4n2bVSZIh_";
//            $secret = "EGnozeEiKuEHh0rKyQj6_RPFAB9QzFFElRgyWYxsEoYo1eI2wEHZ6B_l9-DgYGUHr0GP_UDlOVXOenA4";
            
            $clientId = "AWn3BP7SUmFI9LHkEZl-hEr5dDC1Hu4-CO2NPLqaPnulDKjNlS-Q7u-UqRRnOlifj9ZM-Ip7Gpg0xwh6";
            $secret = "EPTcuWXe3jvKbpUGpP54tupPfPSWG4Abx6dEcnQ7GLHjNb-VXfz7a3lQWZBsTZgHQBA_F67izapxQzHd";
            
//            $clientId = "AaoJxLmNIHr_sXQeJa1pOGMS8LwIgfNasrDEnymrb5Tr-C0FtiEATKPCI4ZL1v5mzLmtB0XLRaK8cyJN";
//            $secret = "EH3f7HSlusjFzAhQO2x0s74iMyGwc3xW9Mr-gljlpV5XdFq0Mr9KXAa8A6aJ5nJxgqcdvSrYmL7-W9uU";

            $ipnexec = curl_init();
            curl_setopt($ipnexec, CURLOPT_URL, "https://api.sandbox.paypal.com/v1/oauth2/token");
//            curl_setopt($ipnexec, CURLOPT_URL, "https://api.paypal.com/v1/oauth2/token");

            curl_setopt($ipnexec, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ipnexec, CURLOPT_POST, true);
            curl_setopt($ipnexec, CURLOPT_USERPWD, $clientId . ":" . $secret);
            curl_setopt($ipnexec, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
            curl_setopt($ipnexec, CURLOPT_RETURNTRANSFER, true);
            $ipnresult = curl_exec($ipnexec);
            $result = json_decode($ipnresult);

            // access token
            $access_token = $result->access_token;

            curl_close($ipnexec);


            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, "https://api.sandbox.paypal.com/v1/vault/credit-cards/$token");
//            curl_setopt($ch, CURLOPT_URL, "https://api.paypal.com/v1/vault/credit-cards/$token");
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_POST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . $access_token));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $result = curl_exec($ch);

            curl_close($ch);
            
            $this->delete_record('credit_card', $where);
            $response = array('status' => 'true', 'message' => 'Card deleted successfully');
        } else {
            $response = array('status' => 'false', 'message' => 'Invalid request parameter');
        }
        $this->json_output($response);
    }

}

$del_Card = new DeleteCard();
$del_Card->card_delete();