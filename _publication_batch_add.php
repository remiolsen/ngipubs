<?php

/*

- check if log-file is active (no end date)


- check for last lab where pubs were retrieved
- loop through labs and "continue" while loop when "last lab" is found

- write to log file (pubtrawl.json)

pubtrawl.json

meta
	timestamp
		start
		end
	parameters (eg. query-type)
data
	search 1
		meta
			timestamp
		data
			n1
				lab
					name
					query
				publications
					pmid1
					pmid2
			n2
				lab
					name
					query
				publications
					pmid3
					pmid4
	search 2
	...

*/


require 'lib/global.php';

if($USER->auth>0) {
	//$start = microtime(true);
	$pubtrawl_cache='cache/pubtrawl_3.json';
	$date_filter=' AND (2016/01/01:2016/12/31[PDAT])';
	$pub_limit=150;
	$total=0;
	
	$pubmed=new PHPMed();
	$researchers=new NGIresearchers();
	$publications=new NGIpublications();

	if(file_exists($pubtrawl_cache)) {
		// Trawl has already been started
		$pubtrawl_json=file_get_contents($pubtrawl_cache);
		$pubtrawl=json_decode($pubtrawl_json,TRUE);
		$labs=array_slice($pubtrawl['lab_list'],$pubtrawl['meta']['last']+1,count($pubtrawl['lab_list']),TRUE);
	} else {
		// New trawl
		$lab_query=sql_query("SELECT lab_clarity_uri FROM labs ORDER BY lab_name");
		while($lab=$lab_query->fetch_assoc()) {
			$labs[]=$lab['lab_clarity_uri'];
		}

		$pubtrawl=array('meta' => array('start' => time(), 'end' => FALSE, 'last' => FALSE), 'lab_list' => $labs, 'data' => array());
	}
	
	foreach($labs as $key => $lab_clarity_uri) {
		$lab_data=$researchers->getLab($lab_clarity_uri);
		if($lab_data['query']['query_string']) {
			$pub_data=$pubmed->parsedSearch($lab_data['query']['query_string']['pi'].$date_filter);
			$added=$publications->addBatch($pub_data,$lab_data);
			$error=FALSE;
			$pub_count=count($pub_data);
			$total+=$pub_count;
		} else {
			$added=FALSE;
			$error='Missing lab data - could not perform search';
		}
		
		$data_new[$lab_clarity_uri]=array('publications' => $added, 'error' => $error);
		
		if($total>$pub_limit) {
			//$time_elapsed_secs = microtime(true) - $start;
			$pubtrawl['data']=array_merge($pubtrawl['data'],$data_new);
			$pubtrawl['meta']['last']=$key;
			$update=file_put_contents($pubtrawl_cache, json_encode($pubtrawl));
			echo '<pre>';
			print_r($pubtrawl['meta']);
			echo '</pre>';
			exit;
		}
	}
	
	// Script has reached end of lab list, finish trawl
	$pubtrawl['data']=array_merge($pubtrawl['data'],$data_new);
	$pubtrawl['meta']['last']=$key;
	$pubtrawl['meta']['end']=time();
	$update=file_put_contents($pubtrawl_cache, json_encode($pubtrawl));
	echo '<pre>';
	print_r($pubtrawl['meta']);
	echo '</pre>';
} else {
	// Not logged in
	$errors[]='Not logged in';
}
