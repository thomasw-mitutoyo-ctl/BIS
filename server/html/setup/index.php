<!DOCTPYE html>
<html>
	<head>
		<title>Einrichtung</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		
		<!-- Bootstrap CSS -->
		<link rel="stylesheet" href="../css/bootstrap.min.css">
	
		<!-- JQuery und Bootstrap-->
		<script src="../js/jquery.min.js"></script>
		<script src="../js/bootstrap.min.js"></script>
	</head>
	<body>
        <div class="jumbotron">
            <div class="container-fluid nopad">
                <h1>Bildschirminformationssystem</h1>
                <p>Server</p>
            </div>
        </div>

        <div class="container-fluid nopad">

        <?php
            try {
                require_once __DIR__ . '/setup.php';
                handleSetupRequest();
            } catch (Exception $ex) {
                echo $ex;
            } catch (Error $ex) {
                echo $ex;
            }
        ?>

        </div>
    </body>
</html>