<?php

function createSuccessMessage($message){
    echo '<div class="alert alert-success">' . $message . '</div>';
}

function createErrorMessage($message){
    echo '<div class="alert alert-danger">' . $message . '</div>';
}

function createSettingsElement($text, $key, $value = ""){

    echo '<div class="form-group">
    <label for="' . $key . '">' . $text . '</label>
    <input type="text" class="form-control" name="' . $key . '" value="' . $value . '">
    </div>';
}

function createPasswordSettingsElement($text, $key, $value=""){

    echo '<div class="form-group">
    <label for="' . $key . '">' . $text . '</label>
    <input type="password" class="form-control" name="' . $key . '" value="' . $value . '">
    </div>';
}

function createBoolSettingsElement($text, $key){
    echo '
    <div class="form-check">
        <input type="checkbox" class="form-check-input" name="' . $key . '">
        <label class="form-check-label" for="' . $key . '">' . $text . '</label>
    </div>';
}

function createLabel($text){
    echo '<h1>' . $text . '</h1>';
}
function createLabel2($text){
    echo '<h2>' . $text . '</h2>';
}
function createLabel3($text){
    echo '<h3>' . $text . '</h3>';
}
function createLabel4($text){
    echo '<h4>' . $text . '</h4>';
}

function createButton($text, $link){
    echo "<a href=\"" . $link . "\" class=\"btn btn-warning btn-lg wordbreak\" role=\"button\">" . $text . "</a>";
}


?>