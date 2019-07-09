<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../php/image_proxy.php';


if(isset($_GET["delete"]) && $_GET["delete"] == "true"){
    if(isset($_GET["id"])){
        $path = $_GET["id"];

        if(pictureExists($path)){
            $result["success"] = deletePicture($path);
            echo json_encode($result, JSON_PRETTY_PRINT);
        }
        else{
            $result["success"] = false;
            echo json_encode($result, JSON_PRETTY_PRINT);
        }

    }
    else{
        $result["success"] = false;
        echo json_encode($result, JSON_PRETTY_PRINT);
    }
}
else{
    // Get the images based on the parameter
    $images = [];

    if(isset($_GET["limit"])){
        if($_GET["limit"] == "all"){
            $images = getAllPictures();
        }
        else{
            $images = getRandomPictures(intval($_GET["limit"]));
        }
    }
    else{
        $images = getRandomPictures(1);
    }
    
    echo json_encode($images, JSON_PRETTY_PRINT);    
}

?>
