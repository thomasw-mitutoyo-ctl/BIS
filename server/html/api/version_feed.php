<?php
/*

Provides the running version

*/

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');


$d["version"] = shell_exec("git rev-parse HEAD");

echo "data: ".json_encode($d)."\n\n";
ob_flush();
flush();

?>
