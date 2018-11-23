<?php
require 'lib/global.php';

if($USER->auth>0) {
	$publications=new NGIpublications();
	if(isset($_REQUEST['comment'])) {
		$comment = $_REQUEST['comment'];
	} else {
		$comment = '';
	}
	if(isset($_REQUEST['publication_id'])) {
		switch($_REQUEST['type']) {
			case 'verify':
				if($publications->updatePubStatus($_REQUEST['publication_id'],'verified', $USER->data['uid'], $comment)) {
					echo json_encode(array('status' => 'Verified'));
				}
			break;

			case 'discard':
				if($publications->updatePubStatus($_REQUEST['publication_id'],'discarded', $USER->data['uid'], $comment)) {
					echo json_encode(array('status' => 'Discarded'));
				}
			break;

			case 'maybe':
				if($publications->updatePubStatus($_REQUEST['publication_id'],'maybe', $USER->data['uid'], $comment)) {
					echo json_encode(array('status' => 'Maybe'));
				}
			break;
		}
	}
}
