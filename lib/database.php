<?php
require_once('corelib/dataaccess.php');

function initializeDataBase_lp()
{
	$query = "show tables;";
	$result = dataConnection::runQuery($query);
    //exit('<pre>'.print_r($result,1).'</pre>');
    if(sizeof($result)==0)
    {
		$query = "CREATE TABLE lp_lti_consumer(id INTEGER PRIMARY KEY AUTO_INCREMENT, keyHash VARCHAR(32), consumer_key VARCHAR(255), secret VARCHAR(255));";
		dataConnection::runQuery($query);
		$query = "CREATE TABLE lp_lti_acj_resource(id INTEGER PRIMARY KEY AUTO_INCREMENT, keyHash VARCHAR(32), client_id INTEGER, resource_link_id VARCHAR(255), created DATETIME, updated DATETIME, link_title TEXT, context_title TEXT, context_label TEXT, question TEXT, rubric TEXT, editopen DATETIME, editclosed DATETIME, commentopen DATETIME, commentclosed DATETIME, rankedby VARCHAR(10), sharecomments INTEGER, submissionsource VARCHAR(15), submissiontype VARCHAR(10), submissionsubtype VARCHAR(10), scoringstrategy VARCHAR(10), returnscore VARCHAR(10), folder VARCHAR(120), round INTEGER, lis_outcome_service_url VARCHAR(1024), extras TEXT);";
		dataConnection::runQuery($query);
		$query = "CREATE TABLE lp_ltiUser(id INTEGER PRIMARY KEY AUTO_INCREMENT, firstVisit DATETIME, lastvisit DATETIME, owner_id INTEGER, ltiRoles VARCHAR(40), userID VARCHAR(40), givenName VARCHAR(40), familyName VARCHAR(40), email VARCHAR(120), lis_result_sourcedid VARCHAR(255));";
		dataConnection::runQuery($query);
		$query = "CREATE TABLE lp_acjSubmission(id INTEGER PRIMARY KEY AUTO_INCREMENT, code VARCHAR(40), uploaded DATETIME, activity_id INTEGER, owner_id INTEGER, rank INTEGER, latestScore FLOAT, data TEXT, value TEXT, fromteacher INTEGER, submissiontype VARCHAR(10), mime VARCHAR(80), height INTEGER, width INTEGER);";
		dataConnection::runQuery($query);
		$query = "CREATE TABLE lp_acjComparison(id INTEGER PRIMARY KEY AUTO_INCREMENT, userID VARCHAR(40), activity_id INTEGER, madeBy_id INTEGER, round INTEGER, left_id INTEGER, right_id INTEGER, leftWon INTEGER, rightWon INTEGER, allocated DATETIME, done DATETIME);";
		dataConnection::runQuery($query);
    }
}

//Skeleton PHP classes for data tables

class lti_consumer
{
	var $id; //primary key
	var $keyHash;
	var $consumer_key;
	var $secret;

	function __construct($asArray=null)
	{
		$this->id = null; //primary key
		$this->keyHash = "";
		$this->consumer_key = "";
		$this->secret = "";
		if($asArray!==null)
			$this->fromArray($asArray);
	}

	function fromArray($asArray)
	{
		$this->id = $asArray['id'];
		$this->keyHash = $asArray['keyHash'];
		$this->consumer_key = $asArray['consumer_key'];
		$this->secret = $asArray['secret'];
	}

	static function retrieve_lti_consumer($id)
	{
		$query = "SELECT * FROM lp_lti_consumer WHERE id='".dataConnection::safe($id)."';";
		$result = dataConnection::runQuery($query);
		if(sizeof($result)!=0)
		{
			return new lti_consumer($result[0]);
		}
		else
			return false;
	}


	static function retrieve_by_keyHash($keyHash)
	{
		$query = "SELECT * FROM lp_lti_consumer WHERE keyHash='".dataConnection::safe($keyHash)."';";
		$result = dataConnection::runQuery($query);
		if(sizeof($result)!=0)
		{
			return new lti_consumer($result[0]);
		}
		else
			return false;
	}

	static function retrieve_lti_consumer_matching($field, $value, $from=0, $count=-1, $sort=null)
	{
	    if(preg_replace('/\W/','',$field)!== $field)
	        return false; // not a permitted field name;
	    $query = "SELECT * FROM lp_lti_consumer WHERE $field='".dataConnection::safe($value)."'";
	    if(($sort !== null)&&(preg_replace('/\W/','',$sort)!== $sort))
	        $query .= " ORDER BY ".$sort;
	    if(($count != -1)&&(is_int($count))&&(is_int($from)))
	        $query .= " LIMIT ".$count." OFFSET ".$from;
	    $query .= ';';
	    $result = dataConnection::runQuery($query);
	    if(sizeof($result)!=0)
	    {
	        $output = array();
	        foreach($result as $r)
	            $output[] = new lti_consumer($r);
	        return $output;
	    }
	    else
	        return false;
	}

	function insert()
	{
		//#Any required insert methods for foreign keys need to be called here.
		$query = "INSERT INTO lp_lti_consumer(keyHash, consumer_key, secret) VALUES(";
		$query .= "'".dataConnection::safe($this->keyHash)."', ";
		$query .= "'".dataConnection::safe($this->consumer_key)."', ";
		$query .= "'".dataConnection::safe($this->secret)."');";
		dataConnection::runQuery("BEGIN;");
		$result = dataConnection::runQuery($query);
		$result2 = dataConnection::runQuery("SELECT LAST_INSERT_ID() AS id;");
		dataConnection::runQuery("COMMIT;");
		$this->id = $result2[0]['id'];
		return $this->id;
	}

	function update()
	{
		$query = "UPDATE lp_lti_consumer ";
		$query .= "SET keyHash='".dataConnection::safe($this->keyHash)."' ";
		$query .= ", consumer_key='".dataConnection::safe($this->consumer_key)."' ";
		$query .= ", secret='".dataConnection::safe($this->secret)."' ";
		$query .= "WHERE id='".dataConnection::safe($this->id)."';";
		return dataConnection::runQuery($query);
	}

