<?php
$requestBody = file_get_contents('php://input');

$parameters = json_decode($requestBody, TRUE);

if($parameters["object_kind"] == "push"){
    echo shell_exec("git pull");
}

?>