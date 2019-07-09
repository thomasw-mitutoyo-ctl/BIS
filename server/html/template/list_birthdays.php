<?php
/**
 * Possible GET parameters:
 *  - command=[add|delete]
 *  - id (when command=delete)
 * 
 * Possbile POST parameters:
 *  - name
 *  - date
 * 
 */


// Create the database connection
require_once ("php/sql_config.php");
require_once ("php/database_proxy.php");
$db = connectToDatabase();

$errorMessage = "";
$successMessage = "";

if(isset($_GET['command']) && $_GET['command'] == "add"){
    $allDataPresent = isset($_POST['name']);
    $allDataPresent &= isset($_POST['date']);

    if($allDataPresent){
        $name = $_POST['name'];
        $date = date("Y-m-d", strtotime($_POST['date']));

        try{
            addNewBirthday($db, $name, $date);
            $successMessage = "Erfolgreich hinzugefügt!";
        }
        catch(Exception $e){
            $errorMessage = "Fehler beim Hinzufügen!";
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
            deleteExistingBirthday($db, $idToDelete);
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
$birthdays = getAllBirthdays($db);
$noTickers = sizeof($birthdays) == 0;
?>

<div class="row">
    <div class="col-md-1"></div>
    <div class="col-md-10" id="head">
        <h1 class="MonthPanel">Alle Geburtstage</h1>
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
                                echo '<div class="no-entries"><p><span class="glyphicon glyphicon-bell"></span></p><p>Keine Geburtstage</p></div>';
                            }
                            else{
                                echo '
                                <thead>
                                    <tr>
                                        <th>Tag</th>
                                        <th>Name</th>
                                        <th>Optionen</th>
                                    </tr>
                                </thead>';
                            }
                        ?>
                        <tbody>
                        <?php
                            foreach ($birthdays as $d) {
                                include ("birthday_row.php");
                            }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
