<?php 
// file: index.php G
	session_start();
//	if (isset($_SESSION['user_id'])){ // TODO: Resurrect?
//		header ('Location: application.php');
//}
	require "settings.php";
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="description" content="Sign in for 4D forest modelling and forest visualization.">
	<meta name="keywords" content="forest modeling, forest modelling, forest visualization, forestry, forest management, ecosystem management,landscape level modeling, landscape level modelling, stand level modeling, stand level modelling">
	<title><?php echo App_Name; ?> | Visionary Ecosystem Management</title> 
	<link type="text/css" rel="stylesheet" href="<?php echo Styles_Href; ?>/style.css"> 
	<link type="image/vnd.microsoft.icon" rel="shortcut icon" href="<?php echo Images_Href; ?>/GaiaStudio.ICO">
	<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
	<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
	<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
	<script type="text/javascript" src="http://platform.linkedin.com/in.js">
		api_key:    //insert API key for LinkedIn
		authorize: false
		credentials_cookie: true
		credentials_cookie_crc: true
		onLoad: on_linkedin_load
	</script>
	<script>
		$(function() {
    			$( "#login-demo" ).button({
      				icons: {
					primary: "ui-icon-play"
      				}
   			 });
			$( "#login-demo" ).click(function() {
  				var date = new Date();
    				var str = date.getFullYear() + "-" + (date.getMonth()+1) + "-" + date.getDate() + "|" +  date.getHours() + ":" + date.getMinutes() + ":" + date.getSeconds();
				window.location.replace ("application.php?user_id=" + str);
			});
  		}); 	
	</script>
 	<script type="text/javascript" src="<?php echo JS_Href; ?>/detect_browser.js"></script>
	<script type="text/javascript" src="<?php echo JS_Href; ?>/constants.js"></script>
	<script type="text/javascript" src="<?php echo JS_Href; ?>/linkedin_login.js"></script>
	
</head>
<body onload="init_login ('<?php echo PHP_Href; ?>')">
	<div id="wrapper">
		<form name="login-form" class="login-form">
			<div class="header">
				<center><img id="logo" src="<?php echo Images_Href; ?>/GaiaStudio.PNG" id="header_img" /></center>
				<center><h3>Platform for forest-modeling and 3D visualization</h3></center> 
				<br>
				<center><span id="login-head">Please login</span></center>
				<center><script type='IN/Login'></script></center>
				<center><p id="login-body"></center>
				<center><a id="login-demo" class="login-demo">Demo Login</a></center>
				<br>	
				<center><span>About us</span></center>
				<center><a href="<?php echo About_Page; ?>"> <img id="about_img" src="<?php echo Images_Href; ?>/id_card.png"> </a> </center> 	
			</div>
		</form>
	</div>
	<div class="gradient"></div>
</body>
</html>
