<?php
define('FORM_NOTSUBMITTED',0);
define('FORM_SUBMITTED_VALID', 1);
define('FORM_SUBMITTED_INVALID', 2);
define('FORM_CANCELED',3);

abstract class nbform
{
	private $inFieldset;
    protected $formStatus;

    function __construct()
    {
    	$this->status = FORM_NOTSUBMITTED;
    }

    function getStatus()
    {
    	return $this->formStatus;
    }

	function formStart($target=false, $method='POST')
	{
    	$this->inFieldset = false;
		if(!$target)
	    	$target = $_SERVER['PHP_SELF'];
	    $out = '<form class="form-horizontal" action="'.$target.'" method="'.$method.'" enctype="multipart/form-data">';
	    return $out;
	}

	function formEnd($showSubmit=true, $showCancel=false)    //# include submit/cancel
	{
		
    	$out = '';
    	
    	if($showSubmit && $showCancel) {
	    	$width = 'col-sm-4';
    	} else {
	    	$width = 'col-sm-8';
    	}
    	
    	if($showSubmit|$showCancel)
        {
            $out .= "<div class=\"form-group\"><div class=\"col-sm-4\"></div>";
    		if($showSubmit)
	            $out .= '<div class="'.$width.'"><input class="submit btn btn-primary btn-block" name="submit" type="submit" value="Submit" /></div>';
    		if($showCancel)
	            $out .= '<div class="'.$width.'"><input class="submit btn btn-default btn-block" name="submit" type="submit" value="Cancel" /></div>';
            $out .= "</div></div></div>";
        }
        if($this->inFieldset)
        	$out .= '</fieldset>';
	    $out .= '</form>';
        return $out;
	}

	function submitInput($name, $value1, $value2=null)
	{
	    $out .= "<div class=\"form-group\"><div class=\"col-sm-4\"></div>";
	    
	    if($value2) {
	    	$width = 'col-sm-4';
    	} else {
	    	$width = 'col-sm-8';
    	}
	    
	    $out .= '<div class="'.$width.'"><input class="submit btn btn-primary btn-block" name="'.$name.'" type="submit" value="'.$value1.'" /></div>';

  		if($value2)
            $out .= '<div class="'.$width.'"><input class="submit btn btn-default btn-block" name="'.$name.'" type="submit" value="'.$value2.'" /></div>';
        $out .= "</div>";;
	    return $out;
	}

	function getFormInput($name, $default=false)
	{
		if(isset($_REQUEST[$name]))
			return trim(strip_tags($_REQUEST[$name]));
	    else
	    	return $default;
	}

	function textInput($caption, $name, $value="", $validateMsgs=null, $width=40, $required=false)
	{
        $out = "<div class=\"form-group";
         if((is_array($validateMsgs))&&(array_key_exists($name, $validateMsgs)))
            $out .= " has-error";
        $out .= "\">";
        $out .= "<label class=\"control-label col-sm-4\" for=\"$name\">$caption";
        if($required)
	    	$out .= "<span class=\"rq\"*</span>";
        $out .= '</label>';
        $out .= "<div class=\"col-sm-8\"><span class=\"forminput\"><input type=\"text\" class=\"form-control\" name=\"$name\" value=\"$value\" size=\"$width\" /></span></div>";
	    if((is_array($validateMsgs))&&(array_key_exists($name, $validateMsgs)))
            $out .= "<p class=\"help-block\">{$validateMsgs[$name]}</p>";
        $out .= "</div>";
	    return $out;
	}

	function fileUploadInput($caption, $name, $validateMsgs=null, $required=false)
	{
	    $out = "<div class=\"form-group\">";
	    $out .= "<label class=\"control-label col-sm-4\" for=\"$name\">$caption";
        if($required)
	    	$out .= "<span class=\"rq\"*</span>";
        $out .= '</label>';
	    if((is_array($validateMsgs))&&(array_key_exists($name, $validateMsgs)))
	        $out .= "<span class=\"errormsg\">{$validateMsgs[$name]}</span>";
	    $out .= '</label>';
	    $out .= "<div class=\"col-sm-8\"><span class=\"forminput\"><input type=\"file\" class=\"form-control\" name=\"$name\"/></span></div>";
        if((is_array($validateMsgs))&&(array_key_exists($name, $validateMsgs)))
            $out .= "<p class=\"help-block\">{$validateMsgs[$name]}</p>";
        $out .= "</div>";
	    return $out;
	}

	function hiddenInput($name, $value)
	{
	    $out = "<input type=\"hidden\" name=\"$name\" value=\"$value\" />";
	    return $out;
	}

	function startFieldset($legend=null)
	{
        if($this->inFieldset)
        	$out = '</fieldset><fieldset>';
        else
	    	$out = '<fieldset>';
        if($legend)
        	$out .= '<legend>'.$legend.'</legend>';
        $this->inFieldset = true;
	    return $out;
	}

    //# Should also have an option of checkboxGroupInput that does several at once
	function checkboxInput($caption, $name, $checked=false, $validateMsgs=null, $required=false)
	{
	    $out = "<div class=\"form-group\">";
	    $out .= "<div class=\"col-sm-8 col-sm-push-4\"><div class=\"checkbox\">";
	    $out .= "<label for=\"$name\"><input type=\"checkbox\" name=\"$name\" value=\"1\"";
	    if($checked)
	    	$out .= ' checked="1" ';
	    $out .= "/>$caption";
        if($required)
	    	$out .= "<span class=\"rq\"*</span>";
        $out .= '</label>';
        
	    if((is_array($validateMsgs))&&(array_key_exists($name, $validateMsgs)))
	        $out .= "<span class=\"errormsg\">{$validateMsgs[$name]}</span>";
	    $out .= "</div></div></div>";
	    return $out;
	}

