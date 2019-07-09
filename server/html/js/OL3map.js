/* 
  This needs a Bootstrap Carousel to be implemented in your html-file
*/

var DATAProvider = {};
var MAP = {};
var _carouselID = null;
var currentCoordinates = new Array(2);
var artist = "";
var countryname = "";
var lastLocation = undefined;

function getLocationDetails(latitude, longitude, callback)
{
	var request = new XMLHttpRequest();
	request.onreadystatechange = function() {
		if ((request.readyState == 4) && request.status == 200)
		{
			var ret = "";

			data = JSON.parse(request.responseText);
			if (data.address != null)
			{
				if (data.address.town != null)
					ret = data.address.town + ", ";
				else if (data.address.city != null)
					ret = data.address.city + ", ";
				else if (data.address.state != null)
					ret = data.address.state + ", ";
				ret = ret + data.address.country;
			}

			callback(ret);
		}
	};

	var url = "https://nominatim.openstreetmap.org/reverse?format=json&&lat=" +latitude + "&lon=" +  longitude + "&zoom=20&addressdetails=1&extratags=1";
	request.open("GET", url, true);
	request.send();
}


(function() {
	this.init = function(carouselID){
		_carouselID = carouselID;
		this.getCurrentExifData(true);
	};
			
	var getLonLatOfPic = function(){
		var lat = EXIF.getTag(this, "GPSLatitude");
		var lon = EXIF.getTag(this, "GPSLongitude");	
		var refLat = EXIF.getTag(this, "GPSLatitudeRef");
		var refLon = EXIF.getTag(this, "GPSLongitudeRef");
		var _artist = EXIF.getTag(this, "Artist");

		if (_artist != undefined)
		{
			artist = bin2String(_artist);
		}
		else
		{
			artist = "";
		}
		
		currentCoordinates = formatCoordinates(lon,lat,refLon,refLat);

		callback = function(locationname){
			countryname = locationname;

			var dataReceivedEvent = jQuery.Event('locationDescriptionReceived');
			jQuery('body').trigger(dataReceivedEvent);
		}

		if(currentCoordinates != null){
			getLocationDetails(currentCoordinates[1], currentCoordinates[0], callback);
		}
		else{
			$(".map").fadeOut();
		}

		var dataReceivedEvent = jQuery.Event('locationReceived');
		jQuery('body').trigger(dataReceivedEvent);

		var dataReceivedEvent = jQuery.Event('locationDescriptionReceived');
		jQuery('body').trigger(dataReceivedEvent);
	}	
	
	function bin2String(array) {
		var result = "";
		for (var i = 0; i < array.length; i++) {
			var number = array[i];
			if(number != 0){
				result += String.fromCharCode(parseInt(number, 10));
			}
		}
		if(result.substring(0,7).localeCompare("UNICODE") == 0)
		{
			result = result.substr(8,result.length);
		}
		
		
		return result;
	}
	function formatCoordinates(lon,lat,refLon,refLat)
	{
		var coords = new Array(2);
		if (typeof(lon) == "undefined" || typeof(lat) == "undefined")
		{
			return null;
		}
		else
		{
			var sign = (refLon == "W") ? -1 : 1;
			coords[0] = sign * (lon[0] + lon[1]/60 + lon[2]/3600);
			
			sign = (refLat == "S") ? -1 : 1;
			coords[1] = sign * (lat[0] + lat[1]/60 + lat[2]/3600);
		}
		return coords;
	}
	
	this.getCurrentExifData = function()
	{		
		var currentID = $("#" + _carouselID + " .carousel-inner > .item.active img").attr('id');
		if(currentID != undefined){
			var img = document.getElementById(currentID);

			// Clear the cached exif data
			img.exifdata = undefined;
			
			EXIF.getData(img, getLonLatOfPic);
		}
	}
}).apply(DATAProvider);

