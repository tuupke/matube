<?php

$loggedIn = $user->isLoggedIn();

$extra = $uname = "";

if(!$loggedIn && isset($_POST['un']) && isset($_POST['pass'])){
	$un = $user->checkCredentials($_POST['un'], $_POST['pass']);
	if($un !== null){
		header("location: /$base");
	} else {
		// echo 'Hier gaat iets fout';
		$uname = isset($_POST['un'])?"value='".$_POST['un']."'":"";
	}
} else if($loggedIn){
	$user->logOut();
}


function body(){
  global $loggingIn;
?>

<form class="form-horizontal" action="login" method="post">
<fieldset>
<?php global $result; if(isset($result['message'])){echo $result['message'];} ?>
<!-- Form Name -->
<legend>Login Form</legend>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="un">Username</label>  
  <div class="col-md-5">
  <input id="un" name="un" type="text" placeholder="Username" class="form-control input-md" required="" <?php global $uname; echo $uname; ?>>
    
  </div>
</div>

<!-- Password input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="pass">Password</label>
  <div class="col-md-5">
    <input id="pass" name="pass" type="password" placeholder="Password" class="form-control input-md" required="">
    
  </div>
</div>

<!-- Button (Double) -->
<div class="form-group">
  <label class="col-md-4 control-label" for="login"></label>
  <div class="col-md-8">
    <button id="login" name="login" class="btn btn-success">Login</button>
    <button id="register" name="register" class="btn btn-primary" onclick='window.location="register";return false;'>Register</button>
  </div>
</div>

</fieldset>
</form>
<?php
}



?>