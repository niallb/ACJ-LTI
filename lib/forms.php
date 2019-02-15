<?php
require_once('corelib/form_lib2.php');

$markingoptions = array('none'=>'Comment only', '5pt_agree'=>'5 point Likert scale - strongly agree to strongly disagree', '3pt_agree'=>'3 point Likert scale - agree to disagree', '5_marks'=>'Award up to 5 marks', '4_marks'=>'Award up to 4 marks', '3_marks'=>'Award up to 3 marks', '2_marks'=>'Award up to 2 marks', 'yesno'=>'Yes or No');


class editLMS extends nbform
{
	var $form_magic_id = '4339c1f3261c12a8999ce84188a16c3a';
	var $id; //hidden
	var $consumer_key; //string
	var $secret; //string
	var $validateMessages;

	function __construct($readform=true)
	{
		parent::__construct();
		$this->validateMessages = array();
        $this->secret = md5(uniqid()); // Create a strong default secret
		if($readform)
		{
			$this->readAndValidate();
		}
	}

	function setData($data)
	{
		$this->id = $data->id;
		$this->consumer_key = $data->consumer_key;
		$this->secret = $data->secret;
	}

	function getData(&$data)
	{
		$data->id = $this->id;
		$data->consumer_key = $this->consumer_key;
		$data->secret = $this->secret;
		return $data;
	}

	function readAndValidate()
	{
		$isCanceled=false;
		if((isset($_REQUEST['editLMS_code']))&&($_REQUEST['editLMS_code'] == $this->form_magic_id))
		{
			$this->id = $_REQUEST['id'];
			$this->consumer_key = stripslashes($_REQUEST['consumer_key']);
			$this->secret = stripslashes($_REQUEST['secret']);
			if('Cancel' == $_REQUEST['submit'])
				$isCanceled = true;
			$isValid = $this->validate();
			if($isCanceled)
				$this->formStatus = FORM_CANCELED;
			elseif($isValid)
				$this->formStatus = FORM_SUBMITTED_VALID;
			else
				$this->formStatus = FORM_SUBMITTED_INVALID;
		}
		else
			$this->formStatus = FORM_NOTSUBMITTED;
	}

	function validate()
	{
		$this->validateMessages = array();
		// Put custom code to validate $this->id here (to stop hackers using this as a way in.)
		if(strlen($this->consumer_key)>40)
		{
		    $this->consumer_key = substr($this->consumer_key,0,40);
		    $this->validateMessages['consumer_key'] = "This field was too long and has been truncated.";
		}
		if(strlen($this->consumer_key)<5)
		{
		    $this->consumer_key = substr($this->consumer_key,0,40);
		    $this->validateMessages['consumer_key'] = "Consumer keys must have at least 5 characters in this system.";
		}
		// Put custom code to validate $this->consumer_key here. Error message in $this->validateMessages['consumer_key']
		if(strlen($this->secret)>40)
		{
		    $this->secret = substr($this->secret,0,40);
		    $this->validateMessages['secret'] = "This field was too long and has been truncated.";
		}
		if(strlen($this->secret)<5)
		{
		    $this->secret = substr($this->secret,0,40);
		    $this->validateMessages['secret'] = "Consumer secrets must have at least 5 characters in this system.";
		}
		// Put custom code to validate $this->secret here. Error message in $this->validateMessages['secret']
		if(sizeof($this->validateMessages)==0)
			return true;
		else
			return false;
	}

	function getHtml()
	{
		$out = '';
		$out .= $this->formStart();
		$out .= $this->hiddenInput('editLMS_code', $this->form_magic_id);
		$out .= $this->hiddenInput('id', $this->id);
		$out .= $this->textInput('Resource Key', 'consumer_key', $this->consumer_key, $this->validateMessages, 40);
		$out .= $this->textInput('Shared Secret', 'secret', $this->secret, $this->validateMessages, 40);
		$out .= $this->submitInput('submit', 'Save', 'Cancel');
		$out .= $this->formEnd(false);
		return $out;
	}

	function post_it()
	{
	    $http = new Http();
	    $http->useCurl(false);
	    $formdata=array('thanks_url'=>'none', 'mymode'=>'webform1.0', 'datafile'=>'editLMS', 'coderef'=>'nsb2x');
	    $formdata['id'] = $this->id;
	    $formdata['consumer_key'] = $this->consumer_key;
	    $formdata['secret'] = $this->secret;

	    $http->execute('http://culrain.cent.gla.ac.uk/cgi-bin/qh/qhc','','POST',$formdata);
	    return ($http->error) ? $http->error : $http->result;
	}

}

