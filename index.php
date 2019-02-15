<?php
require_once("corelib/templateMerge.php");
include_once('lib/forms.php');
include_once('config.php');
include_once('ltilib/lti_session.php');
include_once('lib/acj2_lib.php');
include_once('lib/secretManager.php');

include_once('geshi/geshi.php');

define('PHASE_NOTSETUP', 0);
define('PHASE_NOTSTARTED', 1);
define('PHASE_SUBMITTING', 2);
define('PHASE_WAITING', 3);
define('PHASE_COMPARING', 4);
define('PHASE_FINISHED', 5);

$userinfo = checkLTISession();

?>
<html>

<head>
  <title>ACJ-LTI - Adaptive Comparative Judgement for Peer Assessment over LTI</title>
  <link rel="stylesheet" type="text/css" href="html/bootstrap.css" />
  <link rel="stylesheet" type="text/css" href="html/base.css" />
</head>

<body>

    <div class="container">

<?php
   // echo '<pre>'.print_r($userinfo, true).'</pre>';
   // echo '<pre>'.print_r($_REQUEST, true).'</pre>';
if($userinfo == false)
{
	echo "Failed to launch LTI session.<br/><a href='admin.php'>Click here for site administration.</a>";
	echo '<br/><b>POST contains:</b><pre>'.print_r($_POST,1).'</pre>';
	echo '<br/><b>GET contains:</b><pre>'.print_r($_GET,1).'</pre>';
    exit();
}
else
{
    $activity_key = md5($userinfo->getResourceKey());
    //echo "<div><b>Debug:</b>Resource key is ".$userinfo->getResourceKey()." and md5 hash is {$activity_key}</div>";
    $activity = lti_acj_resource::retrieve_by_keyHash($activity_key);
    if($activity !== false)
    {
	    $user = ltiUser::retrieveActivityUser($activity->id, $userinfo->params['user_id']);
	    if($user == false)
	    {
	        $user = new ltiUser();
	        $user->owner_id = $activity->id;
	        $user->ltiRoles = $userinfo->params['roles'];
	        if(isset($userinfo->params['lis_result_sourcedid']))
	            $user->lis_result_sourcedid = $userinfo->params['lis_result_sourcedid'];
	        if(isset($userinfo->params['lis_person_name_family']))
	            $user->familyName = $userinfo->params['lis_person_name_family'];
	        if(isset($userinfo->params['lis_person_name_given']))
	            $user->givenName = $userinfo->params['lis_person_name_given'];
	        if(isset($userinfo->params['lis_person_contact_email_primary']))
	            $user->email = $userinfo->params['lis_person_contact_email_primary'];
	    	$user->firstVisit = time();
	    	$user->lastvisit = time();
	        $user->userID = $userinfo->params['user_id'];
	        $user->insert();
	    }
	    else
	    {
	        $user->ltiRoles = $userinfo->params['roles'];
	        if(isset($userinfo->params['lis_result_sourcedid']))
	            $user->lis_result_sourcedid = $userinfo->params['lis_result_sourcedid'];
	    	$user->lastvisit = time();
	        $user->update();
	    }
        $userinfo->user = $user;
    }
    $output = false;
    if(($activity == false)||(isset($_REQUEST['editactivity'])))
    {
    	if($userinfo->isInstructor())
        	$output = configActivityPage($activity, $userinfo);
        else
        	$output = 'This activity has not been configured yet. Please try again later.';
    }
    if($output !== false)    // I really need to tidy up the logic here. Basically if output is false the activity exists, so do something.
    	echo $output;
    else
    {
        if((isset($_REQUEST['phase']))&&($userinfo->isInstructor()))
        {
            quickUpdatePhase($_REQUEST['phase'], $activity);
        }
        $currentPhase = getPhase($activity);
    	if($userinfo->isInstructor())
        {
        	echo overviewPage($activity, $currentPhase, $userinfo);
        }
        else
        {
           	if($currentPhase==PHASE_SUBMITTING)
            {
            	echo activityInputPage($activity, $userinfo);
            }
            elseif($currentPhase==PHASE_COMPARING)
            {
                checkFormInput($activity, $userinfo);
            	checkReviewAllocations($activity);
            	echo reviewInputPage($activity, $userinfo);
            }
            else
            {
            	echo infoForLearnersPage($activity, $userinfo);
            }
        	//# make sure in users list
            //# Show appropriate part of activity
        }
    }

}

