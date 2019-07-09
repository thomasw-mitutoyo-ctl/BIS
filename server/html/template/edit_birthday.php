<div class="row" >
    <div class="col-md-1"></div>
    <div class="col-md-10" id="head">
        <h1 class="MonthPanel">Geburtstag eintragen</h1>
    </div>
</div>

<div class="row">		
    <div class="col-md-1"></div>
    <div class="col-md-10 nopad">
        <div class="container-fluid formular card">	
        <form action="admin.php?action=list_birthdays&command=add" name="AppointmentFormular" method="post" onsubmit="return validateBirthday();">

            <div class="row">
				<p class="right top">Bitte informieren Sie Ihren Mitarbeiter über den Zweck und Dauer der Speicherung seiner personenbezogenen Daten und holen Sie seine Einwilligung ein.</p>
                <p class="right top">Name:</p>
                <div class="col-sm-2"></div>
                <div class="col-sm-8 form-group">
                    <input type="text" class="form-control"  maxlength="30" rows="5" id="name" name="name" placeholder="Name">
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="row">
                <div class="col-sm-2"></div>
                <div class="col-sm-8">	
                    <div class='col-sm-6 nopad despiteright'> 
                    <BR>
                        <p class='nopad'>Datum:</p> 
                        <div class='input-group date' id='datetimepickerBirthday'>
                            <input type='text' class='form-control' id='startdate' name='date'/> 
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
        </div>
    </div>
</div>


<script type="text/javascript">

var date = moment();
$("#datetimepickerBirthday").datetimepicker({locale: 'de', format:'DD-MM-YYYY', defaultDate: date});

function validateBirthday(){
    var name = $("#name").val();
    
    if(name == undefined || name.trim() == ""){
        alert("Es wurde kein Name eingegeben");
        return false;
    }

    return true;
}
</script>