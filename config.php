<?php
$banner_gif = "banner.gif";

$_ERROR = "";
$_MESSAGE = "";
$debugmsg = "";
$_login = 0;
$_ret = 0;
$_prvlvl = 1;
$_crypt_uname = "";
$module = 0;
$option = 0;
$group = "";
$username = "";
$password = "";
$etime = 1;
$_lmsg = "";

$_vrows = array(25,50,100,0);
/* $_auth_method = array(1=>"local", 2=>"radius", 3=>"ldap", 4=>"sldap", 5=>"securid"); */
/* $_auth_method = array(1=>"local", 3=>"ldap", 5=>"securid"); */

/****************************************************************************
** Please ONLY change the information below.
*****************************************************************************/

$ipv6_enable = 0;
$debug = 1;
$demo = 0;

$start_uid = 100;	//Starting User ID #
$start_gid = 1000000;	//Starting User Group ID #
$start_pid = 2000000;	//Starting Profile ID #

$ads = "";

if (PHP_VERSION_ID > 70000) {
	$dbtype = "mysqli";
}
?>