function activityInputPage($activity, $userinfo)
{
    global $SALT, $DATAFOLDER;

    $submission = acjSubmission::retrieve_by_activity_and_owner_ids($activity->id, $userinfo->user->id);
    if(($submission == false)||((isset($_REQUEST['edit']))&&($_REQUEST['edit'] == 1)))
    {
	    $inputForm = new editpage_form();
	    $output = "<h3>{$activity->context_title}</h3>";
	    $output .= "<p>{$activity->question}</p>";
        //$output .= '<pre>'.print_r($activity,1).'</pre>';
		switch($inputForm->getStatus())
		{
		case FORM_NOTSUBMITTED:
            // $inputForm->text =
		    if($submission != false)
            {
			    switch($submission->submissiontype)
			    {
			        case 'code':
			            $inputForm->text = file_get_contents($DATAFOLDER.$submission->value.'.'.$activity->submissionsubtype);
			            break;
			        case 'html':
			            $inputForm->text = $submission->value;
			            break;
			         case 'youtube':
			             $inputForm->url = $submission->value;
			             break;
 			    }
            }
		    $output .= $inputForm->getHtml($activity->submissiontype, $activity->submissionsubtype);
		    break;
		case FORM_SUBMITTED_INVALID:
		    $output .= $inputForm->getHtml($activity->submissiontype, $activity->submissionsubtype);
		    break;
		case FORM_SUBMITTED_VALID:
	        // Really need to do extra validation based on $activity->submissiontype
		    $inputForm->getData($data);
            if($submission == false)
            {
            	$submission = new acjSubmission();
                $submission->activity_id = $activity->id;
                $submission->owner_id = $userinfo->user->id;
                $submission->code = trim(base64_encode(sha1("{$submission->activity_id}:{$submission->owner_id}:$SALT")),'=');
                $submission->latestScore = rand(0,100); // randomizes the order
            }
	        $extraValidationMsgs = doExtraValidationAndSave($activity, $submission, $activity->submissiontype, $data);
	        if(sizeof($extraValidationMsgs))
	        {
	            $inputForm->validateMessages = $extraValidationMsgs;
		        $output .= $inputForm->getHtml($activity->submissiontype);
	        }
	        else
	        {
		        $output .= displayUserInput($submission);
	        }
		    break;
		case FORM_CANCELED:
		    break;
	    }
    }
    else
    {
	    $output = displayUserInput($submission);
    }
    return $output;
}

function checkReviewAllocations($activity)
{
}

function reviewInputPage($activity, $userinfo)
{
	if(isset($_REQUEST['avoid']))
	    $avoid = explode(',',$_REQUEST['avoid']);
	else
	    $avoid = array();
    $dueComps = $activity->get_incomplete_comparisons($activity->round);   //# should just count them
    if(sizeof($dueComps)==0)
    {
        prepareNewAcjRound($activity);
        $avoid = array();
        //$dueComps = $activity->get_incomplete_comparisons($activity->round);//# Don't think this is needed.
    }
    $takeComp = getAComparison($activity, $activity->round, $userinfo->user->userID, $avoid);
	if($takeComp === false)
	{
	    return "<div class=\"alert alert-danger\">The system is unable to allocate you a comparison at the moment. Please try again in a few minutes.</div>";
	}
	else
	{
	    $template = new templateMerge('html/compare.html');
        if(strlen($activity->rubric))
        {
        	$template->pageData['rubric'] = "<div id='rubriclink' style='color:blue;' onclick='showHideRubric()'>View the comparison guidelines.</div>";
            $template->pageData['rubric'] .= "<div id='rubrictext' style='display:none;' >".str_replace("\n", "<br/>", $activity->rubric)."</div>";
        }
	    $template->pageData['lefttext'] = display(acjSubmission::retrieve_acjSubmission($takeComp->left_id));
	    $template->pageData['righttext'] = display(acjSubmission::retrieve_acjSubmission($takeComp->right_id));
	    $template->pageData['hiddenfields'] = "<input type='hidden' name='cmpid' value='$takeComp->id'/>";
	    $template->pageData['hiddenfields'] .= "<input type='hidden' name='avoid' value='".implode(',',$avoid)."'/>";
	    $template->pageData['extra'] = "<p><a style='background-color: white;' href='index.php?cancel={$takeComp->id}'>I've done enough for now, please release my allocation.</a></p>";
	    return $template->render();
	}
	//return "List of work to be reviewed or input form to go here <pre>".print_r($takeComp,1).'</pre>';
}

