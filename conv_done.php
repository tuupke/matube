<?php

if(!isset($_GET['id'])){
	exit;
}

$id = $_GET['id'];

require_once("system/database.php");

$db = new Database();

$res = $db->query("select * from video where id=?", array($id));

$ignore = array("the", "one", "a");
if(count($res)==1) {
	$db->nquery("update video set status = 1 where id=?",array($id));
	$res = $res[0];

	$name = explode(" ", $res[2]);
	
	foreach($name as $n){
		if(in_array($n, $ignore)){
			continue;
		}
		$db->nquery("insert into tags values (?, ?)", array($id, $n));
	}
}


?>