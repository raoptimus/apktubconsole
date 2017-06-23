<?php
ini_set('display_errors', 1);
date_default_timezone_set('UTC');
$im = new \Imagick('testimage.jpg');
$im->cropThumbnailImage(128, 128);
$file_name = 'testimage_v_' . time() . '.jpg';
$im->writeImage($file_name);

if (php_sapi_name() == "cli") {
    if(file_exists($file_name)) {
        echo("OK\n");
    } else {
        echo("FAILED\n");
    }
} else {
    header('Content-type: image/jpg');
    readfile($file_name);
}