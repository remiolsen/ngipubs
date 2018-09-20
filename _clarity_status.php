<?php
require 'lib/global.php';
$researchers=new NGIresearchers();
$db_data=$researchers->getDBStatus();

foreach($db_data as $type => $data) {
	echo $type.' cache version: '.$data['cache']['version'].' (total: '.$data['cache']['total'].' '.$type.')<br>';
	echo $type.' database version: '.$data['database']['version'].' (total: '.$data['database']['total'].' '.$type.')<br>';
}