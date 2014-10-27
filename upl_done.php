<?php

$video = $_GET['video'];
$ext = $_GET['ext'];
$name = $_GET['name'];

$video = explode(".",$video);
array_pop($video);
$video = implode(".",$video);

ob_end_clean();
header("Connection: close");
ignore_user_abort(true); // just to be safe
ob_start();
echo('');
$size = ob_get_length();
header("Content-Length: $size");
ob_end_flush(); // Strange behaviour, will not work
flush(); // Unless both are called !

set_time_limit(0);

`ffmpeg -i "files/$video.$ext" -b 1500k -vcodec libx264 -g 30 "files/$video.mp4"`;
`ffmpeg -i "files/$video.$ext" -b 1500k -vcodec libtheora -acodec libvorbis -ab 160000 -g 30 "files/$video.ogv"`;


?>
