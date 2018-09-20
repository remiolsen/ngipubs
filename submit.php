<?php
require 'lib/global.php';
$publications=new NGIpublications();

if($USER->auth>0) {
	$handle=fopen("pmid.txt","r");
	if($handle) {
		while(($line=fgets($handle))!==FALSE) {
			// process the line read.
			$pmid=trim($line);
			$update=sql_query("UPDATE publications SET submitted='2017-11-03' WHERE pmid='$pmid'");
		}
		
		fclose($handle);
	} else {
		// error opening the file.
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
		<?php //echo $table->render(); ?>
	</div>
</div>

<script src="js/vendor/jquery.js"></script>
<script src="js/vendor/what-input.js"></script>
<script src="js/vendor/foundation.js"></script>
<script src="js/app.js"></script>
</body>

</html>
