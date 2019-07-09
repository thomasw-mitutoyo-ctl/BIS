<div class="row">
    <div class="col-md-1"></div>
    <div class="col-md-10" id="head">
        <h1 class="MonthPanel">Versionsinformation</h1>
    </div>
</div>

<div class="row">		
    <div class="col-md-1"></div>
    <div class="col-md-10 nopad">
        <div class="agenda">
            <div class="container-fluid card">

                <div class="row spacer">

                    <div class="col-sm-2 text-left">Branch:</div>
                    <code class="col-sm-8"><?php echo shell_exec("git rev-parse --abbrev-ref HEAD"); ?></code>
                    <div class="col-sm-2"></div>
                    
                </div>
                <div class="row spacer">

                    <div class="col-sm-2 text-left">Commit:</div>
                    <code class="col-sm-8"><?php echo shell_exec("git rev-parse HEAD"); ?></code>
                    <div class="col-sm-2"></div>
                    
                </div>
                <div class="row spacer">

                    <div class="col-sm-2 text-left">Datum:</div>
                    <code class="col-sm-8"><?php echo shell_exec("git log -1 --format=%cd --date=local"); ?></code>
                    <div class="col-sm-2"></div>
                    
                </div>
                <div class="row spacer">

                    <div class="col-sm-2 text-left">Datenbank:</div>
                    <code class="col-sm-8">
                        <?php 
                        
                            require_once __DIR__ . '/../php/sql_request.php';
                            require_once __DIR__ . '/../php/database_proxy.php';
                            require_once __DIR__ . '/../php/_global_settings.php';

                            // Load the credentials from the configuration and connect to the database
                            $server = "";
                            $username = "";
                            $password = "";
                            $database = "";
                            getDbConfig($server, $username, $password, $database);

                            echo "Server: ".$server."<br/>";
                            echo "Username: ".$username."<br/>";
                            echo "Database: ".$database."<br/>";

                            $db = new MySQLi($server, $username, $password, $database);

                            if($db->connect_errno){
                                echo "Connection failed: ".$db->connect_error;
                            }
                            else{
                                echo "Connected successfully";
                            }
                        ?>
                    </code>
                    <div class="col-sm-2"></div>
                    
                </div>

                <div class="spacer"></div>
            </div>
        </div>
    </div>
</div>
