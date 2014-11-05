<?php


function body(){
?>
<form onsubmit="window.location='index.php?page=search&search='+document.getElementById('search').value;return false;">
    <div class="input-group">
		<input type="text" class="form-control" id='search' placeholder="Search">
		<span class="input-group-btn">
			<button class="btn btn-success" type="submit">Search</button>
		</span>
	</div>
</form>
<?php

}


?>