	static function count($where_name=null, $equals_value=null)
	{
		$query = "SELECT COUNT(*) AS count FROM lp_lti_consumer WHERE ";
		if($where_name==null)
			$query .= '1;';
		else
			$query .= "$where_name='".dataConnection::safe($equals_value)."';";
		$result = dataConnection::runQuery($query);
		if($result == false)
			return 0;
		else
			return $result['0']['count'];
	}

	function toXML()
	{
		$out = "<lti_consumer>\n";
		$out .= '<id>'.htmlentities($this->id)."</id>\n";
		$out .= '<keyHash>'.htmlentities($this->keyHash)."</keyHash>\n";
		$out .= '<consumer_key>'.htmlentities($this->consumer_key)."</consumer_key>\n";
		$out .= '<secret>'.htmlentities($this->secret)."</secret>\n";
		$out .= "</lti_consumer>\n";
		return $out;
	}
	//[[USERCODE_lti_consumer]] Put code for custom class members in this block.

	static function retrieve_all()
	{
	    $query = "SELECT * FROM lp_lti_consumer WHERE 1;";
	    $result = dataConnection::runQuery($query);
	    if(sizeof($result)!=0)
	    {
	        $output = array();
	        foreach($result as $r)
	            $output[] = new lti_consumer($r);
	        return $output;
	    }
	    else
	        return false;
	}

	//[[USERCODE_lti_consumer]] WEnd of custom class members.
}

class lti_acj_resource
{
	var $id; //primary key
	var $keyHash;
	var $client_id; //foreign key
	var $resource_link_id;
	var $created;
	var $updated;
	var $link_title;
	var $context_title;
	var $context_label;
	var $question;
	var $rubric;
	var $editopen;
	var $editclosed;
	var $commentopen;
	var $commentclosed;
	var $rankedby;
	var $sharecomments;
	var $submissionsource;
	var $submissiontype;
	var $submissionsubtype;
	var $scoringstrategy;
	var $returnscore;
	var $folder;
	var $round;
	var $lis_outcome_service_url;
	var $extras;

	function __construct($asArray=null)
	{
		$this->id = null; //primary key
		$this->keyHash = "";
		$this->client_id = null; // foreign key, needs dealt with.
		$this->resource_link_id = "";
		$this->created = time();
		$this->updated = time();
		$this->link_title = "";
		$this->context_title = "";
		$this->context_label = "";
		$this->question = "";
		$this->rubric = "";
		$this->editopen = time();
		$this->editclosed = time()+48*3600;
		$this->commentopen = time()+49*3600;
		$this->commentclosed = time()+97*3600;
		$this->rankedby = "";
		$this->sharecomments = false;
		$this->submissionsource = "";
		$this->submissiontype = "";
		$this->submissionsubtype = "";
		$this->scoringstrategy = "";
		$this->returnscore = "";
		$this->folder = "";
		$this->round = "0";
		$this->lis_outcome_service_url = "";
		$this->extras = "";
		if($asArray!==null)
			$this->fromArray($asArray);
	}

	function fromArray($asArray)
	{
		$this->id = $asArray['id'];
		$this->keyHash = $asArray['keyHash'];
		$this->client_id = $asArray['client_id']; // foreign key, check code
		$this->resource_link_id = $asArray['resource_link_id'];
		$this->created = dataConnection::db2time($asArray['created']);
		$this->updated = dataConnection::db2time($asArray['updated']);
		$this->link_title = $asArray['link_title'];
		$this->context_title = $asArray['context_title'];
		$this->context_label = $asArray['context_label'];
		$this->question = $asArray['question'];
		$this->rubric = $asArray['rubric'];
		$this->editopen = dataConnection::db2time($asArray['editopen']);
		$this->editclosed = dataConnection::db2time($asArray['editclosed']);
		$this->commentopen = dataConnection::db2time($asArray['commentopen']);
		$this->commentclosed = dataConnection::db2time($asArray['commentclosed']);
		$this->rankedby = $asArray['rankedby'];
		$this->sharecomments = ($asArray['sharecomments']==0)?false:true;
		$this->submissionsource = $asArray['submissionsource'];
		$this->submissiontype = $asArray['submissiontype'];
		$this->submissionsubtype = $asArray['submissionsubtype'];
		$this->scoringstrategy = $asArray['scoringstrategy'];
		$this->returnscore = $asArray['returnscore'];
		$this->folder = $asArray['folder'];
		$this->round = $asArray['round'];
		$this->lis_outcome_service_url = $asArray['lis_outcome_service_url'];
		$this->extras = $asArray['extras'];
	}

	static function retrieve_lti_acj_resource($id)
	{
		$query = "SELECT * FROM lp_lti_acj_resource WHERE id='".dataConnection::safe($id)."';";
		$result = dataConnection::runQuery($query);
		if(sizeof($result)!=0)
		{
			return new lti_acj_resource($result[0]);
		}
		else
			return false;
	}


	static function retrieve_by_keyHash($keyHash)
	{
		$query = "SELECT * FROM lp_lti_acj_resource WHERE keyHash='".dataConnection::safe($keyHash)."';";
		$result = dataConnection::runQuery($query);
		if(sizeof($result)!=0)
		{
			return new lti_acj_resource($result[0]);
		}
		else
			return false;
	}


	static function retrieve_by_folder($folder)
	{
		$query = "SELECT * FROM lp_lti_acj_resource WHERE folder='".dataConnection::safe($folder)."';";
		$result = dataConnection::runQuery($query);
		if(sizeof($result)!=0)
		{
			return new lti_acj_resource($result[0]);
		}
		else
			return false;
	}

	static function retrieve_lti_acj_resource_matching($field, $value, $from=0, $count=-1, $sort=null)
	{
	    if(preg_replace('/\W/','',$field)!== $field)
	        return false; // not a permitted field name;
	    $query = "SELECT * FROM lp_lti_acj_resource WHERE $field='".dataConnection::safe($value)."'";
	    if(($sort !== null)&&(preg_replace('/\W/','',$sort)!== $sort))
	        $query .= " ORDER BY ".$sort;
	    if(($count != -1)&&(is_int($count))&&(is_int($from)))
	        $query .= " LIMIT ".$count." OFFSET ".$from;
	    $query .= ';';
	    $result = dataConnection::runQuery($query);
	    if(sizeof($result)!=0)
	    {
	        $output = array();
	        foreach($result as $r)
	            $output[] = new lti_acj_resource($r);
	        return $output;
	    }
	    else
	        return false;
	}

