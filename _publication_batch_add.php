<?php


require 'lib/global.php';
$researchers=new NGIresearchers();
if($USER->auth>0) {
	$labs_list=$researchers->listLabs();
	$labs=[];
	foreach($labs_list['all'] as $id => $lab) {
		if(count($lab['errors'])==0){
			$id = $researchers->getClarityID($lab['lab']['lab_clarity_uri']);
			$labs[$id]=$lab['lab'];
			$lab[$id]['session'] = 'pending';
		}
	}
	echo json_encode($labs);
} else {
	// Not logged in
	$errors[]='Not logged in';
}
?>
