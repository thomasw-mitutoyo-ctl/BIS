
/**
 * Reads the properties from the image and calls the callback function
 * 
 * callback(coordinates, artist, width, height)
 */
function loadImageProperties(id, callback){
    // Listen for the onload event of every image using the id
    $(id).each(function() {
        var img = new Image();
        img.onload = function() {
            var image = $(id)[0];
            
            // Get the exif data
            EXIF.getData(image, function() {

                // Read the data and call the callback
                var lat = EXIF.getTag(this, "GPSLatitude");
                var lon = EXIF.getTag(this, "GPSLongitude");	
                var refLat = EXIF.getTag(this, "GPSLatitudeRef");
                var refLon = EXIF.getTag(this, "GPSLongitudeRef");
                var _artist = EXIF.getTag(this, "Artist");
                var width = image.naturalWidth;
                var height = image.naturalHeight
                
                var coordinates = formatCoordinates(lon,lat,refLon,refLat);
                var artist = "";

                if (_artist != undefined)
                {
                    artist = bin2String(_artist);
                }
                
                callback(coordinates, artist, width, height)
            });
        };
        img.src = $(this).attr('src');
    });
}

/**
 * Formats the raw exif coordinates into latitude and longitude
 */
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

/**
 * Converts an array of binary values into a string
 */
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