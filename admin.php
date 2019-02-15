<?php
include('config.php');
include('lib/checklogin.php');
checkLogin();
include_once('lib/forms.php');
?><html><head><title>LTIPeer administration</title></head><body>
<?php

initializeDataBase_lp();

//Example of use of form editLMS (Still needs work!)
echo "<h3>Add/Modify permitted LTI consumers</h3>";
$form = new editLMS();
if(isset($_REQUEST['id']))
{
	switch($form->getStatus())
	{
	case FORM_NOTSUBMITTED:
		$client = lti_consumer::retrieve_lti_consumer($_REQUEST['id']);
	    $form->setData($client);
	    echo $form->getHtml();
	    break;
	case FORM_SUBMITTED_INVALID:
	    echo $form->getHtml();
	    break;
	case FORM_SUBMITTED_VALID:
    	$cons = new lti_consumer();
	    $form->getData($cons);
        $cons->keyHash = md5($cons->consumer_key);
	    if($cons->id > 0)
	        $cons->update();
	    else
	    {
	        $_REQUEST['id'] = $cons->insert();
	    }
	    break;
	case FORM_CANCELED:
	    break;
	}
	$client = lti_consumer::retrieve_lti_consumer($_REQUEST['id']);
    if($client !== false)
    {
		$resources = lti_acj_resource::retrieve_lti_acj_resource_matching('client_id', $client->id);

    }
	//# display list + edit form
	echo "<a href='{$_SERVER['PHP_SELF']}'>Return to admin page</a><br/>";
}
else
{
	echo "<b>Add a new consumer</b><br/>";
    echo $form->getHtml();
	$clients = lti_consumer::retrieve_all();
	if($clients != false)
	{
		foreach($clients as $c)
		{
	        $resources = lti_acj_resource::retrieve_lti_acj_resource_matching('client_id', $c->id);
	        if(is_array($resources))
	        	$rescount = sizeof($resources);
	        else
	        	$rescount = 0;

			echo "<a href='{$_SERVER['PHP_SELF']}?id={$c->id}'>{$c->consumer_key} ($rescount activities</a>)<br/>";
		}
	}
	else
	{
		echo "No LTI consumers set up yet.";
	}
}


?>

</body>

</html>
