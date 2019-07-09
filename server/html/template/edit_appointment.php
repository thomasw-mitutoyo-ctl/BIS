<?php
/**
 * Possible GET parameters:
 *  - command=[edit]
 *  - id
 * 
 */

$title = "Neuer Termin";

$a["id"] = 0;
$a["date"] = date("Y-m-d", time());
$a["start"] =date("Y-m-d", time());
$a["end"] = date("Y-m-d", time());
$a["location"] = "";
$a["title"] = "";
$a["time"] = date("H:00", time());

$submitCommand = "add";

if(isset($_GET['command']) && $_GET['command'] == "edit" && isset($_GET["id"])) {
    $title = "Termin bearbeiten";
    $submitCommand = "edit";
    
    // Create the database connection
    require_once ("php/sql_config.php");
    require_once ("php/database_proxy.php");
    $db = connectToDatabase();

    $a = getAppointmentById($db, $_GET["id"]);
}
    
?>

<div class="row">
    <div class="col-md-1"></div>
    <div class="col-md-10" id="head">
        <h1 class="MonthPanel"><?php echo $title ?></h1>
    </div>
</div>

<div class="row">				
    <div class="col-md-1"></div>
    <div class="col-md-10 nopad">
        <div class="container-fluid formular card">
        <form id="appointment-form" action="admin.php?action=list_appointments&command=<?php echo $submitCommand ?>" name="AppointmentFormular" method="post" onsubmit="return validateEvent();">				
            <div class="row ">
                <p class="right top">Titel:</p>
                <div class="col-sm-2"></div>
                <div class="col-sm-8">
                    <div class="form-group">
                        <input type="text" maxlength="30" class="form-control" id="titel" name="title" 
                        data-toggle="tooltip" data-placement="left" title="Zum Beispiel: Allgemeine Besprechung - Max. 30 Zeichen" 
                        placeholder="Um was für einen Termin handelt es sich?" value=<?php echo '"'.$a["title"].'"' ?>>
                    </div>
                </div>
                <div class="col-sm-2"></div>
            </div>
    
            <div class="row">
                <p class="right">Beschreibung / Ort:</p>
                <div class="col-sm-2"></div>
                <div class="col-sm-8 form-group">
                    <input type="text" class="form-control"  maxlength="30" rows="5" id="place" name="description" 
                    placeholder="Wo findet der Termin statt?" data-toggle="tooltip" data-placement="left" 
                    title="Zum Beispiel: Kawasaki, Utsunomiya, Osaka - Max. 30 Zeichen" value=<?php echo '"'.$a["location"].'"' ?>>
                </div>
                <div class="col-sm-2"></div>
            </div>
    
            <div class="row">
                <div class="col-sm-2"></div>
                <div class="col-sm-8">
                    <div class="col-sm-6 nopad despiteright">
                        <p class="nopad">Datum des Termins:</p>
                        <div class='input-group date' id='datetimepickerTermindatum'>
                            <input type='text' class="form-control" id="Date" name="date"/>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" >
                <div class="col-sm-2"></div>
                <div class="col-sm-8">
                    <div class="col-sm-6 nopad despiteright">
                        <br>
                            <p class="nopad">Terminende:</p>
                            <div class='input-group date' id='datetimepickerTerminende'>
                                <input type='text' class="form-control" id="Date" name="end"/>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </br>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-2"></div>
                <div class="col-sm-8">	
                    <div class='col-sm-6 nopad despiteright'> 
                    <BR>
                        <p class='nopad'>Anzeigen ab: </p> 
                        <div class='input-group date' id='datetimepickerAnzeigestart'>
                            <input type='text' class='form-control' id='startdate' name='start'/> 
                            <span class='input-group-addon'>
                                <span class='glyphicon glyphicon-calendar'></span>
                            </span>
                        </div>
                    </div>
                    <div class='col-sm-6 nopad despiteleft'> 
                        <input type="Submit" class="btn btn-warning btn-block alignBottom" value="Speichern"/>
                    </div>
                </div>
            </div>
            
            <input name="id" value="<?php echo $a["id"]?>" type="hidden">
        </form>
        </div>
    </div>
</div>

<script>

var date = moment("<?php echo date("Y-m-d", strtotime($a["date"])).date(" G:i", strtotime($a["time"])); ?>");
var start = moment("<?php echo $a["start"]; ?>");
var end = moment("<?php echo $a["end"]; ?>");

// Initialize the date pickers
$("#datetimepickerTermindatum").datetimepicker({locale: 'de', format:'DD-MM-YYYY HH:mm', defaultDate: date});
$("#datetimepickerAnzeigestart").datetimepicker({locale: 'de', format:'DD-MM-YYYY', defaultDate: start});
$("#datetimepickerTerminende").datetimepicker({locale: 'de', format:'DD-MM-YYYY', defaultDate: end});

$("#datetimepickerTermindatum").on("dp.change", function (e) {
    $('#datetimepickerTerminende').data("DateTimePicker").minDate(e.date);
    $('#datetimepickerAnzeigestart').data("DateTimePicker").maxDate(e.date);
    $('#datetimepickerAnzeigestart').data("DateTimePicker").date(e.date);
    
    $('#appointment-form').attr('action', 'admin.php?action=list_appointments&command=<?php echo $submitCommand ?>&month=' 
            + e.date.format("MM") + '&year=' + e.date.format("YYYY"));
});

</script>