<?php

//message_response
class cls_message_response //extends
{
	//vars for attributes
	//vars for elements
	var $m_lti_message_type;
	var $m_statusinfo;
	var $m_memberships;

    function cls_message_response($xml=false)
    {
//initialise
        $this->m_memberships = false;
        if(is_string($xml))
            $xml=new SimpleXMLElement($xml);
        if($xml)
            $this->parseIn($xml);
    }

    function parseIn($xml)
    {
        foreach($xml->xpath("*") as $cxml)
        {
            $ename = $cxml->getName();
            switch($ename)
            {
                case 'lti_message_type':
                    if($this->m_lti_message_type==false)
                        $this->m_lti_message_type = strval($cxml);
                    break;
                case 'statusinfo':
                    if($this->m_statusinfo==false)
                        $this->m_statusinfo = new cls_statusinfo($cxml);
                    break;
                case 'memberships':
                    if($this->m_memberships==false)
                        $this->m_memberships = new cls_memberships($cxml);
                    break;
            }
        }
    }

    function toXML($neat=false, $indent=0)
    {
        if($neat)
            $pad = str_repeat('    ',$indent);
        $out = '<?xml version="1.0"?>';
        if($neat) $out .= "\n$pad";
        $out .= '<message_response';
        $out .= '>';
        if($neat) $out .= "\n$pad    ";
        $out .= '<lti_message_type>'.htmlentities($this->m_lti_message_type).'</lti_message_type>';
        $out .= $this->m_statusinfo->toXML($neat, $indent+1);
        if($this->m_memberships !== false)
        {
            $out .= $this->m_memberships->toXML($neat, $indent+1);
        }
        if($neat) $out .= "\n$pad";
        $out .= '</message_response>';
        return $out;
    }

};

//statusinfo
class cls_statusinfo //extends
{
	//vars for attributes
	//vars for elements
	var $m_codemajor;
	var $m_severity;
	var $m_description;

    function cls_statusinfo($xml=false)
    {
//initialise
        if(is_string($xml))
            $xml=new SimpleXMLElement($xml);
        if($xml)
            $this->parseIn($xml);
    }

    function parseIn($xml)
    {
        foreach($xml->xpath("*") as $cxml)
        {
            $ename = $cxml->getName();
            switch($ename)
            {
                case 'codemajor':
                    if($this->m_codemajor==false)
                        $this->m_codemajor = strval($cxml);
                    break;
                case 'severity':
                    if($this->m_severity==false)
                        $this->m_severity = strval($cxml);
                    break;
                case 'description':
                    if($this->m_description==false)
                        $this->m_description = strval($cxml);
                    break;
            }
        }
    }

    function toXML($neat=false, $indent=0)
    {
        if($neat)
        {
            $pad = str_repeat('    ',$indent);
            $out .= "\n$pad";
        }
        $out .= '<statusinfo';
        $out .= '>';
        if($neat) $out .= "\n$pad    ";
        $out .= '<codemajor>'.htmlentities($this->m_codemajor).'</codemajor>';
        if($neat) $out .= "\n$pad    ";
        $out .= '<severity>'.htmlentities($this->m_severity).'</severity>';
        if($neat) $out .= "\n$pad    ";
        $out .= '<description>'.htmlentities($this->m_description).'</description>';
        if($neat) $out .= "\n$pad";
        $out .= '</statusinfo>';
        return $out;
    }

};

//memberships
class cls_memberships //extends
{
	//vars for attributes
	//vars for elements
	var $m_member;

    function cls_memberships($xml=false)
    {
//initialise
        if(is_string($xml))
            $xml=new SimpleXMLElement($xml);
        if($xml)
            $this->parseIn($xml);
    }

    function parseIn($xml)
    {
        $this->m_member = array();
        foreach($xml->xpath("*") as $cxml)
        {
            $ename = $cxml->getName();
            switch($ename)
            {
                case 'member':
                    $this->m_member[] = new cls_member($cxml);
                    break;
            }
        }
    }

