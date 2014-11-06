<?php

$video = $_POST['video'];
$ext = $_POST['ext'];
$name = $_POST['name'];

function __autoload($cn) {
    include "system/$cn.php";
}

$db = new Database();

$user = new User();

if(!$user->isLoggedIn()){
	exit;
}

$description = $_POST['description'];
$hName = $_POST['videoName'];
$public = $_POST['visibility'];

$db->nquery("insert into video (status, name, storage, description, ownedBy, public) values (?,?,?,?,?,?)", 
	array(0,$hName, $video, $description, $user->getId(), $public));

$email = $user->getMail();
`python ./serverBackend/frontendNotifier.py $video $email`;


?>