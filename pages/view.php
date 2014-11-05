<?php

$id = $_GET['id'];

$r = $db->query("select * from video where id=?", array($id));
function body(){
	global $r, $db;
	
	if(count($r)!=1){
?>
<h1>Video not found!</h1>
<?php
	} else {

		$r = $r[0];
	$db->nquery("update video set count=count+1 where id=?", array($r[0]));
	?>
<video controls>
	<source src='videos/<?php echo $r[3]; ?>' type='video/mp4; codecs="avc1.42E01E, mp4a.40.2"'>
</video>
<?php
	}
}

?>