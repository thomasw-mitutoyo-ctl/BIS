<?php

global $config;

$config["DbIniFile"] = __DIR__ . "/../../config/db_settings.ini";
$config["WeatherIniFile"] = __DIR__ . "/../../config/weather_service_settings.ini";
$config["Title"] = "BIS";
$config["WelcomeTitles"] = ["Willkommen", "ようこそ", "Welcome"];

function getDbConfig(&$server, &$username, &$password, &$database){
    global $config;

    // load the settings from the ini file
    $settings = parse_ini_file($config["DbIniFile"], false);

    $server = $settings["Server"];
    $username = $settings["Username"];
    $password = $settings["Password"];
    $database = $settings["DatabaseName"];
}

function getWeatherDaemonConfig(&$server, &$port){
    global $config;

    // load the settings from the ini file
    $settings = parse_ini_file($config["WeatherIniFile"], false);

    $server = $settings["Server"];
    $port = $settings["Port"];
}

?>