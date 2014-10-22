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

$loggingIn = true;

function body(){
  global $loggingIn;
  if($loggingIn){
?>

<form class="form-horizontal" action="login" method="post">
<fieldset>

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
  <label class="col-md-4 control-label" for="login">Double Button</label>
  <div class="col-md-8">
    <button id="login" name="login" class="btn btn-success">Login</button>
    <button id="register" name="register" class="btn btn-primary" onclick='window.location="login/register"'>Register</button>
  </div>
</div>

</fieldset>
</form>



<?php

} else if (!$loggingIn){
?>

<form class="form-horizontal">
<fieldset>

<!-- Form Name -->
<legend>Form Name</legend>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="name">Full name</label>  
  <div class="col-md-5">
  <input id="name" name="name" type="text" placeholder="Name" class="form-control input-md" required="">
    
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="username">Username</label>  
  <div class="col-md-5">
  <input id="username" name="username" type="text" placeholder="Username" class="form-control input-md" required="">
    
  </div>
</div>

<!-- Password input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="password">Password</label>
  <div class="col-md-5">
    <input id="password" name="password" type="password" placeholder="Password" class="form-control input-md" required="">
    
  </div>
</div>

<!-- Password input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="confirm">Confirm password</label>
  <div class="col-md-5">
    <input id="confirm" name="confirm" type="password" placeholder="Password" class="form-control input-md" required="">
    
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="email">email</label>  
  <div class="col-md-5">
  <input id="email" name="email" type="text" placeholder="e-mail" class="form-control input-md" required="">
    
  </div>
</div>

<!-- Button -->
<div class="form-group">
  <label class="col-md-4 control-label" for="singlebutton">Single Button</label>
  <div class="col-md-4">
    <button id="singlebutton" name="singlebutton" class="btn btn-primary">Button</button>
  </div>
</div>

</fieldset>
</form>


<?php


}
}



?>