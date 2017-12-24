<?php
$clientId = "AZGqA3o3kVkiNi74FRXx2Qynt94VKZQ6WmICt1iqG0Y6LM662hG56Xb7tYUDeUDxpADU4oAooJs8UqYF";
$secret = "EJcmlkx0_tWCtczZJadkv204klDa0F34fQ5E00X6iFOxk5_bo9mAkh7sys4HhqwKZbXLaGSp74oEtObg";

$ipnexec = curl_init();
curl_setopt($ipnexec, CURLOPT_URL, "https://api.sandbox.paypal.com/v1/oauth2/token");

curl_setopt($ipnexec, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ipnexec, CURLOPT_POST, true);
curl_setopt($ipnexec, CURLOPT_USERPWD, $clientId . ":" . $secret);
curl_setopt($ipnexec, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
curl_setopt($ipnexec, CURLOPT_RETURNTRANSFER, true);
$ipnresult = curl_exec($ipnexec);
$result = json_decode($ipnresult);
$access_token = $result->access_token;
curl_close($ipnexec);


$ch = curl_init();

$data = '{
  "intent":"sale",
  "payer": {
    "payment_method": "credit_card",
    "funding_instruments": [
      {
        "credit_card_token":{
          "credit_card_id":"CARD-44P89717PM711132GK5UQ5YY",
          "payer_id":"user1"
        }
      }
    ]
  },
  "transactions":[
    {
      "amount":{
        "total":"10.24",
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
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$result = curl_exec($ch);


if (empty($result))
    die("Error: No response.");
else {
    $json = json_decode($result);
    echo "<pre>";
    print_r($json);
}