function infoForLearnersPage($activity, $userinfo)
{
    global $DATAFOLDER;
    //$out = "Open: ".strftime('%d-%b-%Y %H:%M',$activity->editopen).'<br/>';
    //$out .= "Close: ".strftime('%d-%b-%Y %H:%M',$activity->editclosed).'<br/>';
    //$out .= "time: ".strftime('%d-%b-%Y %H:%M',time()).'<br/>';
	$output = "<h3>Peer review activity '{$userinfo->params['resource_link_title']}'.</h3>";
    $out = '';

    $ctime = time();
    if($ctime < $activity->editopen)
    {
    	$out .= "<b>This activity will start at ".strftime('%H:%M on %d-%b-%Y',$activity->editopen)."</b><br/>";
    	$out .= "<b>It will remain open until ".strftime('%H:%M on %d-%b-%Y',$activity->editclosed)."</b>";
        $output .=  formatIsland("Status", $out);
    }
    elseif(($ctime >= $activity->editclosed)&&($ctime < $activity->commentopen))
    {
    	$out .= "<b>This activity is no longer open for submissions.</b><br/>";
    	$out .= "<b>The reviewing process will start at ".strftime('%H:%M on %d-%b-%Y',$activity->commentopen).",</b><br/>";
    	$out .= "<b>and will run until ".strftime('%H:%M on %d-%b-%Y',$activity->commentclosed)."</b>";
        $output .=  formatIsland("Status", $out);
    }
    elseif($ctime >= $activity->commentclosed)
    {
    	$output .= "<p>The reviewing process has now ended.</p>";
        $submission = acjSubmission::retrieve_by_activity_and_owner_ids($activity->id, $userinfo->user->id);
        if($submission == false)
        {
            $out .= "<p>You did not submit to this reviewing process.</p>";
        }
        else
        {
            $count = $activity->get_submissions_count();
            $quartile = intval(4*$submission->rank/$count);
            switch($quartile)
            {
            	case 0:
		            $out .= "<p>Your submission was ranked in the top quartile. Well done!</p>";
                    break;
            	case 1:
		            $out .= "<p>Your submission was ranked in the second top quartile. Well done.</p>";
                    break;
            	case 2:
		            $out .= "<p>Your submission was ranked in the third (second lowest) quartile.</p>";
                    break;
            	case 3:
		            $out .= "<p>Your submission was ranked in the fourth (lowest) quartile.</p>";
                    break;
            }
        }

        $totalComp = $activity->get_comparisons_count();
        $learnerCount = $activity->get_learners_count();
        $av = intval(($totalComp/$learnerCount)+0.5);
        $userComp = $doneCompCount = acjComparison::count('madeBy_id', $u->id);
        $out .= "<p>You performed $userComp out of $totalComp comparisons. (Average number was {$av}.)</p>";
        $output .= formatIsland("Your result.", $out);
        //# Commentary and sample solution
        if(file_exists("$DATAFOLDER/{$activity->id}/solution.html"))
            $output .= formatIsland("Commentary and solution", file_get_contents("$DATAFOLDER/{$activity->id}/solution.html"));
        //# Survey request and ethics info
        if(file_exists("$DATAFOLDER/{$activity->id}/survey.html"))
            $output .= formatIsland("Survey", file_get_contents("$DATAFOLDER/{$activity->id}/survey.html"));
    }


	return $output;
}

function formatIsland($header, $content)
{
    $output .= '<div class="island"><div class="island-header">';
    $output .= "<h3>{$header}</h3></div><div class=\"island-body\"><p>{$content}</p></div></div>";
    return $output;
}