    function toXML($neat=false, $indent=0)
    {
        if($neat)
        {
            $pad = str_repeat('    ',$indent);
            $out .= "\n$pad";
        }
        $out .= '<memberships';
        $out .= '>';
        foreach($this->m_member as $m_member)
        {
            $out .= $m_member->toXML($neat, $indent+1);
        }
        if($neat) $out .= "\n$pad";
        $out .= '</memberships>';
        return $out;
    }

};

//member
class cls_member //extends
{
	//vars for attributes
	//vars for elements
	var $m_user_id;
	var $m_roles;
	var $m_person_name_given;
	var $m_person_name_family;
	var $m_person_contact_email_primary;
	var $m_lis_result_sourcedid;

    function cls_member($xml=false)
    {
//initialise
        $this->m_person_name_given = false;
        $this->m_person_name_family = false;
        $this->m_person_contact_email_primary = false;
        $this->m_lis_result_sourcedid = false;
        if(is_string($xml))
            $xml=new SimpleXMLElement($xml);
        if($xml)
            $this->parseIn($xml);
    }

    function parseIn($xml)
    {
        foreach($xml->xpath("*") as $cxml)
        {
            $ename = $cxml->getName();
            switch($ename)
            {
                case 'user_id':
                    if($this->m_user_id==false)
                        $this->m_user_id = strval($cxml);
                    break;
                case 'roles':
                    if($this->m_roles==false)
                        $this->m_roles = strval($cxml);
                    break;
                case 'person_name_given':
                    if($this->m_person_name_given==false)
                        $this->m_person_name_given = strval($cxml);
                    break;
                case 'person_name_family':
                    if($this->m_person_name_family==false)
                        $this->m_person_name_family = strval($cxml);
                    break;
                case 'person_contact_email_primary':
                    if($this->m_person_contact_email_primary==false)
                        $this->m_person_contact_email_primary = strval($cxml);
                    break;
                case 'lis_result_sourcedid':
                    if($this->m_lis_result_sourcedid==false)
                        $this->m_lis_result_sourcedid = strval($cxml);
                    break;
            }
        }
    }

    function toXML($neat=false, $indent=0)
    {
        if($neat)
        {
            $pad = str_repeat('    ',$indent);
            $out .= "\n$pad";
        }
        $out .= '<member';
        $out .= '>';
        if($neat) $out .= "\n$pad    ";
        $out .= '<user_id>'.htmlentities($this->m_user_id).'</user_id>';
        if($neat) $out .= "\n$pad    ";
        $out .= '<roles>'.htmlentities($this->m_roles).'</roles>';
        if($this->m_person_name_given !== false)
        {
            if($neat) $out .= "\n$pad    ";
            $out .= '<person_name_given>'.htmlentities($this->m_person_name_given).'</person_name_given>';
        }
        if($this->m_person_name_family !== false)
        {
            if($neat) $out .= "\n$pad    ";
            $out .= '<person_name_family>'.htmlentities($this->m_person_name_family).'</person_name_family>';
        }
        if($this->m_person_contact_email_primary !== false)
        {
            if($neat) $out .= "\n$pad    ";
            $out .= '<person_contact_email_primary>'.htmlentities($this->m_person_contact_email_primary).'</person_contact_email_primary>';
        }
        if($this->m_lis_result_sourcedid !== false)
        {
            if($neat) $out .= "\n$pad    ";
            $out .= '<lis_result_sourcedid>'.htmlentities($this->m_lis_result_sourcedid).'</lis_result_sourcedid>';
        }
        if($neat) $out .= "\n$pad";
        $out .= '</member>';
        return $out;
    }

};

//imsx_POXEnvelopeRequest
class cls_imsx_POXEnvelopeRequest //extends
{
	//vars for attributes
	//vars for elements
	var $m_imsx_POXHeader;
	var $m_imsx_POXBody;

