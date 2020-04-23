<?php

require_once __DIR__."/http_helpers.php";
require_once __DIR__."/../php/_global_settings.php";

function handleSetupRequest(){
    $state = $_GET["state"];

    if(!isset($state) || $state === "checkprerequisites"){
        checkPrerequisites();
    }
    else if (isset($state) && $state === "requestsettings"){
        requestSettings();
    }
    else if (isset($state) && $state === "checksettings"){
        checkSettings();
    }
    else if (isset($state) && $state === "install"){
        require_once __DIR__ . '/install.php';

        session_start();
        $settings["mysqlServer"] = $_SESSION["mysqlServer"];
        $settings["mysqlUsername"] = $_SESSION["mysqlUsername"];
        $settings["mysqlPassword"] = $_SESSION["mysqlPassword"];
        $settings["mysqlDatabaseName"] = $_SESSION["mysqlDatabaseName"];
        $settings["setupDBSchema"] = $_SESSION["setupDBSchema"];
        
        $settings["weatherServer"] = $_SESSION["weatherServer"];
        $settings["weatherPort"] = $_SESSION["weatherPort"];

        installServer($settings);
        
        createButton("Zur BIS Administration", "../admin.php");
    }
}

function checkPrerequisites(){
    $prerequisites_available = true;

    $prerequisites_available &= checkExtensions();
    $prerequisites_available &= checkGitInstalled();

    createButton("Installieren", "index.php?state=requestsettings");

    return $prerequisites_available;
}

function checkExtensions(){
    // Check if MySQLi is loaded
    if(!extension_loaded("mysqli")){
        echo "MySQLi Modul nicht geladen. Aktivieren Sie es in php.ini";
        return false;
    }

    return true;
}

function checkGitInstalled(){
    // Check if Git is installed
    if(strlen(exec("git")) === 0){
        echo "Git is not installed!";
        return false;
    }
    return true;
}


function requestSettings(){
    echo "<form action=\"index.php?state=checksettings\" method=\"post\">";
    global $config;
    $settings = parse_ini_file($config["DbIniFile"], false);
    createLabel("MySQL Konfiguration");
    createSettingsElement("MySQL Server", "MySQLServer", $settings["Server"]);
    createSettingsElement("MySQL Benutzername", "MySQLUsername", $settings["Username"]);
    createPasswordSettingsElement("MySQL Passwort für o.g. Benutzer", "MySQLPassword", $settings["Password"]);
    createSettingsElement("MySQL Datenbank", "MySQLDatabasename", $settings["DatabaseName"]);
    createBoolSettingsElement("Datenbank-Schema anlegen", "SetupDBSchema");
    $weather = parse_ini_file($config["WeatherIniFile"], false);
    createLabel("Wetter Daemon Konfiguration");
    createSettingsElement("Server", "WeatherDaemonServer", $weather["Server"]);
    createSettingsElement("Port", "WeatherDaemonPort", $weather["Port"]);

    echo "<p><input type=\"submit\" class=\"btn btn-primary\" /></p>";
    echo "</form>";
}

function checkSettings(){
    $mysqlServer = $_POST["MySQLServer"];
    $mysqlUsername = $_POST["MySQLUsername"];
    $mysqlPassword = $_POST["MySQLPassword"];
    $mysqlDatabaseName = $_POST["MySQLDatabasename"];
    $setupDBSchema = $_POST["SetupDBSchema"];

    $weatherServer = $_POST["WeatherDaemonServer"];
    $weatherPort = $_POST["WeatherDaemonPort"];

    // TODO: Find other way to pass the parameters
    global $settings;
    session_start();
    
    $_SESSION["mysqlServer"] = $mysqlServer;

    $_SESSION["mysqlUsername"] = $mysqlUsername;
    $_SESSION["mysqlPassword"] = $mysqlPassword;
    $_SESSION["mysqlDatabaseName"] = $mysqlDatabaseName;
    $_SESSION["setupDBSchema"] = $setupDBSchema;

    $_SESSION["weatherServer"] = $weatherServer;
    $_SESSION["weatherPort"] = $weatherPort;

    createLabel3("Bereit zum Installieren.");
    createButton("Zurück", "index.php?state=requestsettings");
    createButton("Installieren", "index.php?state=install");
}

?>
