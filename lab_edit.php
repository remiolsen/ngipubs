<?php
require 'lib/global.php';
$researchers=new NGIresearchers();

if($USER->auth>0) {
	if(isset($_POST['submit'])) {
		if($update=$researchers->setPI($_POST['lab_id'],$_POST['lab_pi'])) {
			header('Location:researchers.php');
		} else {
			echo "error";
		}
	} else {
		$lab=$researchers->getLab($_GET['id']);

		if(trim($lab['lab']['lab_pi'])=='') {
			$select[]='--- Please select PI ---';
		}
		
		foreach($lab['group_data'] as $researcher) {
			$select[$researcher['email']]=$researcher['first_name'].' '.$researcher['last_name'];
		}
		
		$theform=new htmlForm("lab_edit.php");
		$theform->addInput(FALSE,array('name' => 'lab_id', 'type' => 'hidden', 'value' => $_GET['id']));
		$theform->addSelect("Lab PI","lab_pi",$select, array($lab['lab']['lab_pi']));
		$theform->addInput(FALSE,array('name' => 'submit', 'type' => 'submit', 'value' => 'Save', 'class' => 'button'));
	}
} else {
	// Not logged in
	header('Location:login.php');
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
		<?php echo $theform->render(); ?>
	</div>
</div>

<script src="js/vendor/jquery.js"></script>
<script src="js/vendor/what-input.js"></script>
<script src="js/vendor/foundation.js"></script>
<script src="js/app.js"></script>
</body>

</html>
