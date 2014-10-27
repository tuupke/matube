<?php

header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Allow-Origin: *");

if(!isset($_GET['ext'])){
	exit;
}

$name = isset($_GET['file'])?$_GET['file']:str_replace(array(" ","."), array("",""), "".microtime()).".".$_GET['ext'];
$uploadFolder = "files";

file_put_contents("$uploadFolder/$name", file_get_contents("php://input"),FILE_APPEND);

echo $name;

?>
