#!/usr/bin/php
<?php

$lines = `ps aux | grep refreshDO.py`;

if(strpos(file_get_contents("/root/html/serverBackend/servers.txt"),"null")!==false && strpos($lines,"python")==false){
	`cd /root/html/serverBackend/; python /root/html/serverBackend/refreshDO.py`;
}

?>
