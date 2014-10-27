<?php


function body(){
?>

<div class="form-group">
    <input id="search" name="search" type="text" placeholder="" class="form-control input-md" required=""><button id="button" name="button" class="btn btn-success" onclick="window.location='search/'+encodeURIComponent(document.getElementById('search').value)">Search</button>
</div>

<?php

}


?>