var appointmentSource = undefined;
var tickerSource = undefined;
var overriddenDate = undefined;

/**
 * Initializes the main view. This function has to be called after the page loaded
 */
function initMainView(){
    // Update the time and date once. This will start the update interval
    updateDateTime();
    var id = setInterval(updateDateTime, 5000);
    
    // Prepare the ticker animation
    $("#ticker-list").webTicker({duplicate:true, speed: 55, direction: 'left', startEmpty:false, hoverpause:false});
    $("#ticker-list").webTicker('stop');
    
    updateWeather(weatherServer, weatherPort);

    // Hook the carousel events
    DATAProvider.init("pictures");
    $('#pictures').on('slid.bs.carousel', function () {
        DATAProvider.getCurrentExifData();
        replacePictures();
    });
    
    $('body').on('locationReceived', function () {
        if(MAP.getMap() == null)
        {
            MAP.initMap();
        }
        else
        {
            MAP.moveMarker();
        }
    });
    
    $('body').on('locationDescriptionReceived', function () {
        MAP.addStringInfos('artist');
    });


    // Load the data
    loadContent(moment().format("DD-MM-YYYY"));
}

function browserSize() {
    var myWidth = 0, myHeight = 0;
    if (typeof (window.innerWidth) == 'number') {
        //Non-IE
        myWidth = window.innerWidth;
        myHeight = window.innerHeight;
    } else if (document.documentElement && (document.documentElement.clientWidth ||   document.documentElement.clientHeight)) {
        //IE 6+ in 'standards compliant mode'
        myWidth = document.documentElement.clientWidth;
        myHeight = document.documentElement.clientHeight;
    } else if (document.body && (document.body.clientWidth || document.body.clientHeight)) {
        //IE 4 compatible
        myWidth = document.body.clientWidth;
        myHeight = document.body.clientHeight;
    }
    return "&w=" + myWidth + "&h=" + myHeight;
}

/**
 * Replaces the source of a hidden image in the image carousel
 */
function replacePictures(){
    // Get all pictures of the background
    var picture_containers = $('#pictures-container').children();
    var element_to_change = undefined;

    // Search for the active image and get the id of the previous image
    for (let i = 0; i < picture_containers.length; i++) {
        const element = picture_containers[i];
        
        if(element.attributes["class"].value.includes("active")){
            element_to_change = picture_containers[((i - 1) + picture_containers.length) % picture_containers.length].id;
        }
    }

    if(element_to_change != undefined){

        // Request a new image url
        var request = new XMLHttpRequest();
        request.onreadystatechange = function() {
            if ((request.readyState == 4) && request.status == 200)
            {
                // Change the image source
                data = JSON.parse(request.responseText);
                $('#' + element_to_change).children("img")[0].src = "size.php?p=../" + data[0]+ browserSize();
            }
        };
        var url = "api/pictures.php";
        request.open("GET", url, true);
        request.send();
    }
}

/**
 * Updates the date and time elements of the page
 */
function updateDateTime(){
    if(overriddenDate == undefined){
        today = moment();
    }
    else{
        today = overriddenDate;
    }

	document.getElementById('date').innerHTML = today.format("DD.MM.YY");
    document.getElementById('time').innerHTML = moment().format("HH:mm");

    // Update the time of the different timezones
    var elements = document.getElementsByClassName("city_time");
    for(var i=0; i<elements.length; i++) {
        var name = elements[i].id;
        elements[i].innerHTML = moment.tz(name).format("HH:mm z");
    }
}

/**
 * Does the weather update
 */
function updateWeather(server, port){
    WeatherHTML = new WeatherDataParser("weather");
    WeatherAPIRequest = new WeatherData(WeatherHTML.createWeatherSurface,
    server, port);

    WeatherAPIRequest.RequestWeatherData();
    setInterval(WeatherAPIRequest.RequestWeatherData, 60000 * 15);
}

/**
 * Does the setup of the server connection
 */
