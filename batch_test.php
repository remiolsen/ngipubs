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
		<span class="start_trawl button">Begin trawl</span>
		<span class="pause_trawl warning button">Pause trawl</span>
	</div>
	<div class="large-12 columns" id="trawlmeter">
		<div class="progress" role="progressbar" tabindex="0" aria-valuenow="0" aria-valuemin="0" aria-valuemax="0">
			<span id="trawlbar" class="progress-meter" style="width: 0%">
		    <p id="trawltext" class="progress-meter-text"></p>
		  </span>
		</div>
	</div>
	<div id="trawl_labs" class="large-12 columns">
	</div>
</div>

<script src="js/vendor/jquery.js"></script>
<script src="js/vendor/what-input.js"></script>
<script src="js/vendor/foundation.js"></script>
<script type='text/javascript'>
	$(document).ready(function() {
		items = "";
		if (sessionStorage.trawl_list) {
			items = JSON.parse(sessionStorage.trawl_list);
		}
		else {
			$.ajax({
				url: "_publication_batch_add.php",
				async: false,
				success: function(json) {
					items = json;
				}
			});
			//items = JSON.parse(req.responseText);
			sessionStorage.setItem("trawl_list", items);
			items = JSON.parse(items);
		}
		$.each(items, function( id, lab ) {
			var text = "Pending";
			var label = "";
			if(lab['session'] == 'done') {
				text = "Done"; label = "success";
			}
			if(lab['session'] == 'error') {
				text = "Error"; label = "alert";
			}
			var out='<span class="secondary label '+label+'" id="status-'+id+'">'+text+'</span> <span id="lab-'+id+'">'+lab['lab_name']+'</span><span id="result-'+id+'"></span><br>';
			$('#trawl_labs').append(out);
		});
	});
</script>
<script src="js/app.js"></script>
</body>

</html>
