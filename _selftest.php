<?php

require 'lib/global.php';
require 'lib/config.php';

if($USER->auth>1) {
  ini_set('default_socket_timeout', 180);
  $test_url = $CONFIG['publications']['parse_url'].'/selftest';
  $response_json = file_get_contents($test_url);
  $response = json_decode($response_json);

  echo('errors: '.$response->errors.'<br>');
  echo('failures: '.$response->failures.'<br>');
  echo('<pre>'.$response->output.'</pre>');

}


?>
