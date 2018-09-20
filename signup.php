<?php
require 'lib/global.php';

if(isset($_POST['submit'])) {
	if($USER->addUser($_POST['email'])) {
		// User added
		$html="<p>Great success!</p><p>Please check your inbox and confirm your registration by following the link provided in the email. If you haven't received any message, please contact the site <a href='mailto:".$CONFIG['site']['admin']."'>administrator</a></p>";
	} else {
		// Unable to register
		$html="<p>Invalid email address, please <a href='".$CONFIG['site']['url']."/signup.php'>try again</a> or contact the site <a href='mailto:".$CONFIG['site']['admin']."'>administrator</a></p>";
	}
} else {
	$theform=new htmlForm('signup.php');
	$theform->addInput('Email',array('type' => 'email', 'name' => 'email', 'value' => '', 'required' => ''));
	$theform->addInput(FALSE,array('type' => 'submit', 'name' => 'submit', 'value' => 'Sign up', 'class' => 'button'));
	$html=$theform->render();
}


// Render Page
//=================================================================================================
?>

<!doctype html>
<html class="no-js" lang="en" dir="ltr">

<head>
	<meta charset="utf-8">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo $CONFIG['site']['name']; ?></title>
	<link rel="stylesheet" href="css/foundation.css">
	<link rel="stylesheet" href="css/app.css">
	<link rel="stylesheet" href="css/icons/foundation-icons.css" />
</head>

<body>
<?php require '_menu.php'; ?>

<div class="row">
<br>
<div class="large-12 columns">
<?php echo $html; ?>
</div>
</div>

<script src="js/vendor/jquery.js"></script>
<script src="js/vendor/what-input.js"></script>
<script src="js/vendor/foundation.js"></script>
<script src="js/app.js"></script>
</body>

</html>