    function cls_imsx_POXEnvelopeRequest($xml=false)
    {
//initialise
        if(is_string($xml))
            $xml=new SimpleXMLElement($xml);
        if($xml)
            $this->parseIn($xml);
    }

    function parseIn($xml)
    {
        foreach($xml->xpath("*") as $cxml)
        {
            $ename = $cxml->getName();
            switch($ename)
            {
                case 'imsx_POXHeader':
                    if($this->m_imsx_POXHeader==false)
                        $this->m_imsx_POXHeader = new cls_imsx_POXHeader($cxml);
                    break;
                case 'imsx_POXBody':
                    if($this->m_imsx_POXBody==false)
                        $this->m_imsx_POXBody = new cls_imsx_POXBody($cxml);
                    break;
            }
        }
    }

    function toXML($neat=false, $indent=0)
    {
        if($neat)
            $pad = str_repeat('    ',$indent);
        $out = '<?xml version="1.0"?>';
        if($neat) $out .= "\n$pad";
        $out .= '<imsx_POXEnvelopeRequest';
        $out .= '>';
        $out .= $this->m_imsx_POXHeader->toXML($neat, $indent+1);
        $out .= $this->m_imsx_POXBody->toXML($neat, $indent+1);
        if($neat) $out .= "\n$pad";
        $out .= '</imsx_POXEnvelopeRequest>';
        return $out;
    }

};

//imsx_POXBody
class cls_imsx_POXBody //extends
{
	//vars for attributes
	//vars for elements
	var $m_readResultRequest;
	var $m_replaceResultResponse;
	var $m_replaceResultRequest;
	var $m_deleteResultResponse;
	var $m_readResultResponse;

    function cls_imsx_POXBody($xml=false)
    {
//initialise
        $this->m_readResultRequest = false;
        $this->m_replaceResultResponse = false;
        $this->m_replaceResultRequest = false;
        $this->m_deleteResultResponse = false;
        $this->m_readResultResponse = false;
        if(is_string($xml))
            $xml=new SimpleXMLElement($xml);
        if($xml)
            $this->parseIn($xml);
    }

    function parseIn($xml)
    {
        foreach($xml->xpath("*") as $cxml)
        {
            $ename = $cxml->getName();
            switch($ename)
            {
                case 'readResultRequest':
                    if($this->m_readResultRequest==false)
                        $this->m_readResultRequest = new cls_readResultRequest($cxml);
                    break;
                case 'replaceResultResponse':
                    if($this->m_replaceResultResponse==false)
                        $this->m_replaceResultResponse = new cls_replaceResultResponse($cxml);
                    break;
                case 'replaceResultRequest':
                    if($this->m_replaceResultRequest==false)
                        $this->m_replaceResultRequest = new cls_replaceResultRequest($cxml);
                    break;
                case 'deleteResultResponse':
                    if($this->m_deleteResultResponse==false)
                        $this->m_deleteResultResponse = new cls_deleteResultResponse($cxml);
                    break;
                case 'readResultResponse':
                    if($this->m_readResultResponse==false)
                        $this->m_readResultResponse = new cls_readResultResponse($cxml);
                    break;
            }
        }
    }

    function toXML($neat=false, $indent=0)
    {
        if($neat)
        {
            $pad = str_repeat('    ',$indent);
            $out .= "\n$pad";
        }
        $out .= '<imsx_POXBody';
        $out .= '>';
        if($this->m_readResultRequest !== false)
        {
            $out .= $this->m_readResultRequest->toXML($neat, $indent+1);
        }
        if($this->m_replaceResultResponse !== false)
        {
            $out .= $this->m_replaceResultResponse->toXML($neat, $indent+1);
        }
        if($this->m_replaceResultRequest !== false)
        {
            $out .= $this->m_replaceResultRequest->toXML($neat, $indent+1);
        }
        if($this->m_deleteResultResponse !== false)
        {
            $out .= $this->m_deleteResultResponse->toXML($neat, $indent+1);
        }
        if($this->m_readResultResponse !== false)
        {
            $out .= $this->m_readResultResponse->toXML($neat, $indent+1);
        }
        if($neat) $out .= "\n$pad";
        $out .= '</imsx_POXBody>';
        return $out;
    }

};

