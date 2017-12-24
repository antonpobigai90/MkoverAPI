<?php
require './db.class.php';

class AddPortfolio extends DB {

    function portfolio_add() {
        if(!empty($_REQUEST['pro_id']) && !empty($_FILES['image'])){
            if (!empty($_FILES['image'])) {

                $target_dir = "../uploads/portfolio/";
                $name = time() . basename($_FILES["image"]["name"]);
                $target_file = $target_dir . $name;
                $uploadOk = 1;
                $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
                
                // Check if image file is a actual image or fake image
                $check = getimagesize($_FILES["image"]["tmp_name"]);
                if ($check !== false) {
                    $uploadOk = 1;
                } else {
                    $error = "File is not an image.";
                    $uploadOk = 0;
                }
                
                // Check if file already exists
                if (file_exists($target_file)) {
                    $error = "Sorry, file already exists.";
                    $uploadOk = 0;
                }
                
                // Check file size
                if ($_FILES["image"]["size"] > 2097152) {
                    $error = "Sorry, your file is too large. File must be less than 2 MB.";
                    $uploadOk = 0;
                }
                
                // Allow certain file formats
                if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                    $error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                    $uploadOk = 0;
                }
                
                // Check if $uploadOk is set to 0 by an error
                if ($uploadOk == 0) {
                    $response = array('status' => 'false', 'message' => $error);
                } else {
                    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                        $data['pp_image'] = $name;
                        $data['pp_provider'] = (int)$_REQUEST['pro_id'];
                        $data['pp_datetime'] = date('Y-m-d H:i:s');
                        $pp_id = $this->insert_records('provider_portfolio', $data);
                        
                        $portfolio_list = array();
                        $where = array('pp_provider' => (int)$_REQUEST['pro_id']);
                        $get_portfolio = $this->get_record_where('provider_portfolio', $where);
                        if(!empty($get_portfolio)){
                            foreach ($get_portfolio as $value) {
                                $value['pp_image'] = $this->baseurl.UPLOADS.'/portfolio/'.$value['pp_image'];
                                unset($value['pp_datetime']);
                                $portfolio_list[] = $value;
                            }
                        }
                        
                        $response = array('status' => 'true', 'message' => "Image uploaded successfully", 'portfolio' => $portfolio_list);
                    } else {
                        $response = array('status' => 'false', 'message' => "Sorry, there was an error uploading your file.");
                    }
                }
            }
        } else {
            $response = array('status' => 'false', 'message' => 'Invalid request parameter');
        }
        $this->json_output($response);
    }

}

$add_port = new AddPortfolio();
$add_port->portfolio_add();