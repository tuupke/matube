<?php

$result;
if(isset($_POST['username'])){ // Register account
  $result = User::createUser($_POST);
  if($result['success']){
    header("location: /$base/login");
  }
}


function body(){
  global $result;

?>

<form class="form-horizontal" method="POST" action="register">
<fieldset>

<!-- Form Name -->
<legend>Register</legend>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="name">First name</label>  
  <div class="col-md-5">
  <input  value="<?php echo isset($_POST['firstName'])?$_POST['firstName']:''; ?>" id="name" name="firstName" type="text" placeholder="Name" class="form-control input-md" required="">
    <?php echo isset($result['firstName'])?$result['firstName']:''; ?>
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="middlename">Middle name</label>  
  <div class="col-md-5">
  <input value="<?php echo isset($_POST['middleName'])?$_POST['middleName']:''; ?>" id="middlename" name="middleName" type="text" placeholder="Middle name" class="form-control input-md">
    <?php echo isset($result['middleName'])?$result['middleName']:''; ?>
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="lastname">Last name</label>  
  <div class="col-md-5">
  <input value="<?php echo isset($_POST['lastName'])?$_POST['lastName']:''; ?>" id="lastname" name="lastName" type="text" placeholder="Last name" class="form-control input-md" required="">
    <?php echo isset($result['lastName'])?$result['lastName']:''; ?>
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="username">Username</label>  
  <div class="col-md-5">
  <input value="<?php echo isset($_POST['username'])?$_POST['username']:''; ?>" id="username" name="username" type="text" placeholder="Username" class="form-control input-md" required="">
    <?php echo isset($result['username'])?$result['username']:''; ?>
  </div>
</div>

<!-- Password input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="password">Password</label>
  <div class="col-md-5">
    <input id="password" name="password" type="password" placeholder="Password" class="form-control input-md" required="">
    <?php echo isset($result['password'])?$result['password']:''; ?>
  </div>
</div>

<!-- Password input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="confirm">Confirm password</label>
  <div class="col-md-5">
    <input id="confirm" name="confirm" type="password" placeholder="Password" class="form-control input-md" required="">
    <?php echo isset($result['confirm'])?$result['confirm']:''; ?>
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="email">email</label>  
  <div class="col-md-5">
  <input value="<?php echo isset($_POST['email'])?$_POST['email']:''; ?>" id="email" name="email" type="text" placeholder="e-mail" class="form-control input-md" required="">
    <?php echo isset($result['email'])?$result['email']:''; ?>
  </div>
</div>

<!-- Button -->
<div class="form-group">
  <label class="col-md-4 control-label" for="button"></label>
  <div class="col-md-4">
    <button id="button" name="button" class="btn btn-success">Register</button>
  </div>
</div>

</fieldset>
</form>


<?php

}



?>