<?php
/**
 * Possible GET parameters:
 *  - command=[edit]
 *  - id
 * 
 */

$m["id"] = 0;
$m["start"] =date("Y-m-d", time());
$m["end"] = date("Y-m-d", time());

?>
<div class="row" >
    <div class="col-md-1"></div>
    <div class="col-md-10" id="head">
        <h1 class="MonthPanel">Neuen Tickereintrag erstellen</h1>
    </div>
</div>

<div class="row">		
    <div class="col-md-1"></div>
    <div class="col-md-10 nopad">
        <div class="container-fluid formular card">	
        <form action="admin.php?action=list_messages&command=add" name="AppointmentFormular" method="post" onsubmit="return validateMessage();">
            <div class="row">
                <div class="col-sm-1"></div>
                <div class="col-sm-10 form-group">
                    <p class="top nopadleft">Anzeigetext:</p>
                    <textarea class="form-control" rows="5" id="inhalt" name="message" placeholder="Bitte hier die Tickernachricht eingeben ..."></textarea>
                </div>
                <div class="col-sm-1"></div>
            </div>
    
            <div class="row">
                <div class="col-sm-1"></div>
                <div class="col-sm-10">
                <div class="col-sm-6 nopad despiteright">
                <p class="nopad">Anzeigen ab:</p>
                    <div class="form-group">
                        <div class='input-group date' id='datetimepickerstart'>
                            <input type='text' class="form-control" id="startdate" name="start"/>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"/>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 nopad despiteleft">
                <p class="nopad">Anzeigen bis:</p>
                    <div class="form-group">
                        <div class='input-group date' id='datetimepickerend'>
                            <input type='text' class="form-control" id="enddate" name="end"/>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                </div>
                </div>
                <div class="col-sm-1"></div>
            </div>
    
            <div class="row">
                <div class="col-sm-1"></div>
                <div class="col-sm-10">
                    <input type="Submit" class="btn btn-warning btn-block" value="Speichern"/>
                </div>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">

var start = moment();
var end = moment();

// Initialize the date pickers
$("#datetimepickerstart").datetimepicker({locale: 'de', format:'DD-MM-YYYY', defaultDate: start});
$("#datetimepickerend").datetimepicker({locale: 'de', format:'DD-MM-YYYY', defaultDate: end});

$("#datetimepickerstart").on("dp.change", function (e) {
    $('#datetimepickerend').data("DateTimePicker").minDate(e.date);
    $('#datetimepickerend').data("DateTimePicker").date(e.date);
});

</script>