(function(){
	var map = null;
	var center = currentCoordinates;
	var vectorSource = new ol.source.Vector();
	var startedSliding = false;
	
	var markerStyle = new ol.style.Style({
		image: new ol.style.Icon(({
			anchor:[0.5,1],
			src: '../resources/marker.svg'
		}))
	});
			
	var vectorLayer = null;
	
	this.initMap = function()
	{	
		center = ol.proj.fromLonLat(currentCoordinates);
		
		var markerOnLocation = new ol.Feature({
			type: 'icon',
			geometry: new ol.geom.Point(center)
		});
		
		markerOnLocation.setId("marker");
	
		vectorSource.addFeature(markerOnLocation);		
		
		vectorLayer = new ol.layer.Vector({
			source: vectorSource,
			style: function(feature) {
				if(startedSliding && (feature.get('type') === 'icon'))
				{
					return null;
				}
				return markerStyle;
			},
			type: 'firstMarker'
		});  
		map = new ol.Map({
			layers: [ new ol.layer.Tile({ opacity:0.8, source: new ol.source.OSM()}), vectorLayer],
			target: 'map',
			view: new ol.View({
				center: center,
				zoom: 4
			}),		
			loadTilesWhileAnimating: true,
			controls: [ ]
		});
		
		map.once('postcompose', zoomFirstLoc);
	}
	
	var zoomFirstLoc = function(event)
	{
		flyTo(center,function (){});
	}
	
	var callbackSetMarkerStyle = function(el,index,array)
	{
		if(el.get('type') == 'firstMarker')
		{
			el.getSource().getFeatureById("marker").setStyle(null);
		}
	}
	
	var moveFeature = function(event){
		var vectorContext = event.vectorContext;
		var feature = new ol.Feature(new ol.geom.Point(center));
		vectorContext.drawFeature(feature,markerStyle);
		//map.render();
	}
	
	this.moveMarker = function()
	{
		startedSliding = true;
		
		if(currentCoordinates == null)
		{
			console.log("Keine Koordinaten");
			$(".map").fadeOut();
			return;
		}
		else
		{
			$(".map").fadeIn();
		}
		center = ol.proj.fromLonLat(currentCoordinates);

		map.getLayers().forEach(callbackSetMarkerStyle);
		map.on('postcompose', moveFeature);
		//map.render();
		//map.getView().setCenter(center);
		//map.getView().setZoom(0.3);
		map.once('postcompose', zoomFirstLoc);
	}
	
	this.getMap = function()
	{
		return map;
	}	
	
	this.addStringInfos = function(paragraphID)
	{
		var docString = "";
		if (artist != "")
		{
			docString += artist;
			if(countryname != "")
			{
				docString += " - " + countryname;
			}
			document.getElementById(paragraphID).innerHTML = docString;
		}
	}
		
    function flyTo(location, done) {
		var view = map.getView();
		var duration = 10000;
        var parts = 2;
		var called = false;

		var distance = 5000;

		if(lastLocation != undefined){
			distance = distanceBetween(currentCoordinates, lastLocation);
		}
		lastLocation = currentCoordinates;
		

		var flyToDuration = (4000 / 10000) * distance;

		var z = 4;

		if(distance < 1000){
			z = 6;
		}

        function callback(complete) {
          --parts;
          if (called) {
            return;
          }
          if (parts === 0 || !complete) {
            called = true;
            done(complete);
          }
		}

		if(flyToDuration < 750){
			flyToDuration = 750;
		}

		view.animate({ // Zoom out
			zoom: z,
			duration: 500
		}, function() { });

        view.animate({ // Fly to the new location
			center: location,
			duration: flyToDuration
		}, function() {
			view.animate({ // Zoom in again
				zoom: 8,
				duration: 3500
			}, callback)
		});
      }
	
	  function distanceBetween(location1, location2){
		  	// https://www.movable-type.co.uk/scripts/latlong.html 
			var R = 6371e3; // meters
			var phi1 = location1[0] * Math.PI / 180;
			var phi2 = location2[0] * Math.PI / 180;
			var dp = (location2[0]-location1[0]) * Math.PI / 180;
			var dl = (location2[1]-location1[1]) * Math.PI / 180;
			
			var a = Math.sin(dp/2) * Math.sin(dp/2) +
					Math.cos(phi1) * Math.cos(phi2) *
					Math.sin(dl/2) * Math.sin(dl/2);
			var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
			
			var d = R * c;

			return d / 1000;
	  }
	  
}).apply(MAP);
