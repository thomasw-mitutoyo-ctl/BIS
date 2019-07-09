<div class="row">
    <div class="col-md-1"></div>
    <div class="col-md-10" id="head">
        <h1 class="MonthPanel">Bilder hochladen</h1>
    </div>
</div>

<div class="row" >
    <div class="col-md-1"></div>
    <div class="col-md-10 nopad">
        <?php include "error_success.php"; ?>
    </div>
</div>

<div class="row">
    <div class="col-md-1"></div>
    <div class="col-md-10 nopad">
        
    <form action="admin.php?action=handle_upload_picture" method="post" enctype="multipart/form-data">
            <h2>Bilder hochladen</h2>
            <div class="form-group">
                <label for="image_files[]">Bilder auswählen</label>
                <input type="file" name="upload[]" multiple="multiple">
            </div>

            <h2>Metadaten</h2>

            <div class="form-group">
                <div class="row">

                    <label class="col-sm-2 col-form-label" for="category">Kategorie</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="category">
                    </div>


                    <label class="col-sm-2 col-form-label" for="name">Fotograf</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="photographer">
                    </div>

                    <label class="col-sm-2 col-form-label">Längengrad</label>
                    <div class="col-sm-10">
                        <input class="form-control" id="location_lat" autocomplete="off" type="text" name="location_lat">
                    </div>

                    <label class="col-sm-2 col-form-label">Breitengrad</label>
                    <div class="col-sm-10">
                        <input class="form-control" id="location_lon" autocomplete="off" type="text" name="location_lon">
                    </div>

                    <p class="col-sm-2 col-form-label"></p>
                    <div class="col-sm-10">
                        <div id="location-picker" style="width: 800px; height: 600px;"></div>
                    </div>


                    <p class="col-sm-2 col-form-label"></p>
                    <div class="col-sm-10">
                        <input type="checkbox" class="form-check-input" id="overwrite_location" name="overwrite_location">
                        <label class="form-check-label" for="overwrite_location">Vorhandene Standortdaten überschreiben</label>
                    </div>
                    
                    <p class="col-sm-2 col-form-label"></p>
                    <div class="col-sm-10">
                    <small id="emailHelp" class="form-text text-muted">Ist diese Option gesetzt, werden bereits vorhandene Standortdaten überschrieben</small>
                    </div>

                    
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Bilder hochladen</button>
        </form>
        
        <script>
            var mymap = L.map('location-picker').setView([0, 0], 3);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 18,
                    id: 'mapbox.streets'
            }).addTo(mymap);

            var marker = {};
   
            var customIcon = L.icon({
                iconUrl: 'resources/marker.svg',
                iconSize:     [40, 40],
                iconAnchor:   [20, 38],
            });

            function onMapClick(e) {
                mymap.removeLayer(marker);
                marker = L.marker(e.latlng, {icon:customIcon}).addTo(mymap);

                $('#location_lat').val(e.latlng.lat)
                $('#location_lon').val(e.latlng.lng)
            }

            mymap.on('click', onMapClick);
        </script>
        
    </div>
</div>

<div class="spacer"></div>
