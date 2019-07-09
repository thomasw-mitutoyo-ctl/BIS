<?php
/*

Api to access the appointments in the database.
GET Parameters:
    - relevant: value: true | If true, only the relevant appointments are returned (optional)
    - date: value: DD-MM-YYYY

*/


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

$all = isset($_GET['relevant']) ? $_GET['relevant'] != 'true' : TRUE;
$date = isset($_GET['date']) ? strtotime($_GET['date']) : time();


if($all){
    $rows = getAllAppointments($db);   
}
else{
    $rows = getRelevantAppointments($db, $date);
}

echo json_encode($rows, JSON_PRETTY_PRINT);

?>
