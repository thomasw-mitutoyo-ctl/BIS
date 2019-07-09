function WeatherData(callback, server, port)
{
	var xmlhttp = new XMLHttpRequest();
	
	this.WeatherDataArray = null;
	
	xmlhttp.onreadystatechange = function() {
		if ((xmlhttp.readyState == 4) && xmlhttp.status == 200)
		{
			WeatherDataArray = JSON.parse(xmlhttp.responseText);
			callback(WeatherDataArray);
		}
	};
	
	this.RequestWeatherData = function() {
		var url = "http://" + server + ":" + port;
		xmlhttp.open("GET", url, true);
		xmlhttp.send();
	}
}