	function insert()
	{
		//#Any required insert methods for foreign keys need to be called here.
		$query = "INSERT INTO lp_lti_acj_resource(keyHash, client_id, resource_link_id, created, updated, link_title, context_title, context_label, question, rubric, editopen, editclosed, commentopen, commentclosed, rankedby, sharecomments, submissionsource, submissiontype, submissionsubtype, scoringstrategy, returnscore, folder, round, lis_outcome_service_url, extras) VALUES(";
		$query .= "'".dataConnection::safe($this->keyHash)."', ";
		if($this->client_id!==null)
			$query .= "'".dataConnection::safe($this->client_id)."', ";
		else
			$query .= "null, ";
		$query .= "'".dataConnection::safe($this->resource_link_id)."', ";
		$query .= "'".dataConnection::time2db($this->created)."', ";
		$query .= "'".dataConnection::time2db($this->updated)."', ";
		$query .= "'".dataConnection::safe($this->link_title)."', ";
		$query .= "'".dataConnection::safe($this->context_title)."', ";
		$query .= "'".dataConnection::safe($this->context_label)."', ";
		$query .= "'".dataConnection::safe($this->question)."', ";
		$query .= "'".dataConnection::safe($this->rubric)."', ";
		$query .= "'".dataConnection::time2db($this->editopen)."', ";
		$query .= "'".dataConnection::time2db($this->editclosed)."', ";
		$query .= "'".dataConnection::time2db($this->commentopen)."', ";
		$query .= "'".dataConnection::time2db($this->commentclosed)."', ";
		$query .= "'".dataConnection::safe($this->rankedby)."', ";
		$query .= "'".(($this->sharecomments===false)?0:1)."', ";
		$query .= "'".dataConnection::safe($this->submissionsource)."', ";
		$query .= "'".dataConnection::safe($this->submissiontype)."', ";
		$query .= "'".dataConnection::safe($this->submissionsubtype)."', ";
		$query .= "'".dataConnection::safe($this->scoringstrategy)."', ";
		$query .= "'".dataConnection::safe($this->returnscore)."', ";
		$query .= "'".dataConnection::safe($this->folder)."', ";
		$query .= "'".dataConnection::safe($this->round)."', ";
		$query .= "'".dataConnection::safe($this->lis_outcome_service_url)."', ";
		$query .= "'".dataConnection::safe($this->extras)."');";
		dataConnection::runQuery("BEGIN;");
		$result = dataConnection::runQuery($query);
		$result2 = dataConnection::runQuery("SELECT LAST_INSERT_ID() AS id;");
		dataConnection::runQuery("COMMIT;");
		$this->id = $result2[0]['id'];
		return $this->id;
	}

	function update()
	{
		$query = "UPDATE lp_lti_acj_resource ";
		$query .= "SET keyHash='".dataConnection::safe($this->keyHash)."' ";
		$query .= ", client_id='".dataConnection::safe($this->client_id)."' ";
		$query .= ", resource_link_id='".dataConnection::safe($this->resource_link_id)."' ";
		$query .= ", created='".dataConnection::time2db($this->created)."' ";
		$query .= ", updated='".dataConnection::time2db($this->updated)."' ";
		$query .= ", link_title='".dataConnection::safe($this->link_title)."' ";
		$query .= ", context_title='".dataConnection::safe($this->context_title)."' ";
		$query .= ", context_label='".dataConnection::safe($this->context_label)."' ";
		$query .= ", question='".dataConnection::safe($this->question)."' ";
		$query .= ", rubric='".dataConnection::safe($this->rubric)."' ";
		$query .= ", editopen='".dataConnection::time2db($this->editopen)."' ";
		$query .= ", editclosed='".dataConnection::time2db($this->editclosed)."' ";
		$query .= ", commentopen='".dataConnection::time2db($this->commentopen)."' ";
		$query .= ", commentclosed='".dataConnection::time2db($this->commentclosed)."' ";
		$query .= ", rankedby='".dataConnection::safe($this->rankedby)."' ";
		$query .= ", sharecomments='".(($this->sharecomments===false)?0:1)."' ";
		$query .= ", submissionsource='".dataConnection::safe($this->submissionsource)."' ";
		$query .= ", submissiontype='".dataConnection::safe($this->submissiontype)."' ";
		$query .= ", submissionsubtype='".dataConnection::safe($this->submissionsubtype)."' ";
		$query .= ", scoringstrategy='".dataConnection::safe($this->scoringstrategy)."' ";
		$query .= ", returnscore='".dataConnection::safe($this->returnscore)."' ";
		$query .= ", folder='".dataConnection::safe($this->folder)."' ";
		$query .= ", round='".dataConnection::safe($this->round)."' ";
		$query .= ", lis_outcome_service_url='".dataConnection::safe($this->lis_outcome_service_url)."' ";
		$query .= ", extras='".dataConnection::safe($this->extras)."' ";
		$query .= "WHERE id='".dataConnection::safe($this->id)."';";
		return dataConnection::runQuery($query);
	}

	static function count($where_name=null, $equals_value=null)
	{
		$query = "SELECT COUNT(*) AS count FROM lp_lti_acj_resource WHERE ";
		if($where_name==null)
			$query .= '1;';
		else
			$query .= "$where_name='".dataConnection::safe($equals_value)."';";
		$result = dataConnection::runQuery($query);
		if($result == false)
			return 0;
		else
			return $result['0']['count'];
	}


	//1:n relationship to acjSubmission
	function get_submissions_count()
	{
	    $query = "SELECT COUNT(*) AS count FROM lp_acjSubmission WHERE activity_id = {$this->id};";
	    $result = dataConnection::runQuery($query);
	    if($result == false)
	        return 0;
	    else
	        return $result['0']['count'];
	}

