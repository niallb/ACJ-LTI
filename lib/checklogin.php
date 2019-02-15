<?php

function checkLogin()
{
	global $ADMINUSER, $ADMINPWD, $SALT;

	if(isset($_SERVER['PHP_AUTH_USER']))
    {
    	if(($_SERVER['PHP_AUTH_USER']==$ADMINUSER)&&(md5($_SERVER['PHP_AUTH_PW'].$SALT)==$ADMINPWD))
        {
		  	return true;
        }
    }
	header('WWW-Authenticate: Basic realm="ACJ LTI Tool"');
	header('HTTP/1.0 401 Unauthorized');
    echo 'Sorry, you need a login to view this page.';
	exit();
    return false;
}

?>
