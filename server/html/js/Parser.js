function WeatherDataParser(ID)
{
	this.HTMLString;
	
	this.createWeatherSurface = function(WeatherDataArray)
	{
		var style = "item active";
		var CarouselHTML = "";

		if(WeatherDataArray.cnt != undefined){
			for (i = 0; i < WeatherDataArray.cnt; i++) {
				var WeatherIcon = getIcon(WeatherDataArray.list[i].weather[0].id);
	
				var timezone = tzlookup(WeatherDataArray.list[i].coord.lat, WeatherDataArray.list[i].coord.lon)
	
				var time = moment.tz(timezone).format("HH:mm z");
	
				CarouselHTML += `
				<div class="` + style + `">
					<div>
						<i class="` + WeatherIcon + `">  ` + Math.round(WeatherDataArray.list[i].main.temp) + `&degC<p></p>
							<div class="city">` + WeatherDataArray.list[i].name + `</div>
							<div class="city_time" id="` + timezone + `">` + time + `</div>
						</i>
					</div>
				</div>`;
	
				style = "item";
			}
		}
		else{
			for(var cityName in WeatherDataArray){
				cityWeather = WeatherDataArray[cityName];
	
				var WeatherIcon = getIcon(cityWeather.icon);
	
				var timezone = tzlookup(cityWeather.latitude, cityWeather.longitude)
	
				var time = moment.tz(timezone).format("HH:mm z");
	
				CarouselHTML += `
				<div class="` + style + `">
					<div>
						<i class="` + WeatherIcon + `">  ` + Math.round(cityWeather.temperature) + `&degC<p></p>
							<div class="city">` + cityName + `</div>
							<div class="city_time" id="` + timezone + `">` + time + `</div>
						</i>
					</div>
				</div>`;
	
				style = "item";
			}
		}

		document.getElementById(ID).innerHTML = CarouselHTML;
	}
}

function getIcon(IconID)
{
	if(IconID >= 200 && IconID <=232)
	{
		return "wi wi-thunderstorm";
	}
	else if(IconID >= 300 && IconID <=321)
	{
		return "wi wi-sprinkle";
	}
	else if(IconID >= 500 && IconID <=531)
	{
		return "wi wi-showers";
	}
	else if(IconID >= 600 && IconID <=622)
	{
		return "wi wi-snow";
	}
	else if(IconID >= 701 && IconID <=761)
	{
		return "wi wi-fog";
	}
	else if(IconID == 800)
	{
		return "wi wi-day-sunny";
	}
	else if(IconID >= 801 && IconID <=804)
	{
		return "wi wi-day-cloudy";
	}
	else if(IconID >= 900 && IconID <=961)
	{
		return "wi wi-windy";
	}
	else{
		return "wi wi-cloudy";
	}
}
