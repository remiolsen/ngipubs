<?php
require 'lib/global.php';

if($USER->auth>0) {
	$publications=new NGIpublications();
	if(!$page=filter_input(INPUT_GET,'page',FILTER_VALIDATE_INT)) {
		$page=1;
	}

	$query=$publications->reservePublications($USER->data['user_email'],date('Y'),1);
	$publication_list=$publications->showPublicationList($query,$page);
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
	<link rel="stylesheet" href="css/foundation.min.css">
	<link rel="stylesheet" href="css/app.css">
	<link rel="stylesheet" href="css/icons/foundation-icons.css" />
</head>

<body>
<?php require '_menu.php'; ?>

<div class="row">
	<br>
	<div class="large-12 columns">
		<?php echo $publication_list['list']; ?>
	</div>
	<div class="large-12 columns">
		<?php echo $publication_list['pagination']; ?>
	</div>
</div>

<script src="js/vendor/jquery.js"></script>
<script src="js/vendor/what-input.js"></script>
<script src="js/vendor/foundation.min.js"></script>
<script src="js/app.js"></script>
</body>

</html>
