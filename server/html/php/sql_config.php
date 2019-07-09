<?php
    function connectToDatabase(){
        require_once __DIR__ . "/_global_settings.php";

        $server = "";
        $username = "";
        $password = "";
        $database = "";
        getDbConfig($server, $username, $password, $database);

        require_once __DIR__ . '/sql_request.php';
        $db = connectDatabase($server, $username, $password, $database);

        return $db;
    }
?>