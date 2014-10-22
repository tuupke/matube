<?php

$base = "2in28";

function __autoload($cn) {
    include "system/$cn.php";
}

$db = new Database();

$user = new User();


$page = isset($_GET['page'])?$_GET['page']:"home";

$title = $lMenu = $body = "";

include("pages/$page.php");


$loggedIn = $user->isLoggedIn();


$un = $wm = $login = $menu = "";
if($loggedIn){
    $un = $user->getUsername();
    $wm = "Welcome $un&nbsp;";
    $login = "Logout";
    $menu = '<li class="active"><a href="upload">Upload video</a></li>';
} else {
    $login = "Log in";
}

?>
<html>
<head>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="css/bootstrap-theme.min.css">

<!-- matube css -->
<link rel="stylesheet" href="css/matube.css">

<!-- Production jquery -->
<script src="js/jquery-2.1.1.min.js"></script>

<!-- Latest compiled and minified JavaScript -->
<script src="js/bootstrap.min.js"></script>
</head>
<body>
  <nav class="navbar navbar-default" role="navigation">
    <div class="container-fluid">
      <!-- Brand and toggle get grouped for better mobile display -->
      <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="index.php">MaTube</a>
      </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
          <ul class="nav navbar-nav">
            <?php echo $menu; ?>
          </ul>

          <ul class="nav navbar-nav navbar-right">
            <li>
              <div id="usernameField">
                  <span><?php echo $wm; ?></span><span id="username"></span>
              </div>
            </li>
            <li>
              <button class="btn btn-sm btn-primary btn-danger" id="logout" onclick="window.location='login'"><span><?php echo $login;?></span><span class="glyphicon"></span></button>
            </li>
          </ul>
      </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
  </nav>
<div class='container'>
    <div class='col-md-12'>
        <div class='col-md-2'><?php echo $lMenu; ?></div>
        <div class='col-md-10'><?php body(); ?></div>
    </div>
</div>
</body>
</html>
