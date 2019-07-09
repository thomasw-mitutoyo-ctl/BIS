<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../php/sql_request.php';
require_once __DIR__ . '/../php/database_proxy.php';
require_once __DIR__ . '/../php/_global_settings.php';

// Load the credentials from the configuration and connect to the database
$server = "";
$username = "";
$password = "";
$database = "";
getDbConfig($server, $username, $password, $database);

$db = connectDatabase($server, $username, $password, $database);

$rows = getAllBirthdays($db);

echo json_encode($rows, JSON_PRETTY_PRINT);

?>
