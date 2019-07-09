<!DOCTYPE html>
<html>
	<head>
	<title><?php
		require_once __DIR__ . "/php/_global_settings.php";
		echo $config["Title"];
	?></title>
	
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/weather-icons.css">
    <link rel="stylesheet" href="css/weather-icons-wind.css">
    <link rel="stylesheet" href="css/main_view_styles.css">

		
	<link rel="shortcut icon" href="favicon.ico" type="image/png" />
	<link rel="icon" href="favicon.ico" type="image/png" />


	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="css/bootstrap-datetimepicker.css">
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.js"></script>
	
	<!-- Scripts for weather information -->
    <script src="js/WeatherData.js"></script>
	<script src="js/Parser.js"></script>
	<script src="js/jquery.webticker.js"></script>
	
	<!-- Script um die Zeitzone der verschiedenen Standorte herauszufinden -->
	<script src="js/tz.js"></script>

	<script src="js/moment-with-locales.js"></script>
	<script src="js/moment-timezone-with-data.js"></script>
    <script src="js/bootstrap-datetimepicker.js"></script>
    
	<script src="js/main_view.js"></script>
	
	<link rel="stylesheet" href="css/ol.css" type="text/css">
    <script src="js/ol.js"></script>
    
    <script type="text/javascript" src="js/exif.js"></script>
    <script type="text/javascript" src="js/OL3map.js"></script>
    </head>
    
    <?php
        require_once __DIR__ ."/php/main_view_scripts.php";
    ?>

	<script>
		<?php

		// The script to show the dialog if needed 
		if(isset($_GET["modus"]) && $_GET["modus"] == "vorschau")
		{
			echo "$(window).load(function(){";
			echo "	$('#previewDateDialog').modal('show');";
			echo "});\n";
		}

		$server = "";
		$port = "";
		getWeatherDaemonConfig($server, $port);
		echo "var weatherServer = \"".$server . "\";";
		echo "var weatherPort = \"".$port."\";";
		?>
		</script>
	
	<body onload="initMainView()">

		<!-- The modal dialog to select the preview date -->
		<div id="previewDateDialog" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
			<div class="modal-dialog">
				<form action="main_view.php" name="StartParam" method="post">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">×</button>
							<h4 class="modal-title">Vorschau für ein bestimmtes Datum?</h4>
						</div>
						<div class="modal-body">
							<div class="input-group date" id="datetimepickerStartParam">
								<input class="form-control" id="Date" name="paramDate" type="text">
								<span class="input-group-addon">
									<span class="glyphicon glyphicon-calendar"></span>
								</span>
							</div>
							<script>
								var d = new Date();
								var month = d.getMonth() + 1;
								var day = d.getDate();
								var year = d.getFullYear();

								moment.createFromInputFallback = function(config) { config._d = new Date(config._i); };
								
								$('#datetimepickerStartParam').datetimepicker(
									{
										defaultDate : year + " " + month + " " + day,
										locale: 'de',
										format: 'DD-MM-YYYY'
									});

							</script>
						</div>
						<div class="modal-footer">
							<button type="button" onClick="applyDateFromDialog()" class="btn btn-primary" 
									data-dismiss="modal" data-toggle="tooltip" 
									data-placement="right" title="">Anwenden</button>
							<button type="button" class="btn btn-primary" data-dismiss="modal" data-toggle="tooltip" data-placement="right" title="">Heute</button>
						</div>
					</div>
				</form>
			</div>
		</div>

		<!-- The background of the site -->
        <div id="pictures" class="carousel slide pictures" data-ride="carousel">
			<div id="pictures-container" class="carousel-inner" role="listbox">
                <?php
                    generateBackgroundItems();
                ?>
			</div>
		</div>

		<!-- The sites header -->
		<div class="head">
			<div class="head left_head">

				<div class="carousel slide head left_head left_head_inner" data-ride="carousel" data-interval="7500">
					<div id="weather" class="carousel-inner left_content " role="listbox">
						
					</div>
				</div>

			</div>
			<div class="head center_head">

				<div class="head carousel slide center_head center_head_inner" data-ride="carousel" data-interval="5000">
					<div class="carousel-inner head center_head_content" role="listbox">
						<?php
							for ($i=0; $i < count($config["WelcomeTitles"]); $i++) {
								echo "<div class=\"item";
								if ($i == 0) echo " active";
								echo "\">";
								echo $config["WelcomeTitles"][$i];
								echo "</div>";
							}
						?>
					</div>
				</div>

			</div>
			<div class="head right_head">

				<div class="head right_head right_head_inner">
					<h1 id="time" class="right_header_h1"></h1>
					<h2 id="date" class="right_header_h2"></h2>
				</div>

			</div>
		</div>

		<!-- The ticker at the bottom -->
		<div id="ticker" style="display:none">
			<div class="ticker ticker_background"></div>
			<div class="ticker tickercontainer">
				<div class="ticker ticker_mask">
					<ul id="ticker-list" class="ul_ticker">
						<li class="ticker_element"> </li>
					</ul>
				</div>
			</div>
		</div>

		<!-- The list of appointments in the middle -->
		<div id="appointments" class="carousel slide appointments_table" data-ride="carousel" data-interval="7500" style="display:none">
			<ol id="appointment-indicators" class="carousel-indicators"></ol>
			<div id="appointment-carousel" class="carousel-inner" role="listbox"></div>
        </div>

		<div class="noimage">
		    No images available
		</div>

		<!-- The map at the bottom right -->
		<div class="mapcontainer">
			<div id="map" class="map"></div>
			<div class="artist"><p id="artist"></p></div>
		</div>
		
		<script type='text/javascript'>
			$(document).ready(function() {
				$('#pictures').carousel({
					interval: 25000
				})
			});
		</script>
    </div>
	</body>
</html>
