<?php
require 'lib/global.php';
$pubmed=new PHPMed();
$researchers=new NGIresearchers();
$publications=new NGIpublications();

if($USER->auth>0) {

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
				Syncronize from <a href="https://publications.scilifelab.se">publications.scilifelab.se</a>
			</div>
			<div class="card-section large-6 columns">
				<span class="start_sync button">Begin syncing from publications.scilifelab.se</span>
				<p>
					This will fetch all publications stored at <a href="https://publications.scilifelab.se">publications.scilifelab.se</a>
					and mark each of them accordingly in this database.
				</p>
			</div>
			<div class="card-section large-6 columns" id="sync_status_message">
			</div>
		</div>
	</div>
</div>

<script src="js/vendor/jquery.js"></script>
<script src="js/vendor/what-input.js"></script>
<script src="js/vendor/foundation.js"></script>
<script src="js/app.js"></script>
</body>

</html>
