<?php

if(!isset($_GET['search'])){
	header("location: /$base");
}

$search = urldecode($_GET['search']);
$search = implode("|", explode(" ", $search));

$r = $db->query("select *,count(*) as cnt from tags where tag REGEXP ? group by videoId", array($search));

function body(){
	global $r;
	global $nav;
	$li = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer eros diam, varius vel tincidunt quis, faucibus nec diam. Morbi scelerisque, erat nec volutpat pellentesque, lacus augue lacinia quam, sed pellentesque lorem turpis id dui. Curabitur quis dui rutrum, faucibus ante at, sagittis nisl. Praesent a molestie ligula. Quisque accumsan justo malesuada neque sollicitudin, a pulvinar nibh varius. Fusce quis ipsum elit. Mauris diam ex, cursus et sollicitudin sed, dictum eget erat. Phasellus placerat id massa in ultrices. Sed accumsan, orci in pharetra lobortis, ante nibh lobortis libero, a vulputate tortor mi quis turpis. Duis nec sapien placerat, iaculis mauris et, pulvinar nulla.
<br /><br />
Pellentesque imperdiet condimentum nisl. Vestibulum vestibulum ipsum et leo porttitor viverra. Mauris tincidunt, dui ut venenatis commodo, turpis elit dignissim nibh, eu consequat nisi libero sed enim. Aliquam erat volutpat. Nulla malesuada efficitur dolor laoreet porttitor. Donec elementum maximus egestas. Etiam tempus nisl arcu, ut lacinia elit rutrum eget.";
	// $r = array();
	// for($i = 0; $i < 15; $i++){
	// 	$r[] = array("videoId" => "1234", "name" => "Video $i", "description" => $li);
	// }
	if(count($r)){
		foreach($r as $v){
			?>
			<a href='index.php?page=view&id=<?php echo $v['videoId']; ?>'>
				<div class="panel panel-primary">
					<div class="panel-heading">
						<h3 class="panel-title"><?php echo $v['name']; ?></h3>
					</div>
					<div class="panel-body">
						<img style='display: inline; float: left; margin-right: 15px;' src='' width="196px" height="110px" /><span style='height: 110px; overflow: hidden;'><div style='max-height: 110px; overflow: hidden;'><?php echo $v['description']; ?></div></span>
					</div>
				</div>
			</a>

			<?php
		}
	} else {
		?>
		<center><h1> No videos found!</h1></center>
		<?php
	}
}

?>