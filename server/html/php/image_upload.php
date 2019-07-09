<?php
/**
 * Provides functions to process uploaded images
 */

// Include the PEL library
require_once __DIR__.'/pel/PelJpeg.php';
set_include_path('/pel' . PATH_SEPARATOR . get_include_path());
use lsolesen\pel\PelJpeg;
use lsolesen\pel\PelExif;
use lsolesen\pel\PelTiff;
use lsolesen\pel\PelIfd;
use lsolesen\pel\PelEntryLong;
use lsolesen\pel\PelTag;
use lsolesen\pel\PelEntryUserComment;
use lsolesen\pel\PelEntryByte;
use lsolesen\pel\PelEntryAscii;
use lsolesen\pel\PelEntryRational;
use lsolesen\pel\PelEntryCopyright;

spl_autoload_register(function ($class) {
    if (substr_compare($class, 'lsolesen\\pel\\', 0, 13) === 0) {
        $classname = str_replace('lsolesen\\pel\\', '', $class);
        $load = realpath(__DIR__ . DIRECTORY_SEPARATOR . 'pel' . DIRECTORY_SEPARATOR . $classname . '.php');
        if ($load !== false) {
            include_once realpath($load);
        }
    }
});

function tryHandleImageUpload(){
    // Get the parameters from post
    $location_lat = isset($_POST["location_lat"]) ? $_POST["location_lat"] : "";
    $location_lon = isset($_POST["location_lon"]) ? $_POST["location_lon"] : "";

    $category = isset($_POST["category"]) ? $_POST["category"] : "";
    $photographer = isset($_POST["photographer"]) ? $_POST["photographer"] : "";
    $overwrite_location = isset($_POST["overwrite_location"]);

    $success = True;

    // Create the target directory if needed
    $dirname = __DIR__.'/../pictures';
    if (!file_exists($dirname)) {
        $success = mkdir($dirname, 0777, true);
    }

    if(!$success){
	throw new Exception("Could not find or create the pictures directory");
    }


    // Loop through each posted file
    $totalFiles = count($_FILES['upload']['name']);
    $errors = array();
    for($i=0; $i<$totalFiles; $i++) {

        //Get the temp file path
        $tmpFilePath = $_FILES['upload']['tmp_name'][$i];

        if ($tmpFilePath != ""){
            // Process the uploaded file
            try {
                processUploadedImageFile(
                $tmpFilePath, $_FILES['upload']['name'][$i], $dirname, $photographer,
                $location_lat, $location_lon, $category, $overwrite_location, $i);
            }
	    catch (Exception $e) {
	    	$success = False;
                array_push($errors, "Error processing file ".$i.":".($e->getMessage()));
            }
        }
        else if ($_FILES['upload']['error'][$i] != UPLOAD_ERR_OK){
            $success = False;
	    array_push($errors, "Error uploading file ".$i.":".$_FILES['upload']['error'][$i]);
        }
    }
    if (count($errors)>0) {
	$msg = "";
	foreach ($errors as $error) {
		$msg = $msg . $error."<br>";
	}
	throw new Exception($msg);
    }
    return $success;
}


/**
 * Processes an uploaded image file by converting it and adding EXTIF informations
 */
function processUploadedImageFile($tempPath, $originalFilename, $targetDirectory, $photographer, 
                        $location_lat, $location_lon, $category, $overwrite_location, $index){

    
    $newFilePath = $targetDirectory. "/".$category.$index.".jpg";
    while (file_exists($newFilePath)){
        $index++;
        $newFilePath = $targetDirectory. "/".$category.$index.".jpg";
    }



    // This converts the file if needed and saves it at the desired location
    if(!convertToJpeg($tempPath, $originalFilename, $newFilePath)){
	throw new Exception ("Could not convert to jpeg");
    }

    // Assign the exif data to the jpg image
    $exifResult =  assignExif($newFilePath, $photographer, $location_lat, $location_lon, $country, $overwrite_location);
    if(!$exifResult) {
	throw new Exception("Could not set Exif data");
    }
}


/**
 * Converts the file from png to jpeg if needed.
 */
function convertToJpeg($path, $originalFilename, $newPath){

    $extension = strtolower(pathinfo($originalFilename)["extension"]);
    // Load the image using the right format
    if($extension == "png"){
        $image = imagecreatefrompng($path);
    }
    else if($extension == "jpg" || $extension == "jpeg"){
        copy ($path , $newPath);
        return true;
    }
    else{
	throw new Exception("Unsupported file extension :".pathinfo($originalFilename)["extension"]);
        return false;
    }


    // Save the image as jpg
    if(isset($image)){
        imagejpeg($image, $newPath, 100);
        imagedestroy($image);

        return true;
    }

    return false;
}

