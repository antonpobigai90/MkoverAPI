<?php

$uploaddir = '/var/www/html/bookmwah/uploads/';
$uploadfile = $uploaddir .time(). basename($_FILES['avatar']['name']);

if (move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadfile)) {
    echo "File is valid, and was successfully uploaded.\n";
} else {
    echo "Possible file upload attack!\n";
}


//echo 'Hello';
//if($_GET['action'] == 'login'){
//    $data = json_decode(file_get_contents("php://input"));
    
//    print_r($data);
//    print_r($_POST);
//    print_r($_FILES['avatar']);
    
//    if($data->email_addr == 'prashant.ypsilon@gmail.com' && $data->password == '123456')
//    {
//        $response = array('status' => TRUE, 'message' => 'Login successful', 'user_id' => 1);
//    }
//    else
//    {
//        $response = array('status' => FALSE, 'message' => 'Invalid email or password');
//    }
//    
//    header('Content-Type: application/json');
//    echo json_encode($response);
    
//}

?>