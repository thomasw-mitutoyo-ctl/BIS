<?php

require_once __DIR__.'/../php/pel/PelJpeg.php';
set_include_path('/../php/pel' . PATH_SEPARATOR . get_include_path());
use lsolesen\pel\PelJpeg;
use lsolesen\pel\PelExif;
spl_autoload_register(function ($class) {
    if (substr_compare($class, 'lsolesen\\pel\\', 0, 13) === 0) {
        $classname = str_replace('lsolesen\\pel\\', '', $class);
        $load = realpath(__DIR__ . DIRECTORY_SEPARATOR . '../php/pel' . DIRECTORY_SEPARATOR . $classname . '.php');
        if ($load !== false) {
            include_once realpath($load);
        }
    }
});
use lsolesen\pel\Pel;
//Pel::setDebug(true);


function bestfit($name, $box_w, $box_h, $debug=false) {
    $img = imagecreatefromjpeg($name);

    if ($debug) { echo "Screen: ".$box_w."x".$box_h."<br>"; }
    //create the image, of the required size
    $new = imagecreatetruecolor($box_w, $box_h);
    if($new === false) {
        //creation failed -- probably not enough memory
	imagedestroy($img);
        return null;
    }
    if ($debug) { echo "Image: ".imagesx($img)."x".imagesy($img)."<br>"; }

    //compute resize ratio
    $hratio = $box_h / imagesy($img);
    $wratio = $box_w / imagesx($img);
    if ($debug) { echo "Ratios: ".$hratio." or ".$wratio."<br>"; }

    $ratio = max($hratio, $wratio);

    //compute sizes
    $sy = floor(imagesy($img) * $ratio);
    $sx = floor(imagesx($img) * $ratio);
    if ($debug) { echo "Size: ".$sx."x".$sy."<br>"; }


    //compute margins
    //Using these margins centers the image in the thumbnail.
    //If you always want the image to the top left, 
    //set both of these to 0
    $m_y = floor(($box_h - $sy) / 2);
    $m_x = floor(($box_w - $sx) / 2);
    if ($debug) { echo "Margin: ".$m_x.", ".$m_y."<br>"; }


    //Copy the image data, and resample
    //
    //If you want a fast and ugly thumbnail,
    //replace imagecopyresampled with imagecopyresized
    if(!imagecopyresampled($new, $img,
        $m_x, $m_y, //dest x, y (margins)
        0, 0, //src x, y (0,0 means top left)
        $sx, $sy,//dest w, h (resample to this size (computed above)
        imagesx($img), imagesy($img)) //src w, h (the full size of the original)
    ) {
        //copy failed
        imagedestroy($new);
	imagedestroy($img);
        return null;
    }

    // apply Exif data
    $originaljpg = new PelJpeg($name);
    $exif = $originaljpg->getExif();
    if (!is_null($exif)) {
	if ($debug) { echo "Exif data found"; }
        $result = new PelJpeg($new);
        $result->setExif($exif);
    }
    else {
	if ($debug) { echo "No Exif data"; }
        $result = new PelJpeg($new);
    }

    //copy successful
    return $result->getBytes();
}


$name=$_GET["p"];
if (!file_exists($name)) {
	header("HTTP/1.1 404 Not found");
	exit();
}

$width=$_GET["w"];
$height=$_GET["h"];

$fit = bestfit($name, $width, $height);

if(is_null($fit)) {
    header('HTTP/1.1 500 Internal Server Error');
    exit();
}
header("Cache-Control: no-cache");
//header('Content-Type: text/html');
header('Content-Type: image/jpeg');
echo $fit;
?>
