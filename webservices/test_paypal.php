<?php
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


$header = array("content-type: application/json", "Authorization: Bearer A101.aH7CS63znGVld4NfAKmgNoXSVDnGGBu-50l-ZiNhBL_fNfnV1CznoDsI-95KLicr.C544i2maN0nm3dJ0yt2Q8byxY2K");

$post = '{"payer_id": "user12345", "type": "visa", "number": "4417119669820331", "expire_month": "11", "expire_year": "2018", "first_name": "Joe", "last_name": "Shopper"}';

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, "https://api.sandbox.paypal.com/v1/vault/credit-card");
curl_setopt($curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
curl_setopt($curl, CURLOPT_HEADER, $header);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
curl_setopt($curl, CURLOPT_POSTFIELDS, $post);

$result = curl_exec($curl);
$err = curl_error($curl);

if (!empty($err)) {
    print_r($err);
}

if(empty($result))die("Error: No response.");
else
{
    $json = json_decode($result);
    print_r($json);
}

curl_close($curl);