<?php
/*

Provides a stream of appointments. When a new appointment was added this feed will
Return the new appointment list as json
GET Parameters:
    - relevant: value: true | If true, only the relevant appointments are returned (optional)
    - date: value: DD-MM-YYYY

*/

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');


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

$sent_rows_json = "";
$i = 0;

// Start the loop which checks for new appointments. After 10 seconds it stops to free up server
// resources. 
// Unfortionaly the approach with the loop is slow on the developmen php server 
while($i < 10){
    $rows = "";
    if($all){
        $rows = getAllAppointments($db);   
    }
    else{
        $rows = getRelevantAppointments($db, $date);
    }

    $json = json_encode($rows);

    // Do a simple string comparison to check if something changed
    if($json != $sent_rows_json){
        echo "data: ".$json."\n\n";
        ob_flush();
        flush();
        //die();
        $sent_rows_json = $json;
    }

    sleep(1);
    $i++;
}

?>