//imsx_POXHeader
class cls_imsx_POXHeader //extends
{
	//vars for attributes
	//vars for elements
	var $m_imsx_POXResponseHeaderInfo;
	var $m_imsx_POXRequestHeaderInfo;

    function cls_imsx_POXHeader($xml=false)
    {
//initialise
        $this->m_imsx_POXResponseHeaderInfo = false;
        $this->m_imsx_POXRequestHeaderInfo = false;
        if(is_string($xml))
            $xml=new SimpleXMLElement($xml);
        if($xml)
            $this->parseIn($xml);
    }

    function parseIn($xml)
    {
        foreach($xml->xpath("*") as $cxml)
        {
            $ename = $cxml->getName();
            switch($ename)
            {
                case 'imsx_POXResponseHeaderInfo':
                    if($this->m_imsx_POXResponseHeaderInfo==false)
                        $this->m_imsx_POXResponseHeaderInfo = new cls_imsx_POXResponseHeaderInfo($cxml);
                    break;
                case 'imsx_POXRequestHeaderInfo':
                    if($this->m_imsx_POXRequestHeaderInfo==false)
                        $this->m_imsx_POXRequestHeaderInfo = new cls_imsx_POXRequestHeaderInfo($cxml);
                    break;
            }
        }
    }

    function toXML($neat=false, $indent=0)
    {
        if($neat)
        {
            $pad = str_repeat('    ',$indent);
            $out .= "\n$pad";
        }
        $out .= '<imsx_POXHeader';
        $out .= '>';
        if($this->m_imsx_POXResponseHeaderInfo !== false)
        {
            $out .= $this->m_imsx_POXResponseHeaderInfo->toXML($neat, $indent+1);
        }
        if($this->m_imsx_POXRequestHeaderInfo !== false)
        {
            $out .= $this->m_imsx_POXRequestHeaderInfo->toXML($neat, $indent+1);
        }
        if($neat) $out .= "\n$pad";
        $out .= '</imsx_POXHeader>';
        return $out;
    }

};

//readResultRequest
class cls_readResultRequest //extends
{
	//vars for attributes
	//vars for elements
	var $m_resultRecord;

    function cls_readResultRequest($xml=false)
    {
//initialise
        if(is_string($xml))
            $xml=new SimpleXMLElement($xml);
        if($xml)
            $this->parseIn($xml);
    }

    function parseIn($xml)
    {
        foreach($xml->xpath("*") as $cxml)
        {
            $ename = $cxml->getName();
            switch($ename)
            {
                case 'resultRecord':
                    if($this->m_resultRecord==false)
                        $this->m_resultRecord = new cls_resultRecord($cxml);
                    break;
            }
        }
    }

    function toXML($neat=false, $indent=0)
    {
        if($neat)
        {
            $pad = str_repeat('    ',$indent);
            $out .= "\n$pad";
        }
        $out .= '<readResultRequest';
        $out .= '>';
        $out .= $this->m_resultRecord->toXML($neat, $indent+1);
        if($neat) $out .= "\n$pad";
        $out .= '</readResultRequest>';
        return $out;
    }

};

//replaceResultRequest
class cls_replaceResultRequest //extends
{
	//vars for attributes
	//vars for elements
	var $m_resultRecord;

    function cls_replaceResultRequest($xml=false)
    {
//initialise
        if(is_string($xml))
            $xml=new SimpleXMLElement($xml);
        if($xml)
            $this->parseIn($xml);
    }

