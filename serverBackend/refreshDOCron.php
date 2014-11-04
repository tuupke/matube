<?php

implode("",exec(`ps aux | grep refreshDO.py`,$lines));


if(strpos("none",file_get_contents("servers.txt")) && strpos("python",$lines)!==false){
	`python refreshDO.py`;
}


?>