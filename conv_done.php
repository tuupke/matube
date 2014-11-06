<?php

if(!isset($_GET['newName']) || !isset($_GET['oldName']) || !isset($_GET['hash']) || $_GET['hash']!="41dc8c4ced0a3ec02593499f3f58fec306dc58903c054abaff5045ee9f189a96"){
	echo 'aaa';
	exit;
}

$id = $_GET['newName'];
$old = $_GET['oldName'];

require_once("system/Database.php");

$db = new Database();

$res = $db->query("select * from video where storage=?", array($old));

$ignore = array("the", "one", "a");
if(count($res)==1) {
	$db->nquery("update video set status=1, storage=? where storage=?",array($id, $old));
	$res = $res[0];
	$id = $res[0];
	$name = explode(" ", $res[2]);
	
	foreach($name as $n){
		if(in_array($n, $ignore)){
			continue;
		}
		$db->nquery("insert into tags values (?, ?)", array($id, $n));
	}
}

`rm videos/$old`;

?>