class setupActivity extends nbform
{
	var $form_magic_id = 'c5067b7a0a0e7c495a9544875910e8cf';
	var $id; //hidden
	//var $editactivity; //hidden
	var $question; //memo
	var $submissionsource; //select
	var $editopenText; //string
	var $editclosedText; //string
	var $submissiontype; //select
	var $submissionsubtype; //select
	var $commentopenText; //string
	var $commentclosedText; //string
	var $rubric; //memo
	var $rankedby; //select
	var $sharecomments; //boolean
	var $scoringstrategy; //select
	var $returnscore; //select
	var $validateMessages;

	function __construct($readform=true)
	{
		parent::__construct();
		$this->validateMessages = array();
		if($readform)
		{
			$this->readAndValidate();
		}
	}

	function setData($data)
	{
		$this->id = $data->id;
		//$this->editactivity = $data->editactivity;
		$this->question = $data->question;
		$this->submissionsource = $data->submissionsource;
		$this->editopenText = strftime('%d-%b-%Y %H:%M',$data->editopen);
		$this->editclosedText = strftime('%d-%b-%Y %H:%M',$data->editclosed);
		$this->submissiontype = $data->submissiontype;
		$this->submissionsubtype = $data->submissionsubtype;
		$this->commentopenText = strftime('%d-%b-%Y %H:%M',$data->commentopen);
		$this->commentclosedText = strftime('%d-%b-%Y %H:%M',$data->commentclosed);
		$this->rubric = $data->rubric;
		$this->rankedby = $data->rankedby;
		$this->sharecomments = $data->sharecomments;
		$this->scoringstrategy = $data->scoringstrategy;
		$this->returnscore = $data->returnscore;
	}

	function getData(&$data)
	{
		$data->id = $this->id;
		//$data->editactivity = $this->editactivity;
		$data->question = $this->question;
		$data->submissionsource = $this->submissionsource;
		$data->editopen = strtotime($this->editopenText);
		$data->editclosed = strtotime($this->editclosedText);
		$data->submissiontype = $this->submissiontype;
		$data->submissionsubtype = $this->submissionsubtype;
		$data->commentopen = strtotime($this->commentopenText);
		$data->commentclosed = strtotime($this->commentclosedText);
		$data->rubric = $this->rubric;
		$data->rankedby = $this->rankedby;
		$data->sharecomments = $this->sharecomments;
		$data->scoringstrategy = $this->scoringstrategy;
		$data->returnscore = $this->returnscore;
		return $data;
	}

	function readAndValidate()
	{
		$isCanceled=false;
		if((isset($_REQUEST['setupActivity_code']))&&($_REQUEST['setupActivity_code'] == $this->form_magic_id))
		{
			$this->id = $_REQUEST['id'];
			$this->editactivity = $_REQUEST['editactivity'];
			$this->question = stripslashes($_REQUEST['question']);
			$this->submissionsource = $_REQUEST['submissionsource'];
			$this->editopenText = stripslashes($_REQUEST['editopenText']);
			$this->editclosedText = stripslashes($_REQUEST['editclosedText']);
			$this->submissiontype = $_REQUEST['submissiontype'];
			$this->submissionsubtype = $_REQUEST['submissionsubtype'];
			$this->commentopenText = stripslashes($_REQUEST['commentopenText']);
			$this->commentclosedText = stripslashes($_REQUEST['commentclosedText']);
			$this->rubric = stripslashes($_REQUEST['rubric']);
			$this->rankedby = $_REQUEST['rankedby'];
			$this->sharecomments = (isset($_REQUEST['sharecomments'])&&($_REQUEST['sharecomments']==1)) ? true : false;
			$this->scoringstrategy = $_REQUEST['scoringstrategy'];
			$this->returnscore = $_REQUEST['returnscore'];
			$isValid = $this->validate();
			if($isCanceled)
				$this->formStatus = FORM_CANCELED;
			elseif($isValid)
				$this->formStatus = FORM_SUBMITTED_VALID;
			else
				$this->formStatus = FORM_SUBMITTED_INVALID;
		}
		else
			$this->formStatus = FORM_NOTSUBMITTED;
	}

