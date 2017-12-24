<?php
require './db.class.php';

class CheckThumb extends DB {

    function thumb_c() {
        $im = new Imagick();
//        if(extension_loaded('imagick')) {
//            echo 'Imagick Loaded';
//        }
//        print_r($_FILES['image']);
    }

}

$c = new CheckThumb();
$c->thumb_c();