    function parseIn($xml)
    {
        foreach($xml->xpath("*") as $cxml)
        {
            $ename = $cxml->getName();
            switch($ename)
            {
                case 'resultRecord':
                    if($this->m_resultRecord==false)
                        $this->m_resultRecord = new cls_resultRecord($cxml);
                    break;
            }
        }
    }

    function toXML($neat=false, $indent=0)
    {
        if($neat)
        {
            $pad = str_repeat('    ',$indent);
            $out .= "\n$pad";
        }
        $out .= '<replaceResultRequest';
        $out .= '>';
        $out .= $this->m_resultRecord->toXML($neat, $indent+1);
        if($neat) $out .= "\n$pad";
        $out .= '</replaceResultRequest>';
        return $out;
    }

};

//imsx_POXRequestHeaderInfo
class cls_imsx_POXRequestHeaderInfo //extends
{
	//vars for attributes
	//vars for elements
	var $m_imsx_version;
	var $m_imsx_messageIdentifier;

    function cls_imsx_POXRequestHeaderInfo($xml=false)
    {
//initialise
        if(is_string($xml))
            $xml=new SimpleXMLElement($xml);
        if($xml)
            $this->parseIn($xml);
    }

    function parseIn($xml)
    {
        foreach($xml->xpath("*") as $cxml)
        {
            $ename = $cxml->getName();
            switch($ename)
            {
                case 'imsx_version':
                    if($this->m_imsx_version==false)
                        $this->m_imsx_version = ($cxml);
                    break;
                case 'imsx_messageIdentifier':
                    if($this->m_imsx_messageIdentifier==false)
                        $this->m_imsx_messageIdentifier = strval($cxml);
                    break;
            }
        }
    }

    function toXML($neat=false, $indent=0)
    {
        if($neat)
        {
            $pad = str_repeat('    ',$indent);
            $out .= "\n$pad";
        }
        $out .= '<imsx_POXRequestHeaderInfo';
        $out .= '>';
        if($neat) $out .= "\n$pad    ";
        $out .= '<imsx_version>'.htmlentities($this->m_imsx_version).'</imsx_version>';
        if($neat) $out .= "\n$pad    ";
        $out .= '<imsx_messageIdentifier>'.htmlentities($this->m_imsx_messageIdentifier).'</imsx_messageIdentifier>';
        if($neat) $out .= "\n$pad";
        $out .= '</imsx_POXRequestHeaderInfo>';
        return $out;
    }

};

//resultRecord
class cls_resultRecord //extends
{
	//vars for attributes
	//vars for elements
	var $m_resultScore;
	var $m_sourcedGUID;

    function cls_resultRecord($xml=false)
    {
//initialise
        if(is_string($xml))
            $xml=new SimpleXMLElement($xml);
        if($xml)
            $this->parseIn($xml);
    }

    function parseIn($xml)
    {
        foreach($xml->xpath("*") as $cxml)
        {
            $ename = $cxml->getName();
            switch($ename)
            {
                case 'resultScore':
                    if($this->m_resultScore==false)
                        $this->m_resultScore = new cls_resultScore($cxml);
                    break;
                case 'sourcedGUID':
                    if($this->m_sourcedGUID==false)
                        $this->m_sourcedGUID = new cls_sourcedGUID($cxml);
                    break;
            }
        }
    }

    function toXML($neat=false, $indent=0)
    {
        if($neat)
        {
            $pad = str_repeat('    ',$indent);
            $out .= "\n$pad";
        }
        $out .= '<resultRecord';
        $out .= '>';
        $out .= $this->m_resultScore->toXML($neat, $indent+1);
        $out .= $this->m_sourcedGUID->toXML($neat, $indent+1);
        if($neat) $out .= "\n$pad";
        $out .= '</resultRecord>';
        return $out;
    }

};

//sourcedGUID
class cls_sourcedGUID //extends
{
	//vars for attributes
	//vars for elements
	var $m_sourcedId;

    function cls_sourcedGUID($xml=false)
    {
//initialise
        if(is_string($xml))
            $xml=new SimpleXMLElement($xml);
        if($xml)
            $this->parseIn($xml);
    }