	function get_submissions($from=0, $count=-1, $sort=null)
    {
        $query = "SELECT * FROM lp_acjSubmission WHERE activity_id='$this->id'";
        if(($sort !== null)&&(preg_replace('/\W/','',$sort)!== $sort))
            $query .= " ORDER BY ".$sort;
        if(($count != -1)&&(is_int($count))&&(is_int($from)))
            $query .= " LIMIT ".$count." OFFSET ".$from;
        $query .= ';';
        $result = dataConnection::runQuery($query);
        if(sizeof($result)!=0)
        {
            $output = array();
            foreach($result as $r)
                $output[] = new acjSubmission($r);
            return $output;
        }
        else
            return false;
    }

	//1:n relationship to acjComparison
	function get_comparisons_count()
	{
	    $query = "SELECT COUNT(*) AS count FROM lp_acjComparison WHERE activity_id = {$this->id};";
	    $result = dataConnection::runQuery($query);
	    if($result == false)
	        return 0;
	    else
	        return $result['0']['count'];
	}

	    function get_comparisons($from=0, $count=-1, $sort=null)
    {
        $query = "SELECT * FROM lp_acjComparison WHERE lti_acj_resource_id='$this->id'";
        if(($sort !== null)&&(preg_replace('/\W/','',$sort)!== $sort))
            $query .= " ORDER BY ".$sort;
        if(($count != -1)&&(is_int($count))&&(is_int($from)))
            $query .= " LIMIT ".$count." OFFSET ".$from;
        $query .= ';';
        $result = dataConnection::runQuery($query);
        if(sizeof($result)!=0)
        {
            $output = array();
            foreach($result as $r)
                $output[] = new acjComparison($r);
            return $output;
        }
        else
            return false;
    }

    	function toXML()
	{
		$out = "<lti_acj_resource>\n";
		$out .= '<id>'.htmlentities($this->id)."</id>\n";
		$out .= '<keyHash>'.htmlentities($this->keyHash)."</keyHash>\n";
		$out .= '<client>'.htmlentities($this->client)."</client>\n";
		$out .= '<resource_link_id>'.htmlentities($this->resource_link_id)."</resource_link_id>\n";
		$out .= '<created>'.htmlentities($this->created)."</created>\n";
		$out .= '<updated>'.htmlentities($this->updated)."</updated>\n";
		$out .= '<link_title>'.htmlentities($this->link_title)."</link_title>\n";
		$out .= '<context_title>'.htmlentities($this->context_title)."</context_title>\n";
		$out .= '<context_label>'.htmlentities($this->context_label)."</context_label>\n";
		$out .= '<question>'.htmlentities($this->question)."</question>\n";
		$out .= '<rubric>'.htmlentities($this->rubric)."</rubric>\n";
		$out .= '<editopen>'.htmlentities($this->editopen)."</editopen>\n";
		$out .= '<editclosed>'.htmlentities($this->editclosed)."</editclosed>\n";
		$out .= '<commentopen>'.htmlentities($this->commentopen)."</commentopen>\n";
		$out .= '<commentclosed>'.htmlentities($this->commentclosed)."</commentclosed>\n";
		$out .= '<rankedby>'.htmlentities($this->rankedby)."</rankedby>\n";
		$out .= '<sharecomments>'.htmlentities($this->sharecomments)."</sharecomments>\n";
		$out .= '<submissionsource>'.htmlentities($this->submissionsource)."</submissionsource>\n";
		$out .= '<submissiontype>'.htmlentities($this->submissiontype)."</submissiontype>\n";
		$out .= '<submissionsubtype>'.htmlentities($this->submissionsubtype)."</submissionsubtype>\n";
		$out .= '<scoringstrategy>'.htmlentities($this->scoringstrategy)."</scoringstrategy>\n";
		$out .= '<returnscore>'.htmlentities($this->returnscore)."</returnscore>\n";
		$out .= '<folder>'.htmlentities($this->folder)."</folder>\n";
		$out .= '<round>'.htmlentities($this->round)."</round>\n";
		$out .= '<lis_outcome_service_url>'.htmlentities($this->lis_outcome_service_url)."</lis_outcome_service_url>\n";
		$out .= '<extras>'.htmlentities($this->extras)."</extras>\n";
		$out .= "</lti_acj_resource>\n";
		return $out;
	}
	//[[USERCODE_lti_acj_resource]] Put code for custom class members in this block.
    function get_unalocated_comparisons($round)
    {
        $query = "SELECT * FROM lp_acjComparison WHERE activity_id='$this->id'";
        $query .= " AND round='".dataConnection::safe($round)."' AND userID=''";
        $query .= ';';
        //echo "$query<br/>";
        $result = dataConnection::runQuery($query);
        $output = array();
        if(sizeof($result)!=0)
        {
            foreach($result as $r)
                $output[] = new acjComparison($r);
        }
        return $output;
    }

    function get_incomplete_comparisons($round)
    {
        $query = "SELECT * FROM lp_acjComparison WHERE activity_id='$this->id'";
        $query .= " AND round='".dataConnection::safe($round)."' AND leftWon='0' AND rightWon='0'";
        $query .= ';';
        //echo "$query<br/>";
        $result = dataConnection::runQuery($query);
        $output = array();
        if(sizeof($result)!=0)
        {
            foreach($result as $r)
                $output[] = new acjComparison($r);
        }
        return $output;
    }

    function get_users_incomplete_comparisons($round, $uname)
    {
        $query = "SELECT * FROM lp_acjComparison WHERE activity_id='$this->id'";
        $query .= " AND round='".dataConnection::safe($round)."' AND leftWon='0' AND rightWon='0'";
        $query .= " AND userID='".dataConnection::safe($uname)."'";
        $query .= ';';
        //echo "$query<br/>";
        $result = dataConnection::runQuery($query);
        $output = array();
        if(sizeof($result)!=0)
        {
            foreach($result as $r)
                $output[] = new acjComparison($r);
        }
        return $output;
    }

	function get_submissionsWithKey($from=0, $count=-1, $sort=null)
    {
        $query = "SELECT * FROM lp_acjSubmission WHERE activity_id='$this->id'";
        if(($sort !== null)&&(preg_replace('/\W/','',$sort)!== $sort))
            $query .= " ORDER BY ".$sort;
        if(($count != -1)&&(is_int($count))&&(is_int($from)))
            $query .= " LIMIT ".$count." OFFSET ".$from;
        $query .= ';';
        $result = dataConnection::runQuery($query);
        $output = array();
        if(sizeof($result)!=0)
        {
            foreach($result as $r)
            {
                $acjs = new acjSubmission($r);
                $output[$acjs->id] = $acjs;
            }
        }
        return $output;
    }

