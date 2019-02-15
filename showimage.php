<?php
include_once('config.php');

$submission = acjSubmission::retrieve_by_code($_REQUEST['code']);
if(($submission)&&($submission->submissiontype == 'image'))
{
	header('Content-type: '.$submission->mime);
	header('Content-length: '.filesize($DATAFOLDER . $submission->value));

	$file = @fopen($DATAFOLDER . $submission->value, 'rb');
	if ($file)
	{
		fpassthru($file);
		exit;
	}
}

?>
