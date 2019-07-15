<?php 


function getRandomPictures($limit){
    $files = getAllPictures();
    $filesCount = sizeof($files);

    if($filesCount == 0){
        return [];
    }

    $used_files = [];

    for ($i = 0; $i < $limit; $i++) {
        // Get the image at i + the random start index
        $img_filename = $files[(rand(0, $filesCount)) % $filesCount];


        if(!in_array($img_filename, $used_files)){
            $used_files[] = $img_filename;
        }
    }

    return $used_files;
}

function getAllPictures(){
    $files = array_merge(glob(__DIR__."/../pictures/*/*.jpg"), glob(__DIR__."/../pictures/*.jpg"));
    
    $images = [];

    for($i = 0; $i < sizeof($files); $i++){
        // Remove the absolute part of the path
        $images[] = str_replace(__DIR__."/../", "", $files[$i]);
    }
    
    return $images;
}

function pictureExists($path){
    return file_exists(__DIR__."/../".$path);
}

function deletePicture($path){
     // Images must be in the images directory. Do not allow pictures elsewhere
     if (substr($path, 0, strlen("pictures/")) !== "pictures/") return false;
     // All images must be jpg. Do not allow other file types, e.g. deleting PHP scripts
     if (substr($path, -strlen(".jpg")) !== ".jpg") return false;
     // Do not allow going up in the directory structure
     if (strpos($path, "/../") !== false) return false;
     // Ensure that full path is inside our directory
     if (strpos(realpath(__DIR__."/../".$path), realpath(__DIR__."/../pictures/")) === 0)
     {
         if(pictureExists($path)){
             return unlink(__DIR__."/../".$path);
         }
     }
     return false;
}


?>