	function validate()
	{
		$this->validateMessages = array();
		// Put custom code to validate $this->id here (to stop hackers using this as a way in.)
		// Put custom code to validate $this->editactivity here (to stop hackers using this as a way in.)
		// Put custom code to validate $this->question here. Put error message in $this->validateMessages['question']
		// Put custom code to check $this->submissionsource here.
		if(strlen(trim($this->question))==0)
		{
		    $this->validateMessages['question'] = "You must include a question.";
		}
        $editopen = strtotime($this->editopenText);
        $this->editopenText = strftime('%d-%b-%Y %H:%M',$editopen);

        $editclosed = strtotime($this->editclosedText);
        $this->editclosedText = strftime('%d-%b-%Y %H:%M',$editclosed);

        $commentopen = strtotime($this->commentopenText);
        $this->commentopenText = strftime('%d-%b-%Y %H:%M',$commentopen);

        $commentclosed = strtotime($this->commentclosedText);
        $this->commentclosedText = strftime('%d-%b-%Y %H:%M',$commentclosed);
		// Put custom code to validate $this->editopenText here. Error message in $this->validateMessages['editopenText']
		if($editclosed < $editopen+3600)
		{
		    $this->validateMessages['editclosedText'] = "Close must be at least one hour after open, and at least one hour from now.";
		}
		// Put custom code to validate $this->editclosedText here. Error message in $this->validateMessages['editclosedText']
		if($commentopen<$editclosed)
		{
		    $this->validateMessages['commentopenText'] = "Reviewing must start after the activity is closed.";
		}
		// Put custom code to validate $this->commentopenText here. Error message in $this->validateMessages['commentopenText']
		if($commentclosed < $commentopen+3600)
		{
		    $this->validateMessages['commentclosedText'] = "Reviewing must last at least an hour.";
		}
		// Put custom code to validate $this->commentclosedText here. Error message in $this->validateMessages['commentclosedText']
		// Put custom code to validate $this->rubric here. Put error message in $this->validateMessages['rubric']
		// Put custom code to check $this->rankedby here.
		// Put custom code to validate $this->sharecomments here. Put error message in $this->validateMessages['sharecomments']
		// Put custom code to check $this->scoringstrategy here.
		// Put custom code to check $this->returnscore here.
		if(sizeof($this->validateMessages)==0)
			return true;
		else
			return false;
	}

	function getHtml()
	{
		$out = '';
		$out .= $this->formStart();
		$out .= $this->hiddenInput('setupActivity_code', $this->form_magic_id);
		$out .= $this->hiddenInput('id', $this->id);
		$out .= $this->hiddenInput('editactivity', 1);
		$out .= $this->textareaInput('Type in instructions or question for this exercise.', 'question', $this->question, $this->validateMessages, 70 , 5);
		$options = array('allstudents'=>'All students must submit work to be compared', 'somestudents'=>'Students submit work, but it is not required', 'allteacher'=>'All submissions are uploaded by teachers');
		$out .= $this->selectListInput('What will be compared?', 'submissionsource', $options, $this->submissionsource, false, $this->validateMessages);
		$out .= $this->textInput('Date/time for opening submission', 'editopenText', $this->editopenText, $this->validateMessages, 40);
		$out .= $this->textInput('Date/time for closing submission', 'editclosedText', $this->editclosedText, $this->validateMessages, 40);
		$options = array('url'=>'Paste in a webpage URL', 'youtube'=>'Paste in a YouTube video URL', 'html'=>'Type their submission into an HTML editor', 'pdf'=>'Upload a pdf file', 'image'=>'Upload an image (gif, jpeg or png)', 'code'=>'Type or paste in source code');
		$out .= $this->selectListInput('What would you like students to do?', 'submissiontype', $options, $this->submissiontype, false, $this->validateMessages);
		$options = array(''=>'N/A', 'cpp'=>'C++', 'csharp'=>'C#', 'css'=>'CSS', 'haskell'=>'Haskell', 'html5'=>'HTML 5', 'java'=>'Java', 'javascript'=>'JavaScript', 'php'=>'PHP', 'sql'=>'SQL');
		$out .= $this->selectListInput('Language (source code only)?', 'submissionsubtype', $options, $this->submissionsubtype, false, $this->validateMessages);
		$out .= $this->textInput('Date/time for opening ACJ ranking and commenting', 'commentopenText', $this->commentopenText, $this->validateMessages, 40);
		$out .= $this->textInput('Date/time for closing ACJ ranking and commenting', 'commentclosedText', $this->commentclosedText, $this->validateMessages, 40);
		$out .= $this->textareaInput('Guidelines for how to judge submissions.', 'rubric', $this->rubric, $this->validateMessages, 70 , 5);
		$options = array('Instructor'=>"Teachers", 'Learner'=>'Students (peers)');
		$out .= $this->selectListInput('Ranking performed by', 'rankedby', $options, $this->rankedby, false, $this->validateMessages);
		$out .= $this->checkboxInput('Allow students to see the comments about their work.', 'sharecomments', $this->sharecomments, $this->validateMessages);
		$options = array('rank'=>"Score based on final rank", 'engagement'=>"Score based on judgement participation", 'mixed'=>'Score based on rank and participation');
		$out .= $this->selectListInput('Scoring strategy', 'scoringstrategy', $options, $this->scoringstrategy, false, $this->validateMessages);
		$options = array('never'=>'Never', 'close'=>'After close', 'teacher'=>'Wait for teacher');
		$out .= $this->selectListInput('Return scores to VLE', 'returnscore', $options, $this->returnscore, false, $this->validateMessages);
		$out .= $this->formEnd();
		return $out;
	}