	function getUsersWithSubInfo($from=0, $count=-1, $sort=null)
    {
        $query = "SELECT lp_ltiUser.id AS uid, lp_ltiUser.*, lp_acjSubmission.id AS sid, lp_acjSubmission.uploaded FROM lp_ltiUser LEFT OUTER JOIN lp_acjSubmission ON lp_ltiUser.id=lp_acjSubmission.owner_id WHERE lp_ltiUser.owner_id='{$this->id}'";
        $query .= " ORDER BY lp_ltiUser.familyName ASC, lp_ltiUser.givenName ASC, lp_ltiUser.userID ASC;";
        $result = dataConnection::runQuery($query);
        $output = array();
    //echo '<pre>'.print_r($result,1).'</pre>';
        if(sizeof($result)!=0)
        {
            foreach($result as $asArray)
            {
                $user = new ltiUser();
                $user->id = $asArray['uid'];
				$user->firstVisit = dataConnection::db2time($asArray['firstVisit']);
				$user->lastvisit = dataConnection::db2time($asArray['lastvisit']);
				$user->owner_id = $asArray['owner_id']; // foreign key, check code
				$user->ltiRoles = $asArray['ltiRoles'];
				$user->userID = $asArray['userID'];
				$user->givenName = $asArray['givenName'];
				$user->familyName = $asArray['familyName'];
				$user->email = $asArray['email'];
				$user->lis_result_sourcedid = $asArray['lis_result_sourcedid'];
				$user->SubmissionId = $asArray['sid']; // not sure about theis wityh MySQL
                if(strlen($asArray['uploaded']))
					$user->submissionUploaded = dataConnection::db2time($asArray['uploaded']);
                $output[] = $user;
            }
        }
        return $output;
    }

	function get_learners_count()
	{
	    $query = "SELECT COUNT(*) AS count FROM lp_ltiUser WHERE owner_id='{$this->id}' AND ltiRoles LIKE '%Learner%';";
	    $result = dataConnection::runQuery($query);
	    if($result == false)
	        return 0;
	    else
	        return $result['0']['count'];
	}

	//[[USERCODE_lti_acj_resource]] WEnd of custom class members.
}

class ltiUser
{
	var $id; //primary key
	var $firstVisit;
	var $lastvisit;
	var $owner_id; //foreign key
	var $ltiRoles;
	var $userID;
	var $givenName;
	var $familyName;
	var $email;
	var $lis_result_sourcedid;

	function __construct($asArray=null)
	{
		$this->id = null; //primary key
		$this->firstVisit = time();
		$this->lastvisit = time();
		$this->owner_id = null; // foreign key, needs dealt with.
		$this->ltiRoles = "";
		$this->userID = "";
		$this->givenName = "";
		$this->familyName = "";
		$this->email = "";
		$this->lis_result_sourcedid = "";
		if($asArray!==null)
			$this->fromArray($asArray);
	}

	function fromArray($asArray)
	{
		$this->id = $asArray['id'];
		$this->firstVisit = dataConnection::db2time($asArray['firstVisit']);
		$this->lastvisit = dataConnection::db2time($asArray['lastvisit']);
		$this->owner_id = $asArray['owner_id']; // foreign key, check code
		$this->ltiRoles = $asArray['ltiRoles'];
		$this->userID = $asArray['userID'];
		$this->givenName = $asArray['givenName'];
		$this->familyName = $asArray['familyName'];
		$this->email = $asArray['email'];
		$this->lis_result_sourcedid = $asArray['lis_result_sourcedid'];
	}

	static function retrieve_ltiUser($id)
	{
		$query = "SELECT * FROM lp_ltiUser WHERE id='".dataConnection::safe($id)."';";
		$result = dataConnection::runQuery($query);
		if(sizeof($result)!=0)
		{
			return new ltiUser($result[0]);
		}
		else
			return false;
	}

	static function retrieve_ltiUser_matching($field, $value, $from=0, $count=-1, $sort=null)
	{
	    if(preg_replace('/\W/','',$field)!== $field)
	        return false; // not a permitted field name;
	    $query = "SELECT * FROM lp_ltiUser WHERE $field='".dataConnection::safe($value)."'";
	    if(($sort !== null)&&(preg_replace('/\W/','',$sort)!== $sort))
	        $query .= " ORDER BY ".$sort;
	    if(($count != -1)&&(is_int($count))&&(is_int($from)))
	        $query .= " LIMIT ".$count." OFFSET ".$from;
	    $query .= ';';
	    $result = dataConnection::runQuery($query);
	    if(sizeof($result)!=0)
	    {
	        $output = array();
	        foreach($result as $r)
	            $output[] = new ltiUser($r);
	        return $output;
	    }
	    else
	        return false;
	}

	function insert()
	{
		//#Any required insert methods for foreign keys need to be called here.
		$query = "INSERT INTO lp_ltiUser(firstVisit, lastvisit, owner_id, ltiRoles, userID, givenName, familyName, email, lis_result_sourcedid) VALUES(";
		$query .= "'".dataConnection::time2db($this->firstVisit)."', ";
		$query .= "'".dataConnection::time2db($this->lastvisit)."', ";
		if($this->owner_id!==null)
			$query .= "'".dataConnection::safe($this->owner_id)."', ";
		else
			$query .= "null, ";
		$query .= "'".dataConnection::safe($this->ltiRoles)."', ";
		$query .= "'".dataConnection::safe($this->userID)."', ";
		$query .= "'".dataConnection::safe($this->givenName)."', ";
		$query .= "'".dataConnection::safe($this->familyName)."', ";
		$query .= "'".dataConnection::safe($this->email)."', ";
		$query .= "'".dataConnection::safe($this->lis_result_sourcedid)."');";
		dataConnection::runQuery("BEGIN;");
		$result = dataConnection::runQuery($query);
		$result2 = dataConnection::runQuery("SELECT LAST_INSERT_ID() AS id;");
		dataConnection::runQuery("COMMIT;");
		$this->id = $result2[0]['id'];
		return $this->id;
	}

