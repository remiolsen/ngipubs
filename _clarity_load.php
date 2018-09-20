<?php
require 'lib/global.php';
$researchers=new NGIresearchers();

switch($_GET['task']) {
	case 'load':
		$cached=$researchers->getClarityData($_GET['type']);
		echo json_encode($cached);
	break;
	
	case 'update':
		$updated=$researchers->updateDB($_GET['type']);
		echo json_encode($updated);
	break;
}