function overviewPage($activity, $currentPhase, $userinfo)
{
	global $markingoptions;
	$output = "<h3>Peer review activity '{$userinfo->params['resource_link_title']}' in {$userinfo->params['context_label']} : {$userinfo->params['context_title']}.</h3>";
	$output .= '<div class="island"><div class="island-header">';
    $output .= "<h3>Question/activity description</h3></div><div class=\"island-body\"><p>{$activity->question}</p></div></div>";
    $output .= '</div><div class="island"><div class="island-header"><h3>Activity Settings</h3></div><div class="island-body">';
    
    if(($activity->submissionsource == allteacher)&&(($currentPhase == PHASE_NOTSTARTED)||($currentPhase == PHASE_SUBMITTING)))
    {
        $output .= teacherUploadsSection($activity);
    }
    
    $button = "";
    if($currentPhase == PHASE_NOTSTARTED)
        $button = " <a class=\"btn btn-xs btn-success\" href='index.php?phase=".PHASE_SUBMITTING."'>Open submitting now</a>";
    if($currentPhase == PHASE_WAITING)
        $button = " <a class=\"btn btn-xs btn-info\" href='index.php?phase=".PHASE_SUBMITTING."'>Reopen submitting.</a>";
    
    
    $output .= statusRow('Submissions Open:', strftime('%d-%b-%Y %H:%M',$activity->editopen), $button);
    
    $button = "";
    if($currentPhase == PHASE_SUBMITTING)
        $button = " <a class=\"btn btn-xs btn-danger\" href='index.php?phase=".PHASE_WAITING."'>Close submitting now</a>";
    
      $output .= statusRow('Submissions Close:', strftime('%d-%b-%Y %H:%M',$activity->editclosed), $button);
    
    
    $button = "";
    
    if($currentPhase == PHASE_WAITING)
        $button .= " <a class=\"btn btn-xs btn-success\" href='index.php?phase=".PHASE_COMPARING."'>Open ACJ now</a>";
    if($currentPhase == PHASE_COMPARING)
        $button .= " <a class=\"btn btn-xs btn-danger\" href='index.php?phase=".PHASE_FINISHED."'>Close ACJ now</a>";
        
    $output .= statusRow('Reviews Open:', strftime('%d-%b-%Y %H:%M',$activity->commentopen), $button);
    
    $button = "";
    
    if($currentPhase == PHASE_FINISHED)
        $button .= " <a class=\"btn btn-xs btn-success\" href='index.php?phase=".PHASE_COMPARING."'>Reopen ACJ</a>";
        
    $output .= statusRow('Reviews Close:', strftime('%d-%b-%Y %H:%M',$activity->commentclosed), $button);
   
    
    $output .= '<br/>';
    $output .= '<p><a href="index.php?editactivity=1">Edit settings</a></p>';
    $output .= '</div></div>';
    
    $output .= '<div class="island"><div class="island-header"><h3>Statistics</h3>';
    $output .= '</div><div class="island-body">';
	$dueComps = $activity->get_incomplete_comparisons($activity->round);
	$output .= statusRow('ACJ Session Number:', $activity->id);
	$output .= statusRow('Number of Submissions:', $activity->get_submissions_count());
	$output .= statusRow('Comparisons:', sizeof($dueComps).' comparisons until Round '.$activity->round.' is complete');
    $output .= '</div></div>';
    //SELECT lp_ltiUser.*,  lp_acjSubmission.id, lp_acjSubmission.uploaded FROM lp_ltiUser LEFT JOIN lp_acjSubmission ON lp_ltiUser.id=lp_acjSubmission.owner_id WHERE lp_ltiUser.owner_id='1';

    $output .= '</div></div>';

    $output .= '<div class="island"><div class="island-header">';
    $output .= '<h3>Learners</h3>';
    $output .= '</div><div class="island-body">';
    $userList = $activity->getUsersWithSubInfo();
    $subUsers = array();

    foreach($userList as $u)
    {
        if((strpos($u->ltiRoles, 'Learner')!==false)||($u->submissionUploaded > 0))
        {
            //$output .= '<pre>'.print_r($u,true).'</pre>';
	        if(strlen($u->familyName))
		    	$userString = "{$u->familyName}, {$u->givenName} ({$u->userID}): ";
	        elseif(strlen($u->email))
		    	$userString = "{$u->email} ({$u->userID}): ";
	        else
		    	$userString = "{$u->userID}: ";
            $output .= $userString;
	        if($u->submissionUploaded > 0)
            {
		    	$output .= "Submitted ".strftime('%a %d-%b-%Y %H:%M', $u->submissionUploaded).' ';
                $subUsers[$u->SubmissionId] = $userString;
            }
	        else
		    	$output .= "No submission ";
	        $doneCompCount = acjComparison::count('madeBy_id', $u->id);
	        $output .= " ($doneCompCount comparisons completed)<br/>";
        }
    }
    $output .= '</div></div>';

    $output .= '<div class="island"><div class="island-header">';
    $output .= '<h3>Ranking</h3>';
    $output .= '</div><div class="island-body">';
    $subs = $activity->get_submissionsWithKey();
    usort($subs, 'byscore');
    $output .= '<ol>';
    foreach($subs as $s)
    {
        //$output .= '<pre>'.print_r($s,true).'</pre>';
        if((isset($_REQUEST['display']))&&($_REQUEST['display']==$s->code))
        {
            $output .= "<li>{$s->code} <br/>";
            $output .= display($s);
            $output .= "</li>";
        }
        else
        {
            $output .= "<li>";
            if(isset($subUsers[$s->id]))
                $output .= "{$subUsers[$s->id]} ";
            $output .= "{$s->code} <a href='index.php?display={$s->code}'>Display</a> (Current 'score': {$s->latestScore})</li>";
        }
    }
    $output .= '</ol>';
    $output .= '</div></div>';
    $output .= '</div>';
    //echo '<pre>'.print_r($subs,1).'</pre>';

	return $output;
}

