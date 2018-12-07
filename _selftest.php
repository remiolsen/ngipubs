<?php

require 'lib/global.php';
require 'lib/config.php';
ini_set('default_socket_timeout', 120);

if($USER->auth>1) {

  if (isset($_GET['test'])) {

    $test = $_GET['test'];
    $test_url = $CONFIG['publications']['parse_url'].'/selftest/'.$test;
    $response_json = file_get_contents($test_url);
    $response = json_decode($response_json);

    echo('errors: '.$response->errors.'<br>');
    echo('failures: '.$response->failures.'<br>');
    echo('<pre>'.$response->output.'</pre>');

  } else {

    $test_url = $CONFIG['publications']['parse_url'].'/selftest';
    $response_json = file_get_contents($test_url);
    $response = json_decode($response_json);
    $out = [];
    foreach ($response->subresource_uris as $name => $value) {
      if ($value != "all") {
        array_push($out, $value);
      }
    }

    echo(json_encode($out));

  }

}

?>
