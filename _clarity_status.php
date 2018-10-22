<?php
require 'lib/global.php';
$researchers=new NGIresearchers();
$db_data=$researchers->getDBStatus();

foreach($db_data as $type => $data) {
	echo ucfirst($type).' cache version <span class="secondary label">'.$data['cache']['version'].'</span> <span class="warning label">Total: '.$data['cache']['total'].' '.$type.'</span><br>';
	echo ucfirst($type).' database version <span class="secondary label">'.$data['database']['version'].'</span> <span class="warning label">Total: '.$data['database']['total'].' '.$type.'</span><br><br>';
}