<!DOCTYPE html>
<html lang="en">
	<head>
		<title><?php
		require_once __DIR__ . "/php/_global_settings.php";
		echo $config["Title"];
		?></title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		
		<link rel="shortcut icon" href="favicon.ico" type="image/png" />
    	<link rel="icon" href="favicon.ico" type="image/png" />


		<!-- Bootstrap CSS -->
		<link rel="stylesheet" href="css/bootstrap-datetimepicker.css">
		<link rel="stylesheet" href="css/bootstrap.min.css">
	
		<!-- Custom CSS -->
		<link rel="stylesheet" href="css/zis.css">
	
		<!-- JQuery und Bootstrap-->
		<script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.min.js"></script>


		<link rel="stylesheet" href="css/leaflet.css"/>
		<script src="js/leaflet.js"></script>

		<script src="js/moment-with-locales.js"></script>
		<script src="js/bootstrap-datetimepicker.js"></script>
        <script src="js/edit_appointment.js"></script>
        <script src="js/validation.js"></script>

		<script>	
			<!-- Initialize tooltip -->
			$(document).ready(function(){
				$('[data-toggle="tooltip"]').tooltip();
			});
		</script>
	</head>
	
	<body>	
        <?php
            date_default_timezone_set('CET');
            
            $action = isset($_GET['action']) ? $_GET['action'] : "";
		?>
		
		<div class="container-fluid nopad">
		
			<!-- navbar on the left side -->
			<div class="col-sm-2 sidenav">
				<ul class="nav nav-pills nav-stacked">
					<?php include("template/menu.html"); ?> 
				</ul>
			</div>
			
			<div class="col-sm-10">
                <div class="row" >


                <?php
                if($action == "new_appointment" || $action == "edit_appointment"){
                    include("template/edit_appointment.php");
                }
                if($action == "list_appointments"){
                    include("template/list_appointments.php");
                }
                if($action == "new_message" || $action == "edit_message"){
                    include("template/edit_ticker.php");
                }
                if($action == "list_messages"){
                    include("template/list_tickers.php");
                }
                if($action == "upload_picture"){
                    include("template/upload_images.php");
				}
				if($action == "handle_upload_picture"){
					include("template/handle_upload_images.php");
				}
				if($action == "new_birthday"){
					include("template/edit_birthday.php");
				}
				if($action == "list_birthdays"){
					include("template/list_birthdays.php");
				}
				if($action == "list_pictures"){
					include("template/list_pictures.php");
				}
				if($action == "about"){
					include("template/about.php");
				}
				else{
					include("template/admin_blank.php");
				}
                ?>
				</div>
			</div>
			<div id="sidenav_bg"></div>
		</div>
	</body>
</html>