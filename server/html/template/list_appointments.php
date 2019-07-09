<?php
/**
 * Possible GET parameters:
 *  - month
 *  - year
 *  - command=[add|delete|edit]
 *  - id (when command=delete)
 * 
 * Possbile POST parameters:
 *  - id
 *  - title
 *  - date (contains date + time)
 *  - description
 *  - start
 *  - end
 * 
 */


// Create the database connection
require_once ("php/sql_config.php");
require_once ("php/database_proxy.php");
$db = connectToDatabase();

// Get the parameters for 'today'
$month = isset($_GET["month"]) ? $_GET["month"] : date("m");
$year = isset($_GET["year"]) ? $_GET["year"] : date("Y");

try{
    $today = strtotime('01-'.$month.'-'.$year.'');
}
catch(Exception $ex){
    $today = time();
}

// Set the previous and next month
if($month - 1 == 0)
{
    $previousM = 12;
    $previousY = $year - 1;
}
else
{
    $previousM = $month - 1;
    $previousY = $year;
}


if($month + 1 == 13)
{
    $nextM = 1;
    $nextY = $year + 1;
}
else
{
    $nextM = $month + 1;
    $nextY = $year;
}

$errorMessage = "";
$successMessage = "";

if(isset($_GET['command']) && ($_GET['command'] == "add" || $_GET['command'] == "edit")){
    $allDataPresent = isset($_POST['title']);
    $allDataPresent &= isset($_POST['description']);
    $allDataPresent &= isset($_POST['start']);
    $allDataPresent &= isset($_POST['end']);
    $allDataPresent &= isset($_POST['date']);

    if($allDataPresent){
        $title = $_POST['title'];
        $description = $_POST['description'];
        $start = date("Y-m-d", strtotime($_POST['start']));;
        $end = date("Y-m-d", strtotime($_POST['end']));
        $date = $_POST['date'];

        $time = date("H:i", strtotime($date));
        $date = date("Y-m-d", strtotime($date));

        $valid = 
            strlen($title) <= 30 && 
            strlen($description) <= 30 && 
            strtotime($start) <= strtotime($end) &&
            strtotime($start) <= strtotime($date) &&
            strtotime($date) <= strtotime($end);

        if(!$valid){
            $errorMessage = "Manche Parameter sind ungültig!";
        }
        else{
            if(isset($_GET['command']) && $_GET['command'] == "edit"){
        
                if(isset($_POST['id'])){
                    $idToEdit = $_POST['id'];
        
                    try{
                        editExistingAppointment($db, $idToEdit, $title, $description, $start, $end, $date, $time);
                        $successMessage = "Erfolgreich bearbeitet!";
                    }
                    catch(Exception $e){
                        $errorMessage = "Fehler beim Bearbeiten!";
                    }
                }
                else{
                    $errorMessage = "Keine Id angegeben!";
                }
            }
            else{
                try{
                    addNewAppointment($db, $title, $description, $start, $end, $date, $time);
                    $successMessage = "Erfolgreich hinzugefügt!";
                }
                catch(Exception $e){
                    $errorMessage = "Fehler beim Hinzufügen!";
                }
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
            deleteExistingAppointment($db, $idToDelete);
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
$eventsForThisSite = getAppointmentsForMonth($db, $today);
$noEvents = sizeof($eventsForThisSite) == 0;
?>

<div class="row" >
    <div class="col-md-1"></div>
    <div class="col-md-10" id="head">

        <div class="col-md-1">
            <ul class="pager">
                <li class="previous">
                    <a class="transparent" href= <?php echo "admin.php?action=list_appointments&month=" . $previousM . "&year=" . $previousY ?> >
                        <div class="pagerbuttons"> 
                            <img src="resources/leftarrow.svg" style="height:40px;"/> 
                        </div>
                    </a>
                </li>		
            </ul>
        </div>

        <div class="col-md-10">
            <h1 class="MonthPanel"><?php echo date('M', $today)." ".$year?></h1>
        </div>

        <div class="col-md-1 ">
            <ul class="pager" >
                <li class="next">
                    <a href=<?php echo "admin.php?action=list_appointments&month=" . $nextM . "&year=" . $nextY ?>>
                        <div class="pagerbuttons">
                            <img src="resources/rightarrow.svg" style="height:40px;"/>
                        </div>
                    </a>
                </li>
            </ul>
        </div>

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
        <div class="agenda">
            <div class="table-responsive card">
                <table class="table table-condensed table-hover">
                    <?php 	
                        if($noEvents)
                        {
                            echo '<div class="no-entries"><p><span class="glyphicon glyphicon-bell"></span></p><p>Keine eingetragenen Termine</p></div>';
                        }
                        else{
                            echo '<thead>
                                    <tr> 
                                        <th>Datum</th>
                                        <th>Uhrzeit</th>
                                        <th>Beschreibung</th>
                                        <th>Ende</th>
                                        <th>Optionen</th>
                                    </tr>
                                </thead>';
                        }
                    ?>
                    <tbody>
                    <?php
                        foreach ($eventsForThisSite as $a) {
                            include ("appointment_row.php");
                        }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
