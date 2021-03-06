<?php

set_time_limit(0);

function __autoload($cn) {
    include "../system/$cn.php";
}

$db = new Database();

$user = new User();
$entity = $user->getEntity();
if(!$entity->isAdmin()){
    exit;
}

$action = isset($_POST['action'])?$_POST['action']:"status";
$droplet = isset($_POST['id'])?$_POST['id']:"";
$size = isset($_POST['size'])?$_POST['size']:"";

switch($action){
    case 'new':
        if(!array_key_exists($size, $sizes)){
            http_response_code(500);
            echo  'Unknown size: '+$size;
            return;
        }
        $si = $sizes[$size];
        echo `python modifyCloud.py add 1 2>&1`;
    break;

    case 'delete':
        if(strpos(file_get_contents("servers.txt"), $droplet)===false){
            http_response_code(500);
            echo  'Unknown server';
            return;
        }
        echo `python modifyCloud.py remove $droplet 2>&1`;
    break;

    case 'status':
        echo `python status.py 2>&1`;
    break;

}

?>