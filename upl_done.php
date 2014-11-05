<?php

$video = $_GET['video'];
$ext = $_GET['ext'];
$name = $_GET['name'];

function __autoload($cn) {
    include "system/$cn.php";
}

$db = new Database();

$user = new User();

if(!$user->isLoggedIn()){
	exit;
}

$description = "Lorem Ipsum";

$db->nquery("insert into video (status, name, storage, description, ownedBy) values (?,?,?,?,?)", array(0,$name, "", $description, $user->getId()));

$email = $user->getMail();
`python ./serverBackend/frontendNotifier.py $video $email`;
?>