    function parseIn($xml)
    {
        foreach($xml->xpath("*") as $cxml)
        {
            $ename = $cxml->getName();
            switch($ename)
            {
                case 'sourcedId':
                    if($this->m_sourcedId==false)
                        $this->m_sourcedId = strval($cxml);
                    break;
            }
        }
    }

    function toXML($neat=false, $indent=0)
    {
        if($neat)
        {
            $pad = str_repeat('    ',$indent);
            $out .= "\n$pad";
        }
        $out .= '<sourcedGUID';
        $out .= '>';
        if($neat) $out .= "\n$pad    ";
        $out .= '<sourcedId>'.htmlentities($this->m_sourcedId).'</sourcedId>';
        if($neat) $out .= "\n$pad";
        $out .= '</sourcedGUID>';
        return $out;
    }

};

//resultScore
class cls_resultScore //extends
{
	//vars for attributes
	//vars for elements
	var $m_textString;
	var $m_language;

    function cls_resultScore($xml=false)
    {
//initialise
        if(is_string($xml))
            $xml=new SimpleXMLElement($xml);
        if($xml)
            $this->parseIn($xml);
    }

    function parseIn($xml)
    {
        foreach($xml->xpath("*") as $cxml)
        {
            $ename = $cxml->getName();
            switch($ename)
            {
                case 'textString':
                    if($this->m_textString==false)
                        $this->m_textString = floatval($cxml);
                    break;
                case 'language':
                    if($this->m_language==false)
                        $this->m_language = strval($cxml);
                    break;
            }
        }
    }

    function toXML($neat=false, $indent=0)
    {
        if($neat)
        {
            $pad = str_repeat('    ',$indent);
            $out .= "\n$pad";
        }
        $out .= '<resultScore';
        $out .= '>';
        if($neat) $out .= "\n$pad    ";
        $out .= '<textString>'.htmlentities($this->m_textString).'</textString>';
        if($neat) $out .= "\n$pad    ";
        $out .= '<language>'.htmlentities($this->m_language).'</language>';
        if($neat) $out .= "\n$pad";
        $out .= '</resultScore>';
        return $out;
    }

};

//imsx_POXEnvelopeResponse
class cls_imsx_POXEnvelopeResponse //extends
{
	//vars for attributes
	//vars for elements
	var $m_imsx_POXBody;
	var $m_imsx_POXHeader;

    function cls_imsx_POXEnvelopeResponse($xml=false)
    {
//initialise
		$this->m_imsx_POXBody=false;
		$this->m_imsx_POXHeader=false;
		try
        {
	        if((is_string($xml))&&(strpos(trim($xml),'<')===0))
            {
	            $xml=new SimpleXMLElement($xml);
	        	if($xml)
	            	$this->parseIn($xml);
            }
        }
        catch(Exception $e)
        {
        	echo $e->getMessage();
		}
    }

    function parseIn($xml)
    {
        foreach($xml->xpath("*") as $cxml)
        {
            $ename = $cxml->getName();
            switch($ename)
            {
                case 'imsx_POXBody':
                    if($this->m_imsx_POXBody==false)
                        $this->m_imsx_POXBody = new cls_imsx_POXBody($cxml);
                    break;
                case 'imsx_POXHeader':
                    if($this->m_imsx_POXHeader==false)
                        $this->m_imsx_POXHeader = new cls_imsx_POXHeader($cxml);
                    break;
            }
        }
    }

    function toXML($neat=false, $indent=0)
    {
        if($neat)
            $pad = str_repeat('    ',$indent);
        $out = '<?xml version="1.0"?>';
        if($neat) $out .= "\n$pad";
        $out .= '<imsx_POXEnvelopeResponse';
        $out .= '>';
        $out .= $this->m_imsx_POXBody->toXML($neat, $indent+1);
        $out .= $this->m_imsx_POXHeader->toXML($neat, $indent+1);
        if($neat) $out .= "\n$pad";
        $out .= '</imsx_POXEnvelopeResponse>';
        return $out;
    }

};

