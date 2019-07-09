<?php
/**
 * Possible GET parameters:
 *  - command=[add|delete]
 *  - id (when command=delete)
 * 
 * Possbile POST parameters:
 *  - message
 *  - start
 *  - end
 * 
 */


// Create the database connection
require_once ("php/sql_config.php");
require_once ("php/database_proxy.php");
$db = connectToDatabase();

$errorMessage = "";
$successMessage = "";

if(isset($_GET['command']) && $_GET['command'] == "add"){
    $allDataPresent = isset($_POST['message']);
    $allDataPresent &= isset($_POST['start']);
    $allDataPresent &= isset($_POST['end']);

    if($allDataPresent){
        $message = $_POST['message'];
        $start = date("Y-m-d", strtotime($_POST['start']));;
        $end = date("Y-m-d", strtotime($_POST['end']));

        $valid = 
            strtotime($start) <= strtotime($end);

        if(!$valid){
            $errorMessage = "Manche Parameter sind ungültig!";
        }
        else{
            try{
                addNewMessage($db, $message, $start, $end);
                $successMessage = "Erfolgreich hinzugefügt!";
            }
            catch(Exception $e){
                $errorMessage = "Fehler beim Hinzufügen!";
            }
        }
    }
    else{
        $errorMessage = "Nicht alle Parameter sind gegeben!";
    }
}

if(isset($_GET['command']) && $_GET['command'] == "delete"){
    if(isset($_GET['id'])){
        $idToDelete = $_GET['id'];

        try{
            deleteExistingMessage($db, $idToDelete);
            $successMessage = "Erfolgreich gelöscht!";
        }
        catch(Exception $e){
            $errorMessage = "Fehler beim Löschen!";
        }
    }
    else{
        $errorMessage = "Keine Id angegeben!";
    }
}

// Get the events
$tickers = getAllMessages($db);
$noTickers = sizeof($tickers) == 0;
?>

<div class="row">
    <div class="col-md-1"></div>
    <div class="col-md-10" id="head">
        <h1 class="MonthPanel">Alle Tickereinträge</h1>
    </div>
</div>

<div class="row" >
    <div class="col-md-1"></div>
    <div class="col-md-10 nopad">
        <?php include "error_success.php"; ?>
    </div>
</div>

<div class="row">		
    <div class="col-md-1"></div>
    <div class="col-md-10 nopad">
        
        <div class="featurette" id="about">
            <div class="agenda">
                <div class="table-responsive card">
                    <table class="table table-condensed table-hover">
                        <?php 	
                            if($noTickers)
                            {
                                echo '<div class="no-entries"><p><span class="glyphicon glyphicon-bell"></span></p><p>Keine Tickereinträge</p></div>';
                            }
                            else{
                                echo '
                                <thead>
                                    <tr>
                                        <th>Starttag</th>
                                        <th>Endtag</th>
                                        <th>Ankündigungsinhalt</th>
                                        <th>Optionen</th>
                                    </tr>
                                </thead>';
                            }
                        ?>
                        <tbody>
                        <?php
                            foreach ($tickers as $msg) {
                                include ("ticker_row.php");
                            }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