	function update()
	{
		$query = "UPDATE lp_ltiUser ";
		$query .= "SET firstVisit='".dataConnection::time2db($this->firstVisit)."' ";
		$query .= ", lastvisit='".dataConnection::time2db($this->lastvisit)."' ";
		$query .= ", owner_id='".dataConnection::safe($this->owner_id)."' ";
		$query .= ", ltiRoles='".dataConnection::safe($this->ltiRoles)."' ";
		$query .= ", userID='".dataConnection::safe($this->userID)."' ";
		$query .= ", givenName='".dataConnection::safe($this->givenName)."' ";
		$query .= ", familyName='".dataConnection::safe($this->familyName)."' ";
		$query .= ", email='".dataConnection::safe($this->email)."' ";
		$query .= ", lis_result_sourcedid='".dataConnection::safe($this->lis_result_sourcedid)."' ";
		$query .= "WHERE id='".dataConnection::safe($this->id)."';";
		return dataConnection::runQuery($query);
	}

	static function count($where_name=null, $equals_value=null)
	{
		$query = "SELECT COUNT(*) AS count FROM lp_ltiUser WHERE ";
		if($where_name==null)
			$query .= '1;';
		else
			$query .= "$where_name='".dataConnection::safe($equals_value)."';";
		$result = dataConnection::runQuery($query);
		if($result == false)
			return 0;
		else
			return $result['0']['count'];
	}

	function toXML()
	{
		$out = "<ltiUser>\n";
		$out .= '<id>'.htmlentities($this->id)."</id>\n";
		$out .= '<firstVisit>'.htmlentities($this->firstVisit)."</firstVisit>\n";
		$out .= '<lastvisit>'.htmlentities($this->lastvisit)."</lastvisit>\n";
		$out .= '<owner>'.htmlentities($this->owner)."</owner>\n";
		$out .= '<ltiRoles>'.htmlentities($this->ltiRoles)."</ltiRoles>\n";
		$out .= '<userID>'.htmlentities($this->userID)."</userID>\n";
		$out .= '<givenName>'.htmlentities($this->givenName)."</givenName>\n";
		$out .= '<familyName>'.htmlentities($this->familyName)."</familyName>\n";
		$out .= '<email>'.htmlentities($this->email)."</email>\n";
		$out .= '<lis_result_sourcedid>'.htmlentities($this->lis_result_sourcedid)."</lis_result_sourcedid>\n";
		$out .= "</ltiUser>\n";
		return $out;
	}
	//[[USERCODE_ltiUser]] Put code for custom class members in this block.
	static function retrieveActivityUser($activityID, $userID)
	{
	    $query = "SELECT * FROM lp_ltiUser WHERE owner_id='".dataConnection::safe($activityID)."'";
	    $query .= " AND userID='".dataConnection::safe($userID)."';";
	    $result = dataConnection::runQuery($query);
	    if(sizeof($result)!=0)
	    {
			return new ltiUser($result[0]);
	    }
	    else
	        return false;
	}


	//[[USERCODE_ltiUser]] WEnd of custom class members.
}

class acjSubmission
{
	var $id; //primary key
	var $code;
	var $uploaded;
	var $activity_id; //foreign key
	var $owner_id; //foreign key
	var $rank;
	var $latestScore;
	var $data;
	var $value;
	var $fromteacher;
	var $submissiontype;
	var $mime;
	var $height;
	var $width;

	function __construct($asArray=null)
	{
		$this->id = null; //primary key
		$this->code = "";
		$this->uploaded = time();
		$this->activity_id = null; // foreign key, needs dealt with.
		$this->owner_id = null; // foreign key, needs dealt with.
		$this->rank = "0";
		$this->latestScore = "0";
		$this->data = false;
		$this->value = "";
		$this->fromteacher = false;
		$this->submissiontype = "";
		$this->mime = "";
		$this->height = "0";
		$this->width = "0";
		if($asArray!==null)
			$this->fromArray($asArray);
	}

	function fromArray($asArray)
	{
		$this->id = $asArray['id'];
		$this->code = $asArray['code'];
		$this->uploaded = dataConnection::db2time($asArray['uploaded']);
		$this->activity_id = $asArray['activity_id']; // foreign key, check code
		$this->owner_id = $asArray['owner_id']; // foreign key, check code
		$this->rank = $asArray['rank'];
		$this->latestScore = $asArray['latestScore'];
		$this->data = unserialize($asArray['data']);
		$this->value = $asArray['value'];
		$this->fromteacher = ($asArray['fromteacher']==0)?false:true;
		$this->submissiontype = $asArray['submissiontype'];
		$this->mime = $asArray['mime'];
		$this->height = $asArray['height'];
		$this->width = $asArray['width'];
	}

	static function retrieve_acjSubmission($id)
	{
		$query = "SELECT * FROM lp_acjSubmission WHERE id='".dataConnection::safe($id)."';";
		$result = dataConnection::runQuery($query);
		if(sizeof($result)!=0)
		{
			return new acjSubmission($result[0]);
		}
		else
			return false;
	}


	static function retrieve_by_code($code)
	{
		$query = "SELECT * FROM lp_acjSubmission WHERE code='".dataConnection::safe($code)."';";
		$result = dataConnection::runQuery($query);
		if(sizeof($result)!=0)
		{
			return new acjSubmission($result[0]);
		}
		else
			return false;
	}

	static function retrieve_acjSubmission_matching($field, $value, $from=0, $count=-1, $sort=null)
	{
	    if(preg_replace('/\W/','',$field)!== $field)
	        return false; // not a permitted field name;
	    $query = "SELECT * FROM lp_acjSubmission WHERE $field='".dataConnection::safe($value)."'";
	    if(($sort !== null)&&(preg_replace('/\W/','',$sort)!== $sort))
	        $query .= " ORDER BY ".$sort;
	    if(($count != -1)&&(is_int($count))&&(is_int($from)))
	        $query .= " LIMIT ".$count." OFFSET ".$from;
	    $query .= ';';
	    $result = dataConnection::runQuery($query);
	    if(sizeof($result)!=0)
	    {
	        $output = array();
	        foreach($result as $r)
	            $output[] = new acjSubmission($r);
	        return $output;
	    }
	    else
	        return false;
	}