function byscore($a, $b)
{
    if ($a->latestScore == $b->latestScore) {
        return 0;
    }
    return ($a->latestScore < $b->latestScore) ? 1 : -1;
}

function teacherUploadsSection($activity)
{
    global $DATAFOLDER;
// Process uploads
	if((isset($_FILES['filesToUpload']))&&(strlen($_FILES['filesToUpload']['tmp_name'][0])))
	{
	    for($n=0; $n < sizeof($_FILES['filesToUpload']['tmp_name']); $n++)
	    {
	        $tmp_name = $_FILES['filesToUpload']['tmp_name'][$n];
	        $raw = file_get_contents($tmp_name, FILE_BINARY);
	        $tail = strtolower(substr($_FILES['filesToUpload']['name'][$n], strrpos($_FILES['filesToUpload']['name'][$n], '.')));
	        $hash = sha1($raw);

	       	$submission = new acjSubmission();
	        $submission->activity_id = $activity->id;
	        $submission->owner_id = 0;
	        $submission->code = $hash;
	        $submission->fromteacher = true;
	        $submission->latestScore = rand(0,100); // randomizes the order

	   	    $savepath = "/{$submission->activity_id}";
	        if(!file_exists($DATAFOLDER.$savepath))
	        	mkdir($DATAFOLDER.$savepath);
	        $savepath .= "/{$hash}{$tail}";
	        if(!file_exists($DATAFOLDER.$savepath))
	        {
		        move_uploaded_file($tmp_name, $DATAFOLDER.$savepath);
		        $submission->submissiontype = $activity->submissiontype;
		        $submission->value = $savepath;
		        $submission->insert();
	        }
	    }
	}

    $subs = $activity->get_submissions();
    foreach($subs as $s)
    {
	    if(($s->value == '')||(!file_exists($DATAFOLDER.$s->value)))
            $s->delete();
    }

// Form for further uploads
	$out =  "<form enctype='multipart/form-data' action='#' method='POST'>";
	$out .= 'Select file(s) to upload: <input type="file" name="filesToUpload[]" multiple="multiple" id="fileToUpload" /><br/>';
 	$out .= "<input type='submit' value='Upload File(s)' />";
	$out .= "</form>";
    return $out;
}

function configActivityPage(&$activity, $userinfo)
{
    global $DATAFOLDER;
	$output = "<h3>Configure peer review activity '{$userinfo->params['resource_link_title']}' in {$userinfo->params['context_label']} : {$userinfo->params['context_title']}.</h3>";
    if(file_exists("info.txt"))
    {
         $output .= '<div>'.file_get_contents("info.txt").'</div>';
    }
    $activityForm = new setupActivity();
    if($activity === false)
        $activity = new lti_acj_resource();
	switch($activityForm->getStatus())
	{
	case FORM_NOTSUBMITTED:
	    $activityForm->setData($activity);
	    $output .= $activityForm->getHtml();
	    break;
	case FORM_SUBMITTED_INVALID:
	    $output .= $activityForm->getHtml();
	    break;
	case FORM_SUBMITTED_VALID:
	    $activityForm->getData($activity);
        //Fill in the bits not in the form
        $activity->keyHash = md5($userinfo->getResourceKey());
       	$hash = md5($userinfo->params["oauth_consumer_key"]);
        $client = lti_consumer::retrieve_lti_consumer_matching('keyHash',$hash);
        $activity->client_id = $client[0]->id;
        $activity->resource_link_id = $userinfo->params['resource_link_id'];
        $activity->link_title = $userinfo->params['resource_link_title'];
        $activity->context_title = $userinfo->params['context_title'];
        $activity->context_label = $userinfo->params['context_label'];
        $activity->updated = time();
	    if($activity->id > 0)
        {
	        $activity->update();
        }
	    else
        {
 	        $activity->id = $activity->insert();
            mkdir($DATAFOLDER.'/'.$activity->id);
        }
        $output = false;
	    break;
	case FORM_CANCELED:
	    break;
    }
    return $output;
}