    //# Should really replace this with radioGroupInput that does several at once.
	function radioInput($caption, $name, $value, $checked=false, $validateMsgs=null)
	{
	    $out = "<div class=\"formfield\">";
	    $out .= "<label for=\"$name\">$caption:";
	    if((is_array($validateMsgs))&&(array_key_exists($name, $validateMsgs)))
	        $out .= "<span class=\"errormsg\">{$validateMsgs[$name]}</span>";
	    $out .= '</label>';
	    $out .= "<br/><span class=\"forminput\"><input type=\"radio\" name=\"$name\" value=\"$value\" ";
	    if($checked)
	    	$out .= 'checked="1" ';
	    $out .= "/></span></div>\n";
	    return $out;
	}

	function radioGroupInput($caption, $name, $options, $value="", $required=false)
	{
    	$out = '';
		if(strlen($caption))
        {
	    	$out .= '<fieldset><legend>'.$caption.'</legend>';
        }

        $out .= 'Not yet implemented, use a select input for now. ';
        
		if(strlen($caption))
        {
	    	$out .= '</fieldset>';
        }
	    return $out;
	}

	function textareaInput($caption, $name, $value="", $validateMsgs=null, $width=30, $height=3, $required=false)
	{
	    
	    $out = "<div class=\"form-group";
         if((is_array($validateMsgs))&&(array_key_exists($name, $validateMsgs)))
            $out .= " has-error";
        $out .= "\">";
        $out .= "<label class=\"control-label col-sm-4\" for=\"$name\">$caption";
        if($required)
	    	$out .= "<span class=\"rq\"*</span>";
        $out .= '</label>';
        $out .= "<div class=\"col-sm-8\"><span class=\"forminput\"><textarea class=\"form-control\" name=\"$name\" cols=\"$width\" rows=\"$height\"/>";
        $out .= $value.'</textarea></span></div>';
	    if((is_array($validateMsgs))&&(array_key_exists($name, $validateMsgs)))
            $out .= "<p class=\"help-block\">{$validateMsgs[$name]}</p>";
        $out .= "</div>";
	    return $out;
	}

	function passwordInput($caption, $name, $value="", $validateMsgs=null, $width=12, $required=false)
	{
	    $out = "<div class=\"formfield\">";
        if($required)
	    	$out .= "<label for=\"$name\" style=\"color: Red;\">$caption: *";
        else
	    	$out .= "<label for=\"$name\">$caption:";
	    if((is_array($validateMsgs))&&(array_key_exists($name, $validateMsgs)))
	        $out .= "<span class=\"errormsg\">{$validateMsgs[$name]}</span>";
	    $out .= '</label>';
	    $out .= "<br/><span class=\"forminput\"><input type=\"password\" name=\"$name\" value=\"$value\" size=\"$width\" /></span></div>\n";
	    return $out;
	}

	function selectListInput($caption, $name, $options, $value="", $required=false)
	{
	    $out = "<div class=\"form-group\">";
        $out .= "<label class=\"control-label col-sm-4\" for=\"$name\">$caption";
        if($required)
	    	$out .= "<span class=\"rq\"*</span>";
        $out .= '</label>';
	    $out .= "<div class=\"col-sm-8\"><span class=\"forminput\">";
	    if(!is_array($options))
	        $options = explode(",",$options);
	    $out .= "<select class=\"form-control\" name=\"$name\">\n";
        foreach($options as $key => $val)
        {
	        $out .= "<option";
	        if((is_integer($key))&&(strpos($val,":")))
	            list($nm, $v) = explode(":",$val,2);
	        else
            {
	            $nm = $val;
                $v = $key;
            }
	        if(trim($v)==trim($value))
	            $out .= " selected=\"1\"";
	        $out .= " value='$v'>{$nm}</option>\n";

	    }
	    $out .= "</select></span></div></div>\n";
	    return $out;
	}

    function groupStart($legend=null)
    {
    	$out = '<fieldset>';
        if($legend !== null)
        	$out .= "<legend>$legend</legend>";
        return $out;
    }

    function groupEnd()
    {
    	return '</fieldset>';
    }

	function dateInput($caption, $name, $value=null, $validateMsgs=null)
	{
	    if($value!==null)
	        $strvalue = date2form($value);
	    else
	        $strvalue='';
	    $out = '<div class="formfield">';
	    $out .= '<label for="'.$name.'">'.$caption.': ';
	    if((is_array($validateMsgs))&&(array_key_exists($name, $validateMsgs)))
	        $out .= "<span class=\"errormsg\">{$validateMsgs[$name]}</span>";
	    $out .= '</label>';
	    $out .= "<span class=\"forminput\"><input type=\"text\" name=\"$name\" value=\"$strvalue\" /> (dd/mm/yyyy)";
	    $out .= "</span></div>";
	    return $out;
	}

	function form2date($in)
	{
	    $in2 = preg_replace('/(\d+)\/(\d+)\/(\d+)/i','${2}/${1}/${3}', $in);
	    return strtotime($in2);
	}

	function date2form($in)
	{
	    return strftime("%d/%m/%Y", $in);
	}
}

?>