	static function retrieve_by_activity_and_owner_ids($activity_id, $owner_id)
	{
		$query = "SELECT * FROM lp_acjSubmission WHERE activity_id='".dataConnection::safe($activity_id)."' AND owner_id='".dataConnection::safe($owner_id)."';";
		$result = dataConnection::runQuery($query);
		if(sizeof($result)!=0)
		{
			return new acjSubmission($result[0]);
		}
		else
			return false;
	}

	function insert()
	{
		//#Any required insert methods for foreign keys need to be called here.
		$query = "INSERT INTO lp_acjSubmission(code, uploaded, activity_id, owner_id, rank, latestScore, data, value, fromteacher, submissiontype, mime, height, width) VALUES(";
		$query .= "'".dataConnection::safe($this->code)."', ";
		$query .= "'".dataConnection::time2db($this->uploaded)."', ";
		if($this->activity_id!==null)
			$query .= "'".dataConnection::safe($this->activity_id)."', ";
		else
			$query .= "null, ";
		if($this->owner_id!==null)
			$query .= "'".dataConnection::safe($this->owner_id)."', ";
		else
			$query .= "null, ";
		$query .= "'".dataConnection::safe($this->rank)."', ";
		$query .= "'".dataConnection::safe($this->latestScore)."', ";
		$query .= "'".dataConnection::safe(serialize($this->data))."', ";
		$query .= "'".dataConnection::safe($this->value)."', ";
		$query .= "'".(($this->fromteacher===false)?0:1)."', ";
		$query .= "'".dataConnection::safe($this->submissiontype)."', ";
		$query .= "'".dataConnection::safe($this->mime)."', ";
		$query .= "'".dataConnection::safe($this->height)."', ";
		$query .= "'".dataConnection::safe($this->width)."');";
		dataConnection::runQuery("BEGIN;");
		$result = dataConnection::runQuery($query);
		$result2 = dataConnection::runQuery("SELECT LAST_INSERT_ID() AS id;");
		dataConnection::runQuery("COMMIT;");
		$this->id = $result2[0]['id'];
		return $this->id;
	}

	function update()
	{
		$query = "UPDATE lp_acjSubmission ";
		$query .= "SET code='".dataConnection::safe($this->code)."' ";
		$query .= ", uploaded='".dataConnection::time2db($this->uploaded)."' ";
		$query .= ", activity_id='".dataConnection::safe($this->activity_id)."' ";
		$query .= ", owner_id='".dataConnection::safe($this->owner_id)."' ";
		$query .= ", rank='".dataConnection::safe($this->rank)."' ";
		$query .= ", latestScore='".dataConnection::safe($this->latestScore)."' ";
		$query .= ", data='".dataConnection::safe(serialize($this->data))."' ";
		$query .= ", value='".dataConnection::safe($this->value)."' ";
		$query .= ", fromteacher='".(($this->fromteacher===false)?0:1)."' ";
		$query .= ", submissiontype='".dataConnection::safe($this->submissiontype)."' ";
		$query .= ", mime='".dataConnection::safe($this->mime)."' ";
		$query .= ", height='".dataConnection::safe($this->height)."' ";
		$query .= ", width='".dataConnection::safe($this->width)."' ";
		$query .= "WHERE id='".dataConnection::safe($this->id)."';";
		return dataConnection::runQuery($query);
	}

	static function count($where_name=null, $equals_value=null)
	{
		$query = "SELECT COUNT(*) AS count FROM lp_acjSubmission WHERE ";
		if($where_name==null)
			$query .= '1;';
		else
			$query .= "$where_name='".dataConnection::safe($equals_value)."';";
		$result = dataConnection::runQuery($query);
		if($result == false)
			return 0;
		else
			return $result['0']['count'];
	}

	function toXML()
	{
		$out = "<acjSubmission>\n";
		$out .= '<id>'.htmlentities($this->id)."</id>\n";
		$out .= '<code>'.htmlentities($this->code)."</code>\n";
		$out .= '<uploaded>'.htmlentities($this->uploaded)."</uploaded>\n";
		$out .= '<activity>'.htmlentities($this->activity)."</activity>\n";
		$out .= '<owner>'.htmlentities($this->owner)."</owner>\n";
		$out .= '<rank>'.htmlentities($this->rank)."</rank>\n";
		$out .= '<latestScore>'.htmlentities($this->latestScore)."</latestScore>\n";
		$out .= '<data>'.htmlentities($this->data)."</data>\n";
		$out .= '<value>'.htmlentities($this->value)."</value>\n";
		$out .= '<fromteacher>'.htmlentities($this->fromteacher)."</fromteacher>\n";
		$out .= '<submissiontype>'.htmlentities($this->submissiontype)."</submissiontype>\n";
		$out .= '<mime>'.htmlentities($this->mime)."</mime>\n";
		$out .= '<height>'.htmlentities($this->height)."</height>\n";
		$out .= '<width>'.htmlentities($this->width)."</width>\n";
		$out .= "</acjSubmission>\n";
		return $out;
	}
	//[[USERCODE_acjSubmission]] Put code for custom class members in this block.

    function delete()
	{
        if($this->id > 0)
        {
			$query = "DELETE FROM lp_acjSubmission WHERE id='".dataConnection::safe($this->id)."';";
			$result = dataConnection::runQuery($query);
            $this->id = 0;
        }
	}

	//[[USERCODE_acjSubmission]] WEnd of custom class members.
}

class acjComparison
{
	var $id; //primary key
	var $userID;
	var $activity_id; //foreign key
	var $madeBy_id; //foreign key
	var $round;
	var $left_id; //foreign key
	var $right_id; //foreign key
	var $leftWon;
	var $rightWon;
	var $allocated;
	var $done;

	function __construct($asArray=null)
	{
		$this->id = null; //primary key
		$this->userID = "";
		$this->activity_id = null; // foreign key, needs dealt with.
		$this->madeBy_id = null; // foreign key, needs dealt with.
		$this->round = "0";
		$this->left_id = null; // foreign key, needs dealt with.
		$this->right_id = null; // foreign key, needs dealt with.
		$this->leftWon = false;
		$this->rightWon = false;
		$this->allocated = time();
		$this->done = time();
		if($asArray!==null)
			$this->fromArray($asArray);
	}