	function post_it()
	{
	    $http = new Http();
	    $http->useCurl(false);
	    $formdata=array('thanks_url'=>'none', 'mymode'=>'webform1.0', 'datafile'=>'setupActivity', 'coderef'=>'nsb2x');
	    $formdata['id'] = $this->id;
	    $formdata['editactivity'] = $this->editactivity;
	    $formdata['question'] = $this->question;
	    $formdata['submissionsource'] = $this->submissionsource;
	    $formdata['editopenText'] = $this->editopenText;
	    $formdata['editclosedText'] = $this->editclosedText;
	    $formdata['submissiontype'] = $this->submissiontype;
	    $formdata['submissionsubtype'] = $this->submissionsubtype;
	    $formdata['commentopenText'] = $this->commentopenText;
	    $formdata['commentclosedText'] = $this->commentclosedText;
	    $formdata['rubric'] = $this->rubric;
	    $formdata['rankedby'] = $this->rankedby;
	    $formdata['sharecomments'] = $this->sharecomments;
	    $formdata['scoringstrategy'] = $this->scoringstrategy;
	    $formdata['returnscore'] = $this->returnscore;

	    $http->execute('http://culrain.cent.gla.ac.uk/cgi-bin/qh/qhc','','POST',$formdata);
	    return ($http->error) ? $http->error : $http->result;
	}

}

class editRubric extends nbform
{
	var $form_magic_id = '5bef235a9922e1422b9129823f843a3e';
	var $idx; //hidden
	var $question; //string
	var $comment; //boolean
	var $type; //select
	var $editrubrics; //hidden
	var $validateMessages;

	function __construct($readform=true)
	{
		parent::__construct();
		$this->validateMessages = array();
		if($readform)
		{
			$this->readAndValidate();
		}
	}

	function setData($data)
	{
		$this->idx = $data->idx;
		$this->question = $data->question;
		$this->comment = $data->comment;
		$this->type = $data->type;
		$this->editrubrics = $data->editrubrics;
	}

	function getData(&$data)
	{
		$data->idx = $this->idx;
		$data->question = $this->question;
		$data->comment = $this->comment;
		$data->type = $this->type;
		$data->editrubrics = $this->editrubrics;
		return $data;
	}

	function clear()
	{
		$this->idx = '';
		$this->question = '';
		$this->comment = '';
		$this->type = '';
		$this->editrubrics = '';
	}

	function readAndValidate()
	{
		$isCanceled=false;
		if((isset($_REQUEST['editRubric_code']))&&($_REQUEST['editRubric_code'] == $this->form_magic_id))
		{
			$this->idx = $_REQUEST['idx'];
			$this->question = stripslashes($_REQUEST['question']);
			$this->comment = (isset($_REQUEST['comment'])&&($_REQUEST['comment']==1)) ? true : false;
			$this->type = $_REQUEST['type'];
			$this->editrubrics = $_REQUEST['editrubrics'];
			$isValid = $this->validate();
			if($isCanceled)
				$this->formStatus = FORM_CANCELED;
			elseif($isValid)
				$this->formStatus = FORM_SUBMITTED_VALID;
			else
				$this->formStatus = FORM_SUBMITTED_INVALID;
		}
		else
			$this->formStatus = FORM_NOTSUBMITTED;
	}

