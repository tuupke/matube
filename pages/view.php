<?php


$id = $_GET['id'];

$r = $db->query("select * from video, `user` where video.id=? and video.ownedBy=`user`.entityId", array($id), PDO::FETCH_BOTH);

function body(){
	global $r, $db, $user;
	if(count($r)!=1){


?>
<h1>Video not found!</h1>
<?php
	} else {
		$r = $r[0];
		if($r['public']==1 || $r['ownedBy']==$user->getId()){
	$db->nquery("update video set count=count+1 where id=?", array($r[0]));
	?>

<style>
h1 {
	display: inline-block;
	max-width: 550px;
	height: 45px;
	overflow: hidden;
}

h3 {
	padding-top: 10px;
}

</style>
<span style='display: inline-block;'>
<video id='video' controls>
	<source src='videos/<?php echo $r[3]; ?>' type='video/mp4; codecs="avc1.42E01E, mp4a.40.2"'>
</video><br />
<h1 id='title'><?php echo $r[2]; ?></h1><h3 id='views' style='float: right;'>Views <?php echo $r[5]; ?></h3><br />By: <b><?php echo $r[12]; ?></b>
</span>
<?php
		} else {
?>
<h1>Video is private!</h1>
<?php
		}
	}
}

?>