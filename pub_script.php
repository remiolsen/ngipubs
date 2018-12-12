<?php

require 'lib/global.php';
require 'lib/config.php';


ini_set('register_argc_argv', 0);
if (!isset($argc) || is_null($argc))
{
    print('Not CLI mode');
    die();
} else {
    print("CLI mode\n");
}

$labid = $argv[1];
# Start forgery
$USER = new stdClass();
$USER->data = array();
$USER->data["uid"] = 1;
$USER->data["user_email"] = "pub_script@ngipubs.local";
global $USER;
# End forgery

$pubmed=new PHPMed();
$researchers=new NGIresearchers();
$publications=new NGIpublications();

$labs=[];
if(isset($labid)) {
  array_push($labs, [$labid, "cli_parameter"]);
} else {
  $labs_query=sql_query("SELECT * FROM labs WHERE lab_status <> 'disabled' or lab_status IS NULL ORDER BY lab_name");
  while($lab=$labs_query->fetch_assoc()) {
  	$id = $researchers->getClarityID($lab['lab_clarity_uri']);
  	array_push($labs, [$id, $lab['lab_name']]);
  }
}

foreach($labs as $lab) {
  $labid = $lab[0]; $labname = $lab[1];
  print(date(c)."  --  start ".$labname."\n");
  if($lab=$researchers->getLab($labid)) {
    $added=0;
    $found=0;

    // Build pubmed query with a limitation on publication date
    date_default_timezone_set('UTC');
    $timestamp = time() - ($CONFIG['publications']['max_days'] * 86400);
    $dt = date('Y/m/d', $timestamp);
    $pmq = '('.$lab['query']['query_string']['pi'].')' . ' AND ("'.$dt.'"[Date - Publication] : "3000"[Date - Publication])';

    $data=$pubmed->parsedSearch($pmq);
    $found=count($data);
    foreach($data as $pmid => $article) {
      $add=$publications->addPublication($article,$lab);
      if($add['data']['status']=="added") {
        $added++;
        print("+ ".$article["uid"]."\n");
      }
    }
    print(date(c)."  --  found: ".$found.", added: ".$added."\n");
  } else {
    // Lab not found
    print('Invalid Lab ID:' . $labid."\n");
  }
}



?>