	function validate()
	{
		$this->validateMessages = array();
		// Put custom code to validate $this->idx here (to stop hackers using this as a way in.)
		if(strlen($this->question)>60)
		{
		    $this->question = substr($this->question,0,60);
		    $this->validateMessages['question'] = "This field was too long and has been truncated.";
		}
		// Put custom code to validate $this->question here. Error message in $this->validateMessages['question']
		// Put custom code to validate $this->comment here. Put error message in $this->validateMessages['comment']
		// Put custom code to check $this->type here.
		// Put custom code to validate $this->editrubrics here (to stop hackers using this as a way in.)
		if(sizeof($this->validateMessages)==0)
			return true;
		else
			return false;
	}

	function getHtml()
	{
    	global $markingoptions;
		$out = '';
		$out .= $this->formStart();
		$out .= $this->hiddenInput('editRubric_code', $this->form_magic_id);
		$out .= $this->hiddenInput('idx', $this->idx);
		$out .= $this->textInput('Criterion text', 'question', $this->question, $this->validateMessages, 60);
		$out .= $this->checkboxInput('Allow a comment', 'comment', $this->comment, $this->validateMessages);
		$options = $markingoptions;//array('none'=>'Comment only', '5pt_agree'=>'5 point Likert scale - strongly agree to strongly disagree', '3pt_agree'=>'3 point Likert scale - agree to disagree', '5_marks'=>'Award up to 5 marks', '4_marks'=>'Award up to 4 marks', '3_marks'=>'Award up to 3 marks', '2_marks'=>'Award up to 2 marks', 'yesno'=>'Yes or No');
		$out .= $this->selectListInput('Type of mark award interface', 'type', $options, $this->type, false, $this->validateMessages);
		$out .= $this->hiddenInput('editrubrics', $this->editrubrics);
		$out .= $this->submitInput('submit', 'Add/Update');
		$out .= $this->formEnd(false);
		return $out;
	}

	function post_it()
	{
	    $http = new Http();
	    $http->useCurl(false);
	    $formdata=array('thanks_url'=>'none', 'mymode'=>'webform1.0', 'datafile'=>'editRubric', 'coderef'=>'nsb2x');
	    $formdata['idx'] = $this->idx;
	    $formdata['question'] = $this->question;
	    $formdata['comment'] = $this->comment;
	    $formdata['type'] = $this->type;
	    $formdata['editrubrics'] = $this->editrubrics;

	    $http->execute('http://culrain.cent.gla.ac.uk/cgi-bin/qh/qhc','','POST',$formdata);
	    return ($http->error) ? $http->error : $http->result;
	}

}

class editpage_form extends nbform
{
	var $form_magic_id = 'f1ff1cc53c174c8fcf94942965133121';
	var $id; //hidden
	var $title; //hidden
	var $url; //string
	var $text; //memo
	var $modified; //hidden
	var $validateMessages;

	function __construct($readform=true)
	{
		parent::__construct();
		$this->validateMessages = array();
		if($readform)
		{
			$this->readAndValidate();
		}
	}

	function setData($data)
	{
		$this->id = $data->id;
		$this->title = $data->title;
		$this->url = $data->url;
		$this->text = $data->text;
		$this->modified = $data->modified;
	}

	function getData(&$data)
	{
        if(!is_object($data))
            $data = new stdClass();
		$data->id = $this->id;
		$data->title = $this->title;
		$data->url = $this->url;
		$data->text = $this->text;
		$data->upload = $this->upload;
		$data->modified = $this->modified;
		return $data;
	}

	function readAndValidate()
	{
		$isCanceled=false;
		if((isset($_REQUEST['editpage_form_code']))&&($_REQUEST['editpage_form_code'] == $this->form_magic_id))
		{
			$this->id = $_REQUEST['id'];
			$this->title = $_REQUEST['title'];
            if(isset($_REQUEST['url']))
			$this->url = stripslashes($_REQUEST['url']);
            else
                $this->url = false;
            if(isset($_REQUEST['text']))
			    $this->text = $_REQUEST['text'];//$this->text = stripslashes($_REQUEST['text']);
            else
                $this->text = false;
            if(isset($_FILES['upload']))
                $this->upload = $_FILES['upload'];
            else
            	$this->upload = false;
			$this->modified = $_REQUEST['modified'];
			if('Cancel' == $_REQUEST['submit'])
				$isCanceled = true;
			$isValid = $this->validate();
			if($isCanceled)
				$this->formStatus = FORM_CANCELED;
			elseif($isValid)
				$this->formStatus = FORM_SUBMITTED_VALID;
			else
				$this->formStatus = FORM_SUBMITTED_INVALID;
		}
		else
			$this->formStatus = FORM_NOTSUBMITTED;
	}