//replaceResultResponse
class cls_replaceResultResponse //extends
{
	//vars for attributes
	//vars for elements

    function cls_replaceResultResponse($xml=false)
    {
//initialise
        if(is_string($xml))
            $xml=new SimpleXMLElement($xml);
        if($xml)
            $this->parseIn($xml);
    }

    function parseIn($xml)
    {
        foreach($xml->xpath("*") as $cxml)
        {
            $ename = $cxml->getName();
            switch($ename)
            {
            }
        }
    }

    function toXML($neat=false, $indent=0)
    {
        if($neat)
        {
            $pad = str_repeat('    ',$indent);
            $out .= "\n$pad";
        }
        $out .= '<replaceResultResponse';
        $out .= '>';
        if($neat) $out .= "\n$pad";
        $out .= '</replaceResultResponse>';
        return $out;
    }

};

//deleteResultResponse
class cls_deleteResultResponse //extends
{
	//vars for attributes
	//vars for elements

    function cls_deleteResultResponse($xml=false)
    {
//initialise
        if(is_string($xml))
            $xml=new SimpleXMLElement($xml);
        if($xml)
            $this->parseIn($xml);
    }

    function parseIn($xml)
    {
        foreach($xml->xpath("*") as $cxml)
        {
            $ename = $cxml->getName();
            switch($ename)
            {
            }
        }
    }

    function toXML($neat=false, $indent=0)
    {
        if($neat)
        {
            $pad = str_repeat('    ',$indent);
            $out .= "\n$pad";
        }
        $out .= '<deleteResultResponse';
        $out .= '>';
        if($neat) $out .= "\n$pad";
        $out .= '</deleteResultResponse>';
        return $out;
    }

};

//readResultResponse
class cls_readResultResponse //extends
{
	//vars for attributes
	//vars for elements
	var $m_resultScore;

    function cls_readResultResponse($xml=false)
    {
//initialise
        if(is_string($xml))
            $xml=new SimpleXMLElement($xml);
        if($xml)
            $this->parseIn($xml);
    }

    function parseIn($xml)
    {
        foreach($xml->xpath("*") as $cxml)
        {
            $ename = $cxml->getName();
            switch($ename)
            {
                case 'resultScore':
                    if($this->m_resultScore==false)
                        $this->m_resultScore = new cls_resultScore($cxml);
                    break;
            }
        }
    }

    function toXML($neat=false, $indent=0)
    {
        if($neat)
        {
            $pad = str_repeat('    ',$indent);
            $out .= "\n$pad";
        }
        $out .= '<readResultResponse';
        $out .= '>';
        $out .= $this->m_resultScore->toXML($neat, $indent+1);
        if($neat) $out .= "\n$pad";
        $out .= '</readResultResponse>';
        return $out;
    }

};

//imsx_POXResponseHeaderInfo
class cls_imsx_POXResponseHeaderInfo //extends
{
	//vars for attributes
	//vars for elements
	var $m_imsx_version;
	var $m_imsx_messageIdentifier;
	var $m_imsx_statusInfo;

    function cls_imsx_POXResponseHeaderInfo($xml=false)
    {
//initialise
        if(is_string($xml))
            $xml=new SimpleXMLElement($xml);
        if($xml)
            $this->parseIn($xml);
    }

    function parseIn($xml)
    {
        foreach($xml->xpath("*") as $cxml)
        {
            $ename = $cxml->getName();
            switch($ename)
            {
                case 'imsx_version':
                    if($this->m_imsx_version==false)
                        $this->m_imsx_version = strval($cxml);
                    break;
                case 'imsx_messageIdentifier':
                    if($this->m_imsx_messageIdentifier==false)
                        $this->m_imsx_messageIdentifier = strval($cxml);
                    break;
                case 'imsx_statusInfo':
                    if($this->m_imsx_statusInfo==false)
                        $this->m_imsx_statusInfo = new cls_imsx_statusInfo($cxml);
                    break;
            }
        }
    }

