<?php

require_once __DIR__."/http_helpers.php";

function installServer($args){

    // Setup the database
    try{
        $success = initializeDatabase($args);

        if($success){
            createSuccessMessage("Datenbank-Einrichtung erfolgreich");
        }
        else{
            createErrorMessage("Datenbank-Einrichtung fehlgeschlagen");
        }
    }catch (Exception $ex) {
        createErrorMessage("Datenbank-Einrichtung fehlgeschlagen");
    }

    // Setup the weather daemon
    try{
        installWeatherDaemon($args);

        createSuccessMessage("Wetter Daemon erfolgreich eingerichtet");
    }catch (Exception $ex) {
        createErrorMessage("Wetter Daemon Einrichtung fehlgeschlagen");
    }
}


/*
Tries to connect to the database and creates the tables if needed
*/
function initializeDatabase($args){
    require_once __DIR__ . '/../php/sql_request.php';
    
    $server = $args["mysqlServer"];
    $username = $args["mysqlUsername"];
    $password = $args["mysqlPassword"];
    $database = $args["mysqlDatabaseName"];

    $db = connectDatabase($server, $username, $password, $database);

    if($db != null){
        if($args["setupDBSchema"]){
            setupDatabase($db);
        }
    
        saveDbSettings($args);

        return true;
    }
    else{
        return false;
    }
}

/*
Saves the database settings to a file
*/
function saveDbSettings($args){
    $settings["Server"] = $args["mysqlServer"];
    $settings["Username"] = $args["mysqlUsername"];
    $settings["Password"] = $args["mysqlPassword"];
    $settings["DatabaseName"] = $args["mysqlDatabaseName"];

    require_once __DIR__ . "/../php/ini_write.php";
    writeIni($settings, __DIR__."/../../config/db_settings.ini");
}


function installWeatherDaemon($args){

    // Save the settings to an ini file
    $settings["Server"] = $args["weatherServer"];
    $settings["Port"] = $args["weatherPort"];

    require_once __DIR__ . "/../php/ini_write.php";
    writeIni($settings, __DIR__."/../../config/weather_service_settings.ini");
}

?>