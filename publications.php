<?php
require 'lib/global.php';
$pubmed=new PHPMed();
$researchers=new NGIresearchers();
$publications=new NGIpublications();

if($USER->auth>0) {
	if(isset($_GET['id'])) {
		$output=$publications->showPublication($_GET['id']);
	} else {
		if(filter_var($_REQUEST['year'],FILTER_VALIDATE_INT)) {
			$output=$publications->showPublicationList($_REQUEST['year']);
		} else {
			$output=$publications->showPublicationList(date("Y"));
		}

		//----------------------------------------------------
		// "Sync" with SciLifeLab Publications database

		/*
		// JSON sources for Production and Applications papers in the SciLifeLab database
		$sources=array(
			'https://publications.scilifelab.se/label/NGI%20Stockholm%20%28Genomics%20Production%29.json', 
			'https://publications.scilifelab.se/label/NGI%20Stockholm%20%28Genomics%20Applications%29.json'
		);
		$list=$publications->checkDB($sources);
		
		// Auto add missing papers
		$autoadd_data=$pubmed->summary($list['missing']);
		
		// OBS! This will NOT parse authors since no lab is given, however this will be done if the PMID shows up in searches
		// Not a big issue since this is likely only a one-time event and only for old papers
		$add_papers=$publications->addBatch($autoadd_data,FALSE); 

		//These two papers are listed in publications db but classified here as "discarded" - double check these!
		// 27913302: NGI labels removed in pub db (seq on HiSeq 1500)

		if(count($list['mismatch'])>0) {
			$missing=implode(',', $list['mismatch']);
			$query=sql_query("SELECT * FROM publications WHERE pmid IN($missing)");
			$output=$publications->showSelectedPublications($query);
		}
		*/
		
		//----------------------------------------------------
		// Examples
		
		//$publist=$publications->listPublications(2017);
		//$output=$publications->formatPubList($publist);

		//$query=sql_query("SELECT * FROM publications WHERE status IS NULL AND score>5 AND pubdate>='2017-01-01' ORDER BY score DESC");
		//$query=sql_query("SELECT * FROM publications WHERE status='maybe' AND pubdate>='2017-01-01' ORDER BY score DESC");
		//$output=$publications->showSelectedPublications($query);

		//----------------------------------------------------
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
		<?php echo $output; ?>
		<?php //echo $table->render(); ?>
	</div>
</div>

<script src="js/vendor/jquery.js"></script>
<script src="js/vendor/what-input.js"></script>
<script src="js/vendor/foundation.js"></script>
<script src="js/app.js"></script>
</body>

</html>
