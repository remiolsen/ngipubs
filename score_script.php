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

$pubid = $argv[1];
# Start forgery
$USER = new stdClass();
$USER->data = array();
$USER->data["uid"] = 1;
$USER->data["user_email"] = "pub_script@ngipubs.local";
global $USER;
# End forgery
global $CONFIG;
$pubmed=new PHPMed();
$publications=new NGIpublications();

$pubs=[];
if(isset($pubid)) {
  array_push($pubs, $pubid);
} else {
  $pubs_query=sql_query("SELECT p.id, p.pmid FROM publications p JOIN publications_text pt ON p.id = pt.publication_id WHERE p.pubdate>='2018-01-01' AND (pt.text='false' or pt.text='[]' or pt.text = 'false');");
  if($pubs_query){
    while($pub=$pubs_query->fetch_assoc()) {
    	array_push($pubs, $pub['id']);
    }
  }
  $pubs_query=sql_query("SELECT id, pmid FROM publications WHERE pubdate>='2018-01-01' AND id NOT IN (SELECT publication_id from publications_text);");
  if($pubs_query) {
    while($pub=$pubs_query->fetch_assoc()) {
      array_push($pubs, $pub['id']);
    }
  }
}

foreach($pubs as $pub) {
  print(date(c)."  --  start ".$pub."\n");
  if($check=sql_fetch("SELECT id from publications where id=$pub")) {
    $ret = $publications->updatePublication($pub, FALSE, TRUE);
    print("publication ".$pub." -- text: ".json_encode($ret["full_text"]).", scoring: ". json_encode($ret["scoring"]) ."\n");
  } else {
    print("Invalid pub ID: " . $pub."\n");
  }
}



?>
