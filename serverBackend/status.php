<?php

set_time_limit(0);

$action = isset($_POST['action'])?$_POST['action']:"status";
$droplet = isset($_POST['id'])?$_POST['id']:"";
$size = isset($_POST['size'])?$_POST['size']:"";

$sizes = array("small" => 1, "medium" => 2, "large" => 3);

switch($action){
    case 'new':
        if(!array_key_exists($size, $sizes)){
            http_response_code(500);
            echo  'Unknown size: '+$size;
            return;
        }
        $size = $sizes[$size];
        echo `python modifyCloud.py add $size`;
    break;

    case 'delete':
        if(!preg_match("/[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}/")){
            http_response_code(500);
            echo  'Unknown server';
            return;
        }
        echo `python modifyCloud.py remove $droplet`;
    break;

    case 'status':
        echo `python status.py`;
    break;

}

?>