/**
 * Assigns the EXIF data tags to the jpg images
 */
function assignExif($path, $photographer, $location_lat, $location_lon, $country, $overwrite_location){
    

    // Load the image
    $jpeg = new PelJpeg($path);
    
    // Try to get existing GPS informations
    $gps_ifd = getExistingGpsIfd($jpeg);

    // Get the IFD tag
    $ifd0 = $jpeg->getExif()->getTiff()->getIfd();

    // Create a new gps informations when needed
    if($gps_ifd == null){
        $gps_ifd = new PelIfd(PelIfd::GPS);
        $ifd0->addSubIfd($gps_ifd);

        $overwrite_location = true;
    }

    // Overwrite the location when needed
    if($overwrite_location){
        $exif_ifd = new PelIfd(PelIfd::EXIF);
        $ifd0->addSubIfd($exif_ifd);

        $inter_ifd = new PelIfd(PelIfd::INTEROPERABILITY);
        $ifd0->addSubIfd($inter_ifd);
        
        // Add the GPS version
        $gps_ifd->addEntry(new PelEntryByte(PelTag::GPS_VERSION_ID, 2, 2, 0, 0));
        
        // Add the latitude
        list ($hours, $minutes, $seconds) = convertDecimalToDMS($location_lat);
        $latitude_ref = ($location_lat < 0) ? 'S' : 'N';
        $gps_ifd->addEntry(new PelEntryAscii(PelTag::GPS_LATITUDE_REF, $latitude_ref));
        $gps_ifd->addEntry(new PelEntryRational(PelTag::GPS_LATITUDE, $hours, $minutes, $seconds));
    
        // Add the longitude
        list ($hours, $minutes, $seconds) = convertDecimalToDMS($location_lon);
        $longitude_ref = ($location_lon < 0) ? 'W' : 'E';
        $gps_ifd->addEntry(new PelEntryAscii(PelTag::GPS_LONGITUDE_REF, $longitude_ref));
        $gps_ifd->addEntry(new PelEntryRational(PelTag::GPS_LONGITUDE, $hours, $minutes, $seconds));
    
    }

    $photographer = unpack('C*', mb_convert_encoding($photographer, 'UTF-16BE', 'UTF-8'));
    $photographer[] = 0;
    $photographer[] = 0;

    $country = unpack('C*', mb_convert_encoding($country, 'UTF-16BE', 'UTF-8'));
    $country[] = 0;
    $country[] = 0;

    $desc = new PelEntryByte(PelTag::ARTIST, $photographer);
    $ifd0->addEntry($desc);

    $desc = new PelEntryByte(PelTag::IMAGE_DESCRIPTION, $country);
    $ifd0->addEntry($desc);

    // Save the file
    $bytes = $jpeg->getBytes();

    $success = file_put_contents($path, $bytes);

    if($success === false){
        return false;
    }

    return true;
}

/**
 * Tries to extract exissting gps informations from a PelJpeg instance. 
 * Note: This function will add exif, tiff and ifd0 elements to the jpeg if
 * not already available
 */
function getExistingGpsIfd($jpeg){
    $exif = $jpeg->getExif();

    if($exif == null){
        $exif = new PelExif();
        $jpeg->setExif($exif);
    }

    $tiff = $exif->getTiff();
    if($tiff == null){
        $tiff = new PelTiff();
        $exif->setTiff($tiff);
    }
    
    $ifd0 = $tiff->getIfd();
    if($ifd0 == null){
        $ifd0 = new PelIfd(PelIfd::IFD0);
        $tiff->setIfd($ifd0);
    }

    $gps_ifd = $ifd0->getSubIfd(PelIfd::GPS);
    
    return $gps_ifd;
}


/**
 * Typical string ends with test
 */
function endsWith($text, $pattern)
{
    $length = strlen($pattern);
    return $length === 0 || (substr($text, -$length) === $pattern);
}

/**
 * Helper function which converts decimal coordinates to dms
 */
function convertDecimalToDMS($degree)
{
    if ($degree > 180 || $degree < - 180) {
        return null;
    }
    $degree = abs($degree);
    $seconds = $degree * 3600;
    $degrees = floor($degree);
    $seconds -= $degrees * 3600;
    $minutes = floor($seconds / 60);
    $seconds -= $minutes * 60;
    $seconds = round($seconds * 100, 0);

    return [[$degrees, 1], [$minutes, 1], [$seconds, 100]];
}
?>
