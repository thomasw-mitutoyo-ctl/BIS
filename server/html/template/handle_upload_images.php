<?php

require_once __DIR__."/../php/image_upload.php";

$success = True;

try{
    $success = tryHandleImageUpload();
}
catch(Exception $ex){
    $success = False;
	echo $ex;
}

if($success){
    $successMessage = "Erfolgreich hochgeladen!";
}
else{
    $errorMessage = "Fehler beim Hochladen!\nMöglicherweise wurde keine Datei angegeben oder sie überschreitet die die maximale Dateigröße";
}

?>

<div class="row">
    <div class="col-md-1"></div>
    <div class="col-md-10" id="head">
        <h1 class="MonthPanel">Bilder hochladen</h1>
    </div>
</div>

<div class="row" >
    <div class="col-md-1"></div>
    <div class="col-md-10 nopad">
        <?php include "error_success.php"; ?>
    </div>
</div>
