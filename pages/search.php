<?php

if(!isset($_GET['options'])){
	header("location: /$base");
}

$options = urldecode($_GET['options']);

$r = $db->query("select * from video where name like %?%", array($options));

function body(){

	echo $_GET['options'];
}


?>