function getPhase($activity)
{
    if($activity == false)
        return PHASE_NOTSETUP;
    $tm = time();
    if($activity->editopen > $tm)
        return PHASE_NOTSTARTED;
    if(($activity->editopen <= $tm)&&($activity->editclosed >= $tm))
        return PHASE_SUBMITTING;
    if(($activity->editclosed > $tm)&&($activity->commentopen > $tm))
        return PHASE_WAITING;
    if(($activity->commentopen <= $tm)&&($activity->commentclosed >= $tm))
        return PHASE_COMPARING;
    return PHASE_FINISHED;
}

function checkLTISession()
{
    $secretManager = new secretManager();
    session_start();
	if((isset($_REQUEST['lti_message_type']))||(!isset($_SESSION['ltisession'])))
    {
	    session_destroy();
        session_start();
        
    	$_SESSION['ltisession'] = ltiSession::Create($secretManager, $_POST);
    }
    
    return $_SESSION['ltisession'];
}

function getRequestURL()
{
	$pageURL = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
	if (($_SERVER["SERVER_PORT"] != "80") && ($_SERVER["SERVER_PORT"] != "443"))
	{
	    $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	}
	else
	{
	    $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	return $pageURL;
}

function doExtraValidationAndSave($activity, &$submission, $submissiontype, $data)
{
    global $DATAFOLDER;

    $msgs = array();
	switch($submissiontype)
	{
		case 'url':
            break;
		case 'youtube':
            if(preg_match('%(v=|\/v\/|\A\s+|\.be\/|embed\/|.*?=)([^\&\?\/=\s]{6,14})%', $data->url, $matches))
            {
                $submission->submissiontype = 'youtube';
                $submission->value = $matches[2];
            }
            else
            {
            	$msgs['url'] = "Please insert a youtube URL containing a Video ID or a youtube Video ID.";
            }
			break;
		case 'pdf':
        	$savepath = "/{$submission->activity_id}";
            if(!file_exists($DATAFOLDER.$savepath))
            	mkdir($DATAFOLDER.$savepath);
            $savepath .= "/{$submission->owner_id}.pdf";
            if((strlen($submission->value))&&(file_exists($DATAFOLDER.$submission->value)))
                unlink($DATAFOLDER . $submission->value);
            move_uploaded_file($data->upload['tmp_name'],$DATAFOLDER.$savepath);
            $submission->submissiontype = 'pdf';
            $submission->value = $savepath;
			break;
		case 'image':
            $imginfo = getimagesize($data->upload['tmp_name']);
            if($imginfo == false)
            {
            	$msgs['upload'] = "Upload was not an image file. Please upload a valid JPEG, PNG or GIF image file.";
            }
            else
            {
            	$savepath = "/{$submission->activity_id}";
                if(!file_exists($savepath))
                	mkdir($savepath);
                $savepath .= "/{$submission->owner_id}.".substr($imginfo['mime'], strpos($imginfo['mime'],'/')+1);
                if((strlen($submission->value))&&(file_exists($DATAFOLDER . $submission->value)))
                    unlink($DATAFOLDER . $submission->value);
                move_uploaded_file($data->upload['tmp_name'],$DATAFOLDER.$savepath);
                $submission->submissiontype = 'image';
                $submission->value = $savepath;
                $submission->mime = $imginfo['mime'];
                $submission->width = $imginfo[0];
                $submission->height = $imginfo[1];
            }
            //echo '<pre>'.print_r($imginfo, 1).'</pre>';
			break;
		case 'html':
                $submission->submissiontype = 'html';
                $submission->value = $data->text;
    		break;
		case 'code':
            if(strlen($data->text))
            {
            	$savepath = "/{$submission->activity_id}";
                if(!file_exists($savepath))
                	mkdir($savepath);
                $savepath .= "/{$submission->owner_id}";
                if((strlen($submission->value))&&(file_exists($DATAFOLDER . $submission->value)))
                    unlink($DATAFOLDER . $submission->value);
                $language = $activity->submissionsubtype == '' ? 'text' : $activity->submissionsubtype;
                $geshi = new GeSHi($data->text, $language);
                file_put_contents($DATAFOLDER.$savepath.'.html', $geshi->parse_code());
                file_put_contents($DATAFOLDER.$savepath.'.'.$activity->submissionsubtype, $data->text);
                $submission->submissiontype = 'code';
                $submission->value = $savepath;
            }
			break;
	}
    if(sizeof($msgs) == 0)
    {
        $submission->uploaded = time();
        if($submission->id==0)
        	$submission->insert();
        else
            $submission->update();
    }
    return $msgs;
}

function displayUserInput($submission)
{
/*    $output = "";
	switch($submission->submissiontype)
	{
		case 'url':
            break;
		case 'youtube':
			break;
		case 'pdf':
			break;
		case 'image':
            $output = "<img src='showimage.php?code={$submission->code}'/>";
			break;
		case 'html':
    		break;
		case 'code':
			break;
	}                 */
    //$output .= '<pre>'.print_r($submission,1).'</pre>';
    $output = display($submission) . "<p><a href='index.php?edit=1'>Edit/Replace submission.</a></p>";
    return $output;
}

function prepareNewAcjRound(&$session)
{
    //Need to create
    $engine = new acj2();
    $subs = $session->get_submissionsWithKey();
    foreach($subs as $sub)
    {
        $acjpaper = new submission($sub->id);
        $acjpaper->_latestScore = $sub->latestScore;
        $acjpaper->rank = $sub->rank;
        $lcmps = acjComparison::retrieve_acjComparison_matching('left_id', $sub->id);
        if($lcmps)
        {
	        foreach($lcmps as $cmp)
	        {
	            if($cmp->done > 0)
	                $acjpaper->addComparison($cmp->right_id, $cmp->leftWon, $cmp->round);
	        }
        }
        $rcmps = acjComparison::retrieve_acjComparison_matching('right_id', $sub->id);
        if($rcmps)
        {
	        foreach($rcmps as $cmp)
	        {
	            if($cmp->done > 0)
	                $acjpaper->addComparison($cmp->left_id, $cmp->rightWon, $cmp->round);
	        }
        }
        $engine->papers[$sub->id] = $acjpaper;
    }
    if($session->round >= 0)
    {
        $engine->recalc1($session->round);
        foreach($engine->papers as $p)
        {
            $subs[$p->id]->latestScore = $p->_latestScore;
            $subs[$p->id]->rank = $p->rank;
            $subs[$p->id]->update();
        }
    }
    $pairings = $engine->getPairingsByRank();
    $session->round++;
    $session->update();
    //echo '<pre>'.print_r($pairings, 1).'</pre>';
    foreach($pairings as $p)
    {
        $cmpr = new acjComparison();
        $cmpr->activity_id = $session->id;
        $cmpr->left_id = $p[0];
        $cmpr->right_id = $p[1];
        $cmpr->allocated = 0;
        $cmpr->done = 0;
        $cmpr->round = $session->round;
        $cmpr->insert();
    }
}

function getAComparison($session, $round, $uname, $avoid)
{
    $dueComps = $session->get_users_incomplete_comparisons($round, $uname);
	if(sizeof($dueComps)>0)
	{
	    $takeComp = $dueComps[0];
	    $takeComp->allocated = time();
	    $takeComp->update();
	}
    else
    {
	    $takeComp = false;
		$dueComps = $session->get_unalocated_comparisons($round);
        if(sizeof($dueComps)==0)
        {
            clearOverdueComps($session);
		    $dueComps = $session->get_unalocated_comparisons($round);
        }
        $n=0;
		while(($takeComp == false)&&($n<sizeof($dueComps)))
		{
            if(!in_array($dueComps[$n]->id, $avoid))
            {
			    $takeComp = $dueComps[$n];
			    $takeComp->allocated = time();
			    $takeComp->userID = $uname;
			    $takeComp->update();
            }
            $n++;
		}
    }
    return $takeComp;
}

function clearOverdueComps($session, $timelimit=600)
{
    $dueComps = $session->get_incomplete_comparisons($session->round);
    $pretime = time()-$timelimit;
    foreach($dueComps as $d)
    {
        if(($d->allocated < $pretime)&&($d->allocated > 0))
        {
		    $d->allocated = 0;
			$d->userID = '';
            $d->update();
        }
    }
}

function checkFormInput($activty, $userinfo)
{
	if(isset($_REQUEST['cmpid']))
	{
	    $cmp = acjComparison::retrieve_acjComparison($_REQUEST['cmpid']);
	    if(isset($_REQUEST['left']))
	    {
	        $cmp->leftWon = true;
	        $cmp->done = time();
	        $cmp->madeBy_id = $userinfo->user->id;  // just in case
	    }
	    elseif(isset($_REQUEST['right']))
	    {
	        $cmp->rightWon = true;
	        $cmp->done = time();
	        $cmp->madeBy_id = $userinfo->user->id;  // just in case
	    }
	    else
	    {
	        $cmp->allocated = 0;
	        $cmp->userID = '';
	        $avoid[] = $cmp->id;
	    }
	    $cmp->update();
	}
	elseif(isset($_REQUEST['cancel']))
	{
	    $cmp = acjComparison::retrieve_acjComparison($_REQUEST['cancel']);
	    $cmp->allocated = 0;
	    $cmp->userID = '';
	    $cmp->update();
        if(isset($userinfo->launch_presentation_return_url))
	        header("Location: {$userinfo->launch_presentation_return_url}");
	    echo "Please close this browser window.";
	    exit();
	}
}

function quickUpdatePhase($phase, &$activity)
{
    $defaultPhaseLen = 24*3600;
    switch($phase)
    {
        case PHASE_SUBMITTING:
            if($activity->editopen > time())
                $activity->editopen = time();
            if($activity->editclosed < time()+$defaultPhaseLen)
                $activity->editclosed = time()+$defaultPhaseLen;
            if($activity->commentopen <= $activity->editclosed)
                $activity->commentopen = $activity->editclosed+1;
            if($activity->commentclosed <= $activity->commentopen+$defaultPhaseLen)
                $activity->commentclosed = $activity->commentopen+$defaultPhaseLen;
            break;
        case PHASE_WAITING:
            if($activity->editclosed > time())
                $activity->editclosed = time()-1;
            if($activity->editopen > $activity->editclosed)
                $activity->editopen = $activity->editclosed-1;
            if($activity->commentopen <= $activity->editclosed+$defaultPhaseLen)
                $activity->commentopen = $activity->editclosed+$defaultPhaseLen;
            if($activity->commentclosed <= $activity->commentopen+$defaultPhaseLen)
                $activity->commentclosed = $activity->commentopen+$defaultPhaseLen;
            break;
        case PHASE_COMPARING:
            if($activity->editclosed > time())
                $activity->editclosed = time()-1;
            if($activity->editopen > $activity->editclosed)
                $activity->editopen = $activity->editclosed-1;
            if($activity->commentopen > time())
                $activity->commentopen = time();
            if($activity->commentclosed <= time()+$defaultPhaseLen)
                $activity->commentclosed = time()+$defaultPhaseLen;
            if($activity->commentclosed <= $activity->commentopen+$defaultPhaseLen)
                $activity->commentclosed = $activity->commentopen+$defaultPhaseLen;
            break;
        case PHASE_FINISHED:
            if($activity->editclosed > time())
                $activity->editclosed = time()-1;
            if($activity->editopen > $activity->editclosed)
                $activity->editopen = $activity->editclosed-1;
            if($activity->commentclosed >= time())
                $activity->commentclosed = time()-1;
            if($activity->commentopen > $activity->commentclosed)
                $activity->commentopen = $activity->commentclosed-1;
            break;
    }
    $activity->update();
}

function display($submission)
{
    global $DATAFOLDERURL, $DATAFOLDER;
    $type = $submission->submissiontype;
    switch($type)
    {
        case 'image':
            return "<img style='display: block; margin-left: auto; margin-right: auto; width: 95%;' src='showimage.php?code={$submission->code}'/>";
            break;
        case 'code':
            return file_get_contents($DATAFOLDER.$submission->value.'.html');
            break;
        case 'html':
            return $submission->value;
            break;
         case 'youtube':
             return '<iframe width="560" height="315" src="https://www.youtube.com/embed/' . $submission->value . '" frameborder="0" allowfullscreen></iframe>';
             break;
         case 'pdf':
               //return '<object data="' . $DATAFOLDERURL.$submission->value . '" type="application/pdf" width="100%" height="100%"></object>';
               // return '<iframe width="560" height="315" src="pdf2html/viewer.html?file='.  urlencode($DATAFOLDERURL.$submission->value).'" frameborder="0"></iframe>';
                return '<object data="ViewerJS/#' . $DATAFOLDERURL.$submission->value . '" width="100%" height="600px"></object>';
                return '<iframe width="560" height="315" src="ViewerJS/#'.  $DATAFOLDERURL.$submission->value.'" frameborder="0"></iframe>';
             break;
         default:
            return "Unknown submission type $type. <pre>".print_r($submission,1).'</pre>';//$file;//file_get_contents($file);
            break;
    }
}

function statusRow($description, $value, $button="") {
    $output  = '<div class="status-row"><div class="form-group">';
    $output .= '<span class="label col-xs-4">'.$description.'</span>';
    $output .= '<div class="col-sm-8">';
    $output .= '<span class="value">'.$value.'</span>';
    $output .= $button;
    $output .= '</div>';
    $output .= '</div></div>';
    
    return $output;
}

?>
    </div>
</body>

</html>
