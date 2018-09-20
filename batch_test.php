<?php
require 'lib/global.php';
$pubmed=new PHPMed();
$researchers=new NGIresearchers();
$publications=new NGIpublications();
$pubtrawl_cache='cache/trawltest.json';

if($USER->auth>0) {
	if(file_exists($pubtrawl_cache)) {
		// Trawl has already been started
		$pubtrawl_json=file_get_contents($pubtrawl_cache);
		$pubtrawl=json_decode($pubtrawl_json,TRUE);
		//$last=array_search($pubtrawl['meta']['last'], $pubtrawl['lab_list']);
		//$labs=array_slice($pubtrawl['lab_list'],$pubtrawl['meta']['last']+1,count($pubtrawl['lab_list']),TRUE);
	} else {
		// New trawl
		$lab_query=sql_query("SELECT * FROM labs ORDER BY lab_name");
		while($lab=$lab_query->fetch_assoc()) {
			if($id=$researchers->getClarityID($lab['lab_clarity_uri'])) {
				$labs[$id]=$lab;
			}
		}

		$pubtrawl=array('meta' => array('start' => time(), 'end' => FALSE, 'last' => FALSE), 'lab_list' => $labs, 'data' => array());
		$update=file_put_contents($pubtrawl_cache, json_encode($pubtrawl));
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
		<span class="start_trawl button">Begin trawl</span> 
		<span class="pause_trawl warning button">Pause trawl</span>
	</div>
	<div class="large-12 columns">
		<?php
		foreach($pubtrawl['lab_list'] as $id => $lab) {
			echo '<span class="secondary label" id="status-'.$id.'">Pending</span> <span id="lab-'.$id.'">'.$lab['lab_name'].'</span><span id="result-'.$id.'"></span><br>';
		}
		?>
	</div>
</div>

<script src="js/vendor/jquery.js"></script>
<script src="js/vendor/what-input.js"></script>
<script src="js/vendor/foundation.js"></script>
<script src="js/app.js"></script>
</body>

</html>
