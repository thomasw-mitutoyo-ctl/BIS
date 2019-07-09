<?php
/*

Provides a stream of tickers. When a new ticker was added this feed will
Return the new ticker list as json
GET Parameters:
    - relevant: value: true | If true, only the relevant tickers are returned (optional)
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

while($i < 10){
    $rows = "";
    if($all){
        $rows = getAllMessages($db);
    }
    else{
        $rows = getMessages($db, $date);
        
        $birthdays = getBirthdays($db, $date);

        for ($j=0; $j < sizeof($birthdays); $j++) { 
            $birthdays[$j]["name"] = "Happy birthday ".$birthdays[$j]["name"];
        }

        // Add missed birthdays from the weekend
        if(date('D', $date) === 'Mon') {
            $belatedBirthdays = getBirthdays($db, $date - 24 * 3600);

            foreach($belatedBirthdays as $b) {
                $b["name"] = $b["name"]." had a birthday yesterday";
                $birthdays[] = $b;
            }

            $belatedBirthdays = getBirthdays($db, $date - 24 * 3600 * 2);
            
            foreach($belatedBirthdays as $b) {
                $b["name"] = $b["name"]." had a birthday two days ago";
                $birthdays[] = $b;
            }
        }

        foreach($birthdays as $b){

            $birthday["message"] = $b["name"];
            $birthday["date"] = $b["date"];

            $rows[] = $birthday;
        }
        
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

    $i++;
}

?>
