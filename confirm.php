<?php
require 'lib/global.php';

// Process login (field "p" is created in uservalidation.js and contain the SHA512 hash of the password so raw password will never be sent to server)
if(isset($_POST['p'])) {
	if($USER->confirmUser($_POST['code'],$_POST['p'])) {
		// User confirmed!
		$html="<p>Congratulations, your account has been confirmed!</p><p>You can now <a href='login.php'>login</a> using your email address and the password you selected.</p>";
	} else {
		// Unable to register
		$html="<p>Unable to confirm account, please check your email and <a href='".$CONFIG['site']['url']."/confirm.php?code=".$_POST['code']."'>try again</a> or contact the site <a href='mailto:".$CONFIG['site']['admin']."'>administrator</a></p>";
	}
} else {
	$theform=new htmlForm('confirm.php');
	$theform->addInput('Confirmation code',array('type' => 'text', 'name' => 'code', 'value' => $_GET['code'], 'required' => ''));
	$theform->addInput('Password',array('type' => 'password', 'name' => 'pwd1', 'value' => '', 'required' => ''));
	$theform->addInput('Repeat password',array('type' => 'password', 'name' => 'pwd2', 'value' => '', 'required' => ''));
	$theform->addInput(FALSE,array('type' => 'button', 'value' => 'Confirm your account', 'class' => 'button', 'onclick' => 'regformhash(this.form);'));
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
<script src="js/sha512.js"></script>
<script src="js/uservalidation.js"></script>
</body>

</html>
