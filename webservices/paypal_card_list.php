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
//echo "<pre>";
$access_token = $result->access_token;
//print_r($result->access_token);
$token_type = $result->token_type;
curl_close($ipnexec);



$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://api.sandbox.paypal.com/v1/vault/credit-cards");
curl_setopt($ch, CURLOPT_POST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
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

curl_close($ch);