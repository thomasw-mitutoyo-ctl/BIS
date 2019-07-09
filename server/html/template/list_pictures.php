<?php
/**
 * Possible GET parameters:
 * 
 * Possbile POST parameters:
 * 
 */
?>

<div class="row">
    <div class="col-md-1"></div>
    <div class="col-md-10" id="head">
        <h1 class="MonthPanel">Alle Bilder</h1>
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
        <div class="featurette" id="about">
            <div class="agenda">
                <div class="card">
                    <div id="loader">
                        <div class="loader col-sm-2"></div>
                    </div>
                    <div class="grid">
                        <ul id="list">
                            
                        </ul>
                    </div>

                    <div id="empty-state" class="empty-state">Keine Bilder vorhanden</div>

                    <hr></hr>
                    
                    <script type="text/javascript" src="js/exif.js"></script>
                    <script type="text/javascript" src="js/image_properties.js"></script>
                    <script>
                        function removeImage(src, listitemid){
                            if(confirm("Wollen Sie das Bild wirklich löschen?")){
                                $.getJSON('api/pictures.php?delete=true&id=' + src, function(data){
                                    if(data.success){
                                        $(listitemid).remove();
                                        $('.simple-list-grid').simpleListGrid();
                                    }
                                    else{
                                        alert("Löschen fehlgeschlagen!");
                                    }
                            });
                            }
                        }
                        function loadImages(){
                            $.getJSON('api/pictures.php?limit=all', function(data) {

                                var i;
                                var html = "";
                                for(i = 0; i < data.length; i++){
                                    var listitemid = "list_item_" + i
                                    html += `
                                    <li id='` + listitemid +`'>
                                            <button type="button" onclick="removeImage('` + data[i] + `', '#` + listitemid + `')" 
                                                    class="btn btn-danger delete-image"><i class="glyphicon glyphicon-remove"></i></button>
                                            <img id="list_image_` + i + `" src="` + data[i] + `"/>
                                            <p id="resolution_` + i + `"></p>
                                            <p id="artist_` + i + `"></p>
                                    </li>`;
                                }

                                document.getElementById("list").innerHTML = html;
                                document.getElementById("loader").style.display = "none";

                                if(data.length == 0){
                                    document.getElementById("empty-state").style.display = "block";
                                }
                                else{
                                    document.getElementById("empty-state").style.display = "none";
                                }

                                for(i = 0; i < data.length; i++){
                                    // Wrap the load image call into a function to preserve i
                                    c = function(){
                                        var ti = i;
                                        loadImageProperties("#list_image_" + i, function(coordinades, artist, width, height) {
                                            applyProperties(ti, coordinades, artist, width, height)
                                        });
                                    }
                                    c();
                                }
                            });
                        }

                        function applyProperties(i, coordinades, artist, width, height){
                            $('#resolution_' + i).text(width + "x" + height);
                            $('#artist_' + i).text(artist);
                        }

                        loadImages();
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="spacer"></div>
