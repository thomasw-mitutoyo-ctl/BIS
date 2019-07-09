function initDatetimepickerDMY(id, day, month, year)
{
	$(function () {
		$(id).datetimepicker({locale: 'de', format:'DD-MM-YYYY', defaultDate: year + "-" + month + "-" + day});
	});	
}

function initDatetimepickerDMYH(id, day, month, year, hour)
{
	$(function () {
		$(id).datetimepicker({locale: 'de', format:'DD-MM-YYYY HH:mm', defaultDate: year + "/" + month + "/" + day + " " + hour + ":00"});
	});	
}