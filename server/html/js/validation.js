function validateEvent()
{
	var confirmstring = "";
	
	// check wether no fields empty:
	if (document.getElementById('titel').value == "")
	{
		alert('Es wurde kein Titel eingeben');
		return false;
	}
	if (document.getElementById('place').value == "")
	{
		alert('Es wurde kein-/e Beschreibung/Ort angegeben');
		return false;
	}
	if (document.getElementById('Date').value == "")
	{
		alert('Bitte geben Sie das Datum des Termins an');
		return false;
	}
	if (document.getElementById('startdate').value == "")
	{
		alert('Bitte Anzeigestart festlegen');
		return false;
	}
	try {
		if (document.getElementById('enddate').value == "")
		{
			alert('Bitte Anzeigeende festlegen');
			return false;
		}
	} 
	catch (e) {
		
	}
	
	// check startdate and event
	
	var startdate = document.getElementById('startdate').value;
	var splittStartdate = startdate.split('-');
	startdate = splittStartdate[2] + '-' + splittStartdate[1] + '-' + splittStartdate[0];
	startdate =  new Date(startdate);	
	var events = document.getElementById('Date').value;
	var dayAndMonth = events.split('-');
	var yearAndTime = dayAndMonth[2].split(' ');
	var HoursAndMinutes = yearAndTime[1].split(':');
	events = new Date(yearAndTime[0],dayAndMonth[1]-1,dayAndMonth[0],HoursAndMinutes[0],HoursAndMinutes[1]);
					
	if(startdate == 'Invalid Date')
	{
		alert('Kein gültiges Datum');
		return false;
	}
	if (startdate.getTime() > events.getTime()) 
	{
		alert('Anzeigestart darf nicht nach Beginn des Termins sein.');
		return false;
	}
	
	// check enddate
	var enddate = document.getElementById('enddate').value;			
	var splitStartdate = enddate.split('-');
	enddate = splitStartdate[2] + '-' + splitStartdate[1] + '-' + splitStartdate[0];
	enddate =  new Date(enddate);	
	
	if (termin.getTime() > enddate.getTime()) 
	{
		alert('Terminende darf nicht vor Beginn des Termins sein.');
		return false;
	}
}

function validateMessage()
{			
	var startdate = document.getElementById('startdate').value;
	var enddate = document.getElementById('enddate').value;
	var inhalt = document.getElementById('inhalt').value;
	
	if (inhalt == "")
	{
		alert('Es wurde kein Inhalt eingeben');
		return false;
	}
	if (startdate == "")
	{
		alert('Bitte Anzeigestart festlegen');
		return false;
	}
	if (enddate == "")
	{
		alert('Bitte Anzeigeende festlegen');
		return false;
	}	
	
	var splitStartdate = startdate.split('-');
	startdate = splitStartdate[2] + '-' + splitStartdate[1] + '-' + splitStartdate[0];
	startdate =  new Date(startdate);	
	var splitEnddate = enddate.split('-');
	enddate = splitEnddate[2] + '-' + splitEnddate[1] + '-' + splitEnddate[0];
	enddate = new Date(enddate);
	
	if (startdate.getTime() > enddate.getTime()) 
	{
		alert('Anzeigestart muss vor dem Anzeigeende sein.');
		return false;
	}
	if (inhalt.length > 200)
	{
		alert('Maximal Zeichenlänge der Beschreibung: 200 \n Jetzige Länge: ' + document.getElementById('inhalt').value.length);
		return false;
	}
}
