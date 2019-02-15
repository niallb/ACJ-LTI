<?php

$ADMINUSER = 'learnadmin';
$ADMINPWD = '721515feadbaebf32820bce5c5cc40f9';
/*$DATAFILE = 'data.s3db';
$DATAFOLDER = '/xampp/htdocs/acj_experiments/ACJLTITool/data';
$DATAFOLDERURL = '/acj_experiments/ACJLTITool/data';
$SALT = 'hdkjshgvzcxnvlfkd';

//include_once('lib/databaseLite.php');

if(($DATAFILE=='')||($ADMINPWD==''))
	exit("You need to set up application settings in config.php before using LTIPeer.");

if(!file_exists($DATAFILE))
	initializeDataBase_lp();  */


$DBCFG['host'] = 'localhost'; // Host name
$DBCFG['username'] = 'acjlti'; // Mysql username
$DBCFG['password'] = 'acjlti'; // Mysql password
$DBCFG['db_name'] = 'acjlti'; // Database name

$DATAFOLDER = '/inetpub/vhosts/acj.haskellmooc.co.uk/httpdocs/acj/data';
$DATAFOLDERURL = '/acj/data';
$SALT = 'hdvgdfgsdfggfdgsd';

include_once('lib/database.php');



?>
