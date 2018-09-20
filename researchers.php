<?php
require 'lib/global.php';

if($USER->auth>0) {
	$researchers=new NGIresearchers();

	$lab_list=$researchers->listLabs();	
	$lab_errors=$researchers->formatLabList($lab_list['errors']);
	$all_labs=$researchers->formatLabList($lab_list['all']);

	$res_list=$researchers->listResearchers();
	$res_errors=$researchers->formatResearcherList($res_list['errors']);
	
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
		<div class="card">
			<div class="card-divider">
				Clarity status
			</div>
			<div class="card-section large-6 columns">
				<div id="clarity_status"></div>
				<br>
				<div class="button-group">
					<button class="small button right" id="load_clarity">Update</button>
				</div>
			</div>
			<div class="card-section large-6 columns" id="clarity_status_message"></div>
		</div>
	</div>
</div>

<div class="row">
	<div class="large-12 columns">
		<h3>Errors</h3>
	</div>
	<div class="large-12 columns">
		<?php echo $lab_errors; ?>
	</div>
	<div class="large-12 columns">
		<?php echo $res_errors; ?>
	</div>
	<div class="large-12 columns">
		<h3>Labs</h3>
	</div>
	<div class="large-12 columns">
		<?php echo $all_labs; ?>
	</div>
</div>

<script src="js/vendor/jquery.js"></script>
<script src="js/vendor/what-input.js"></script>
<script src="js/vendor/foundation.js"></script>
<script src="js/app.js"></script>
</body>

</html>