function loadContent(date){
    if(appointmentSource != undefined){
        appointmentSource.close();
    }
    if(tickerSource != undefined){
        tickerSource.close();
    }


    var eventJson = "";
    appointmentSource = new EventSource("api/appointment_feed.php?relevant=true&date=" + date);
    appointmentSource.onmessage = function(event) {
        var data = event.data;
        if(eventJson != data){
            applyAppointment(JSON.parse(data));
            eventJson = data;
        }
    };
    

    var tickerJson = "";
    tickerSource = new EventSource("api/ticker_feed.php?relevant=true&date=" + date);
    tickerSource.onmessage = function(event) {
        var data = event.data;
        if(tickerJson != data){
            applyTicker(JSON.parse(data));
            tickerJson = data;
        }
    };


    var version = undefined;
    var versionSource = new EventSource("api/version_feed.php");
    versionSource.onmessage = function(event) {
        var data = event.data;

        if(version != data){
            if(version == undefined){
                version = data;
            }
            else{
                location.reload();
            }
        }
    };
}

/**
 * Displays the appointments given in the parameter
 */
function applyAppointment(json){
    var count = json.length;
    var pages = count / 6;

    var appointmentsTable = "";
    var appointmentsIndicators = "";
    var style = "item active"

    // Loop through the appointments and build the appointments table
    for (let i = 0; i < pages; i++) {
        appointmentsTable += '<div class="'+style+'" style="height:100%; width:100%;"><div class="table-responsive"><table class="table table-borderless" id="termintab"><tbody>';
        style = "item";
        for (let j = 0; j < Math.min(json.length - i *6, 6); j++) {
            const element = json[j + i * 6];
            
            moment.locale('de');
            var date = moment(element["date"], "YYYY-MM-DD");

            appointmentsTable += '<tr>';
            appointmentsTable += '<td class="col-md-1 appointment_table_entry_bold">'+date.format("dd")+'</td>';
            appointmentsTable += '<td class="col-md-2 appointment_table_entry">'+date.format("DD.MM.YYYY")+'</td>';
            appointmentsTable += '<td class="col-md-1 appointment_table_entry" id="uhrzeit">'+element["time"]+'</td>';
            appointmentsTable += '<td class="col-md-6 appointment_table_entry">'+element["title"]+'</td>';
            appointmentsTable += '<td class="col-md-2 appointment_table_entry">'+element["location"]+'</td>';
            appointmentsTable += '</tr>';
        }

        appointmentsTable += '</tbody></table></div></div>';
    }

    if(count == 0){
        appointmentsTable += '<div class="'+style+'" style="height:100%; width:100%;"><div class="table-responsive"><table class="table table-borderless" id="termintab"><tbody>';
        appointmentsTable += '<tr></tr>';
        appointmentsTable += '<tr></tr>';
        appointmentsTable += '</tbody></table></div></div>';
    }
    
    // Build the indicators
    if(pages > 1){
        appointmentsIndicators += '<li data-target="appointments" class="active"></li>';
        for (let i = 1; i < pages; i++) {
            appointmentsIndicators += '<li data-target="appointments"></li>';
        }
    }

    // Hide or show the appointments table
    if(count == 0){
        $("#appointments").fadeOut();
    }
    else{
        $("#appointments").fadeIn();
    }

    // Apply the new layout
    $("#appointment-carousel").html(appointmentsTable);
    $("#appointment-indicators").html(appointmentsIndicators);
}

/** 
 * Displays the ticker given in the parameter
 */
function applyTicker(json){
    var count = json.length;

    var tickers = "";

    // Loop through the tickers and build the table
    for (let j = 0; j < json.length; j++) {
        tickers += '<li class="ticker_element">+++&nbsp;&nbsp;&nbsp;&nbsp;'+json[j].message+'</li>';
    }
    
    // Hide or show the appointments table
    if(count == 0){
        $("#ticker").fadeOut("slow", function() {
            $("#ticker-list").webTicker('update', '<li class="ticker_element"> </li>', false, true);
            $("#ticker-list").webTicker('stop');
        });
    }
    else{
        $("#ticker").fadeIn();
        $("#ticker-list").webTicker('cont');
        $("#ticker-list").webTicker('update', tickers, false, true);
    }
}

/**
 * Applies the date selected in the date pick dialog
 */
function applyDateFromDialog(){
    var date = $('#datetimepickerStartParam').data('DateTimePicker').date();

    loadContent(moment(date).format("DD-MM-YYYY"))
    overriddenDate = moment(date);
    updateDateTime();
}
