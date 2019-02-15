<?php

class secretManager extends ltiSecretManager
{
	var $data;
	function devSecretManager()
    {
        $this->data = array('12345'=>array('domain'=>'/', 'secret'=>'secret'));
    }

    function getSecret($key)
    {
    	$hash = md5($key);
        $client = lti_consumer::retrieve_lti_consumer_matching('keyHash',$hash);
        if($client === false)
        	return false;
        else
        	return $client[0]->secret;
    }

    function getDomain($key)
    {
    	$hash = md5($key);
        $client = lti_consumer::retrieve_lti_consumer_matching('keyHash',$hash);
        if($client === false)
        	return false;
        else
        	return $client->domain;
    }

    function registerNonce($nonce, $consumerKey)
    {
    	return true;
    }

}


