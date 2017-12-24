<?php
$clientId = "AZGqA3o3kVkiNi74FRXx2Qynt94VKZQ6WmICt1iqG0Y6LM662hG56Xb7tYUDeUDxpADU4oAooJs8UqYF";
$secret = "EJcmlkx0_tWCtczZJadkv204klDa0F34fQ5E00X6iFOxk5_bo9mAkh7sys4HhqwKZbXLaGSp74oEtObg";

$ipnexec = curl_init();
curl_setopt($ipnexec, CURLOPT_URL, "https://api.sandbox.paypal.com/v1/oauth2/token"); // test url

curl_setopt($ipnexec, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ipnexec, CURLOPT_POST, true);
curl_setopt($ipnexec, CURLOPT_USERPWD, $clientId . ":" . $secret);
curl_setopt($ipnexec, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
curl_setopt($ipnexec, CURLOPT_RETURNTRANSFER, true);
$ipnresult = curl_exec($ipnexec);
$result = json_decode($ipnresult);
echo "<pre>";
$access_token = $result->access_token;
//print_r($result->access_token);
$token_type = $result->token_type;
curl_close($ipnexec);


// phase 2 for credit card payment

$scope = "https://api.sandbox.paypal.com/v1/vault/credit-card";
$expire_month = 04;
$expire_year = 2020;
$first_name = "Prashant";
$last_name = "Sharma";
$method = "storecreditcard";
$number = 5175515909806502;
$type = "mastercard";
$payer_id = "prashant.sharma@gmail.com";
$ch = curl_init();
//curl_setopt($ch, CURLOPT_HTTPHEADER, 1);
$data = '
 {
 "payer_id":"user1",
 "type":"mastercard",
 "number":"5175515909806502",
 "expire_month":"04",
 "expire_year":"2020",
 "first_name":"Prashant",
 "last_name":"Sharma"
}
';
curl_setopt($ch, CURLOPT_URL, $scope);
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . $access_token));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


$result = curl_exec($ch);

if (empty($result))
    die("Error: No response.");
else {
    $json = json_decode($result);
    print_r($json);
}
curl_close($ch);

/* * ************************** phase 3 ********************************** */
$ch = curl_init();

$data = '{
  "intent":"sale",
  "payer": {
    "payment_method": "credit_card",
    "funding_instruments": [
      {
        "credit_card_token":{
          "credit_card_id":"' . $json->id . '",
          "payer_id":"user1"
        }
      }
    ]
  },
  "transactions":[
    {
      "amount":{
        "total":"7.47",
        "currency":"USD"
      },
      "description":"This is the payment transaction description."
    }
  ]
}
';

curl_setopt($ch, CURLOPT_URL, "https://api.sandbox.paypal.com/v1/payments/payment");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . $access_token));

$result = curl_exec($ch);


if (empty($result))
    die("Error: No response.");
else {
    $json = json_decode($result);
    echo "<pre>";
    print_r($json);
}





//$ch = curl_init();
//$clientId = "AZGqA3o3kVkiNi74FRXx2Qynt94VKZQ6WmICt1iqG0Y6LM662hG56Xb7tYUDeUDxpADU4oAooJs8UqYF";
//$secret = "EJcmlkx0_tWCtczZJadkv204klDa0F34fQ5E00X6iFOxk5_bo9mAkh7sys4HhqwKZbXLaGSp74oEtObg";
//
//curl_setopt($ch, CURLOPT_URL, "https://api.sandbox.paypal.com/v1/oauth2/token");
//curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
//curl_setopt($ch, CURLOPT_HEADER, "Accept: application/json");
////curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//curl_setopt($ch, CURLOPT_POST, true);
//curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
//curl_setopt($ch, CURLOPT_USERPWD, $clientId.":".$secret);
//curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
//
//$result = curl_exec($ch);
//$err = curl_error($ch);
//
//if (!empty($err)) {
//    print_r($err); die;
//}
//
//if(empty($result))die("Error: No response.");
//else
//{
//    $json = json_decode($result);
//    print_r($json->access_token);
//}
//
//curl_close($ch);


//curl https://api.sandbox.paypal.com/v1/oauth2/token \
//-H "Accept: application/json" \
//-u "AQkquBDf1zctJOWGKWUEtKXm6qVhueUEMvXO_-MCI4DQQ4-LWvkDLIN2fGsd:EL1tVxAjhT7cJimnz5-Nsx9k2reTKSVfErNQF-CmrwJgxRtylkGTKlU4RvrX" \
//-d "grant_type=client_credentials"