    function toXML($neat=false, $indent=0)
    {
        if($neat)
        {
            $pad = str_repeat('    ',$indent);
            $out .= "\n$pad";
        }
        $out .= '<imsx_POXResponseHeaderInfo';
        $out .= '>';
        if($neat) $out .= "\n$pad    ";
        $out .= '<imsx_version>'.htmlentities($this->m_imsx_version).'</imsx_version>';
        if($neat) $out .= "\n$pad    ";
        $out .= '<imsx_messageIdentifier>'.htmlentities($this->m_imsx_messageIdentifier).'</imsx_messageIdentifier>';
        $out .= $this->m_imsx_statusInfo->toXML($neat, $indent+1);
        if($neat) $out .= "\n$pad";
        $out .= '</imsx_POXResponseHeaderInfo>';
        return $out;
    }

};

//imsx_statusInfo
class cls_imsx_statusInfo //extends
{
	//vars for attributes
	//vars for elements
	var $m_imsx_codeMajor;
	var $m_imsx_description;
	var $m_imsx_severity;
	var $m_imsx_messageRefIdentifier;
	var $m_imsx_operationRefIdentifier;

    function cls_imsx_statusInfo($xml=false)
    {
//initialise
        if(is_string($xml))
            $xml=new SimpleXMLElement($xml);
        if($xml)
            $this->parseIn($xml);
    }

    function parseIn($xml)
    {
        foreach($xml->xpath("*") as $cxml)
        {
            $ename = $cxml->getName();
            switch($ename)
            {
                case 'imsx_codeMajor':
                    if($this->m_imsx_codeMajor==false)
                        $this->m_imsx_codeMajor = strval($cxml);
                    break;
                case 'imsx_description':
                    if($this->m_imsx_description==false)
                        $this->m_imsx_description = strval($cxml);
                    break;
                case 'imsx_severity':
                    if($this->m_imsx_severity==false)
                        $this->m_imsx_severity = strval($cxml);
                    break;
                case 'imsx_messageRefIdentifier':
                    if($this->m_imsx_messageRefIdentifier==false)
                        $this->m_imsx_messageRefIdentifier = strval($cxml);
                    break;
                case 'imsx_operationRefIdentifier':
                    if($this->m_imsx_operationRefIdentifier==false)
                        $this->m_imsx_operationRefIdentifier = strval($cxml);
                    break;
            }
        }
    }

    function toXML($neat=false, $indent=0)
    {
        if($neat)
        {
            $pad = str_repeat('    ',$indent);
            $out .= "\n$pad";
        }
        $out .= '<imsx_statusInfo';
        $out .= '>';
        if($neat) $out .= "\n$pad    ";
        $out .= '<imsx_codeMajor>'.htmlentities($this->m_imsx_codeMajor).'</imsx_codeMajor>';
        if($neat) $out .= "\n$pad    ";
        $out .= '<imsx_description>'.htmlentities($this->m_imsx_description).'</imsx_description>';
        if($neat) $out .= "\n$pad    ";
        $out .= '<imsx_severity>'.htmlentities($this->m_imsx_severity).'</imsx_severity>';
        if($neat) $out .= "\n$pad    ";
        $out .= '<imsx_messageRefIdentifier>'.htmlentities($this->m_imsx_messageRefIdentifier).'</imsx_messageRefIdentifier>';
        if($neat) $out .= "\n$pad    ";
        $out .= '<imsx_operationRefIdentifier>'.htmlentities($this->m_imsx_operationRefIdentifier).'</imsx_operationRefIdentifier>';
        if($neat) $out .= "\n$pad";
        $out .= '</imsx_statusInfo>';
        return $out;
    }

};
?>
