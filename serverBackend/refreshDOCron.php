<?php

$lines;

exec('ps aux | grep refreshDO.py', $lines);

$debug = time()." ";

if(strpos("none",file_get_contents("servers.txt")) && strpos("python",$lines)!==false){
	$debug .= "Refreshing";
	`python refreshDO.py`;
}

file_put_contents("debug", $debug."\n", FILE_APPEND);

?>