	function fromArray($asArray)
	{
		$this->id = $asArray['id'];
		$this->userID = $asArray['userID'];
		$this->activity_id = $asArray['activity_id']; // foreign key, check code
		$this->madeBy_id = $asArray['madeBy_id']; // foreign key, check code
		$this->round = $asArray['round'];
		$this->left_id = $asArray['left_id']; // foreign key, check code
		$this->right_id = $asArray['right_id']; // foreign key, check code
		$this->leftWon = ($asArray['leftWon']==0)?false:true;
		$this->rightWon = ($asArray['rightWon']==0)?false:true;
		$this->allocated = dataConnection::db2time($asArray['allocated']);
		$this->done = dataConnection::db2time($asArray['done']);
	}

	static function retrieve_acjComparison($id)
	{
		$query = "SELECT * FROM lp_acjComparison WHERE id='".dataConnection::safe($id)."';";
		$result = dataConnection::runQuery($query);
		if(sizeof($result)!=0)
		{
			return new acjComparison($result[0]);
		}
		else
			return false;
	}

	static function retrieve_acjComparison_matching($field, $value, $from=0, $count=-1, $sort=null)
	{
	    if(preg_replace('/\W/','',$field)!== $field)
	        return false; // not a permitted field name;
	    $query = "SELECT * FROM lp_acjComparison WHERE $field='".dataConnection::safe($value)."'";
	    if(($sort !== null)&&(preg_replace('/\W/','',$sort)!== $sort))
	        $query .= " ORDER BY ".$sort;
	    if(($count != -1)&&(is_int($count))&&(is_int($from)))
	        $query .= " LIMIT ".$count." OFFSET ".$from;
	    $query .= ';';
	    $result = dataConnection::runQuery($query);
	    if(sizeof($result)!=0)
	    {
	        $output = array();
	        foreach($result as $r)
	            $output[] = new acjComparison($r);
	        return $output;
	    }
	    else
	        return false;
	}

	function insert()
	{
		//#Any required insert methods for foreign keys need to be called here.
		$query = "INSERT INTO lp_acjComparison(userID, activity_id, madeBy_id, round, left_id, right_id, leftWon, rightWon, allocated, done) VALUES(";
		$query .= "'".dataConnection::safe($this->userID)."', ";
		if($this->activity_id!==null)
			$query .= "'".dataConnection::safe($this->activity_id)."', ";
		else
			$query .= "null, ";
		if($this->madeBy_id!==null)
			$query .= "'".dataConnection::safe($this->madeBy_id)."', ";
		else
			$query .= "null, ";
		$query .= "'".dataConnection::safe($this->round)."', ";
		if($this->left_id!==null)
			$query .= "'".dataConnection::safe($this->left_id)."', ";
		else
			$query .= "null, ";
		if($this->right_id!==null)
			$query .= "'".dataConnection::safe($this->right_id)."', ";
		else
			$query .= "null, ";
		$query .= "'".(($this->leftWon===false)?0:1)."', ";
		$query .= "'".(($this->rightWon===false)?0:1)."', ";
		$query .= "'".dataConnection::time2db($this->allocated)."', ";
		$query .= "'".dataConnection::time2db($this->done)."');";
		dataConnection::runQuery("BEGIN;");
		$result = dataConnection::runQuery($query);
		$result2 = dataConnection::runQuery("SELECT LAST_INSERT_ID() AS id;");
		dataConnection::runQuery("COMMIT;");
		$this->id = $result2[0]['id'];
		return $this->id;
	}

	function update()
	{
		$query = "UPDATE lp_acjComparison ";
		$query .= "SET userID='".dataConnection::safe($this->userID)."' ";
		$query .= ", activity_id='".dataConnection::safe($this->activity_id)."' ";
		$query .= ", madeBy_id='".dataConnection::safe($this->madeBy_id)."' ";
		$query .= ", round='".dataConnection::safe($this->round)."' ";
		$query .= ", left_id='".dataConnection::safe($this->left_id)."' ";
		$query .= ", right_id='".dataConnection::safe($this->right_id)."' ";
		$query .= ", leftWon='".(($this->leftWon===false)?0:1)."' ";
		$query .= ", rightWon='".(($this->rightWon===false)?0:1)."' ";
		$query .= ", allocated='".dataConnection::time2db($this->allocated)."' ";
		$query .= ", done='".dataConnection::time2db($this->done)."' ";
		$query .= "WHERE id='".dataConnection::safe($this->id)."';";
		return dataConnection::runQuery($query);
	}

	static function count($where_name=null, $equals_value=null)
	{
		$query = "SELECT COUNT(*) AS count FROM lp_acjComparison WHERE ";
		if($where_name==null)
			$query .= '1;';
		else
			$query .= "$where_name='".dataConnection::safe($equals_value)."';";
		$result = dataConnection::runQuery($query);
		if($result == false)
			return 0;
		else
			return $result['0']['count'];
	}

	function toXML()
	{
		$out = "<acjComparison>\n";
		$out .= '<id>'.htmlentities($this->id)."</id>\n";
		$out .= '<userID>'.htmlentities($this->userID)."</userID>\n";
		$out .= '<activity>'.htmlentities($this->activity)."</activity>\n";
		$out .= '<madeBy>'.htmlentities($this->madeBy)."</madeBy>\n";
		$out .= '<round>'.htmlentities($this->round)."</round>\n";
		$out .= '<left>'.htmlentities($this->left)."</left>\n";
		$out .= '<right>'.htmlentities($this->right)."</right>\n";
		$out .= '<leftWon>'.htmlentities($this->leftWon)."</leftWon>\n";
		$out .= '<rightWon>'.htmlentities($this->rightWon)."</rightWon>\n";
		$out .= '<allocated>'.htmlentities($this->allocated)."</allocated>\n";
		$out .= '<done>'.htmlentities($this->done)."</done>\n";
		$out .= "</acjComparison>\n";
		return $out;
	}
	//[[USERCODE_acjComparison]] Put code for custom class members in this block.

	//[[USERCODE_acjComparison]] WEnd of custom class members.
}

