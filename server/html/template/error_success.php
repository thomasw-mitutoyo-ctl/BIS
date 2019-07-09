<?php 

if(isset($successMessage) && $successMessage != ""){
    
    echo '<div class="top"><div class="alert alert-success">';
    echo $successMessage;
    echo '</div></div>';
}

if(isset($errorMessage) && $errorMessage != ""){
    echo '<div class="top"><div class="alert alert-danger">';
    echo $errorMessage;
    echo '</div></div>';
}

?>