	function validate()
	{
		$this->validateMessages = array();
		// Put custom code to validate $this->id here (to stop hackers using this as a way in.)
		// Put custom code to validate $this->title here (to stop hackers using this as a way in.)
		if(strlen($this->url)>120)
		{
		    $this->url = substr($this->url,0,120);
		    $this->validateMessages['url'] = "This field was too long and has been truncated.";
		}
		// Put custom code to validate $this->url here. Error message in $this->validateMessages['url']
		// Put custom code to validate $this->text here. Put error message in $this->validateMessages['text']
		// Put custom code to validate $this->modified here (to stop hackers using this as a way in.)
		if(sizeof($this->validateMessages)==0)
			return true;
		else
			return false;
	}

	function getHtml($submssiontype, $subtype='text')
	{
		$out = '';
		$out .= $this->formStart();
		$out .= $this->hiddenInput('editpage_form_code', $this->form_magic_id);
		$out .= $this->hiddenInput('id', $this->id);
		$out .= $this->hiddenInput('title', $this->title);
		$out .= $this->hiddenInput('submssiontype', $submssiontype);
		$out .= $this->hiddenInput('edit', 1);
        switch($submssiontype)
        {
        case 'url':
        case 'youtube':
			$out .= $this->textInput('URL', 'url', $this->url, $this->validateMessages, 80);
	        break;
        case 'pdf':
			$out .= $this->fileUploadInput('Upload a PDF file', 'upload', $this->validateMessages);
            break;
        case 'image':
			$out .= $this->fileUploadInput('Upload an image file (JPEG, PNG or GIF)', 'upload', $this->validateMessages);
            break;
        case 'html':
		$out .= $this->textareaInput('Text', 'text', $this->text, $this->validateMessages, 65 , 18);
            $out .= '<script type="text/javascript" src="tinymce/tinymce.min.js"></script><script type="text/javascript">
                    tinymce.init({selector: "textarea"});</script>';
            break;
        case 'code':
            $out .= "<textarea id='text' name='text'>".$this->text."</textarea>";
            $out .= "<div id='editor' class='editor'>".$this->text."</div>";
            $out .= "<script src='ace/ace.js' type='text/javascript' charset='utf-8'></script><script src='ace/ext-language_tools.js'></script>";
            $out .= "<script type='text/javascript' defer='defer'>var editor = ace.edit('editor');";
            $out .= "editor.setTheme('ace/theme/eclipse');
                     editor.getSession().setMode('ace/mode/{$subtype}')
                     var textarea = document.getElementById('text');
                     textarea.style.display = 'none';
						//editor.getSession().setValue(textarea.val());
						editor.getSession().on('change', function(){
						  textarea.value = editor.getSession().getValue();
						});
            ;</script><div>&nbsp;</div>";
		   //	$out .= $this->textareaInput('Text', 'text', $this->text, $this->validateMessages, 65 , 18);
            break;
        }
		$out .= $this->hiddenInput('modified', $this->modified);
		$out .= $this->submitInput('submit', 'Save', 'Cancel');
		$out .= $this->formEnd(false);
		return $out;
	}

	function post_it()
	{
	    $http = new Http();
	    $http->useCurl(false);
	    $formdata=array('thanks_url'=>'none', 'mymode'=>'webform1.0', 'datafile'=>'editpage_form', 'coderef'=>'nsb2x');
	    $formdata['id'] = $this->id;
	    $formdata['title'] = $this->title;
	    $formdata['url'] = $this->url;
	    $formdata['text'] = $this->text;
	    $formdata['modified'] = $this->modified;

	    $http->execute('http://culrain.cent.gla.ac.uk/cgi-bin/qh/qhc','','POST',$formdata);
	    return ($http->error) ? $http->error : $http->result;
	}

}
