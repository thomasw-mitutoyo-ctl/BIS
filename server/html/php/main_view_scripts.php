<?php
/*

This is the backend script of the main view 

*/

require_once __DIR__ . '/sql_request.php';
require_once __DIR__ . '/database_proxy.php';
require_once __DIR__ . '/image_proxy.php';
require_once __DIR__ . '/_global_settings.php';

function generateBackgroundItems(){

    $style = "item active background_image";
    
    $used_files = getRandomPictures(2);

    for ($i = 0; $i < sizeof($used_files); $i++) {
        echo '<div class="'.$style.'" id="container_'.$i.'"><img id="background_'.$i.'" class="background_image" src="'.$used_files[$i].'"></div>';
        $style = "item background_image";
    }
}

?>
