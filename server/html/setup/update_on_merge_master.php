<?php
$requestBody = file_get_contents('php://input');

$parameters = json_decode($requestBody, TRUE);

if($parameters["object_kind"] == "merge_request"){
    if($parameters["object_attributes"]["target_branch"] == "master" || $parameters["object_attributes"]["target_branch"] == "develop_self_update"){
        if($parameters["object_attributes"]["action"] == "merge"){
            echo shell_exec("git pull");
        }
    }
}

?>