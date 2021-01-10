<?php
/*
    Copyright (C) 2003-2021 Young Consulting, Inc

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

require_once("config.php");
require_once("mainfile.php");

//$dbi=OpenDatabase($dbhost, $dbuname, $dbpass, $dbname);
$dbi=OpenDatabase($db_config);

require_once ("banner.php");
//require_once ("nav.php");

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head><title><?php echo "$pagetitle"; ?></title>
<?php
$HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
unset($BROWSER_AGENT);
unset($BROWSER_VER);

if (preg_match( '|MSIE ([0-9].[0-9]{1,2})|',$HTTP_USER_AGENT,$log_version)) { 
  $BROWSER_VER=$log_version[1]; 
  $BROWSER_AGENT='IE'; 
} elseif (preg_match( '|Opera ([0-9].[0-9]{1,2})|',$HTTP_USER_AGENT,$log_version)) { 
  $BROWSER_VER=$log_version[1]; 
  $BROWSER_AGENT='OPERA'; 
} elseif (preg_match( '|Firefox/([0-9\.]+)|',$HTTP_USER_AGENT,$log_version)) { 
  $BROWSER_VER=$log_version[1]; 
  $BROWSER_AGENT='FIREFOX'; 
} elseif (preg_match( '|Safari/([0-9\.]+)|',$HTTP_USER_AGENT,$log_version)) { 
  $BROWSER_VER=$log_version[1]; 
  $BROWSER_AGENT='SAFARI'; 
} else { 
  $BROWSER_VER=0; 
  $BROWSER_AGENT='OTHER'; 
} 
if ($BROWSER_AGENT=='IE') {
        echo "<meta http-equiv=\"X-UA-Compatible\" content=\"IE=10\" />\n";
        $_ERROR="<font size=1 color=red>**Microsoft IE is known to have issues with this web site</font>";
}

if (!isset($_SERVER['HTTPS'])) {
	if (extension_loaded('openssl')) {
		$redirect = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		header("Location: $redirect");
	} else {
		$_MESSAGE = "Please configure and load SSL module";
	}
}
?>
<meta name="viewport" contact="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="css/style.css">
<link rel="stylesheet" type="text/css" href="css/style-addition.css">
<link rel="stylesheet" type="text/css" href="js/jquery-ui-1.12.1/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="js/jquery-ui-1.12.1/jquery-ui.theme.css">
<!-- <link rel="stylesheet" type="text/css" href="js/jquery-ui-1.12.1/jquery-ui.datetimepicker.css"> -->
<link rel="stylesheet" type="text/css" href="js/jquery-ui-1.12.1/jquery-ui-timepicker-addon.css">
<?php
if ($BROWSER_AGENT=='IE') {
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"css/style_ie.css\" />\n";
}
?>
<style>
label {
  display: inline-block;
  width: 5em;
}
</style>
</head>
<body class="top_page">
<?php //- <body bgcolor="#F0F0F0" text="#000000" link="0000FF"> ?>
<script language="JavaScript" src="js/ajax.js"></script>
<script language="JavaScript" src="js/tacacs.js"></script>
<script type="text/javascript" src="js/jquery-2.2.4.js"></script>
<!-- <script type="text/javascript" src="js/jquery.validate.min.js"></script> -->
<script type="text/javascript" src="js/jquery-ui-1.12.1/jquery-ui.js"></script>
<!-- <script type="text/javascript" src="js/jquery-ui-1.12.1/jquery-ui.datetimepicker.js"></script> -->
<script type="text/javascript" src="js/jquery-ui-1.12.1/jquery-ui-timepicker-addon.js"></script>
<script type="text/javaScript" src="js/webtool.js"></script>
<script language="JavaScript"> var process_prov = <?php echo $prov_config->{'process_prov'}; ?>; </script>
<?php
   if ($_login) {
	if (($_ret=Login($username,$password,$dbi)) > 0) {
		setcookie("login",$_crypt_uname, time()+(3600*$etime));
		setcookie("uname",$username, time()+(3600*$etime));
	}
   } else {
	if (isset($_COOKIE["login"])) {
   		$_ret = checkLogin($_COOKIE["login"], $dbi);
		setcookie("login",$_COOKIE["login"], time()+(3600*$etime));
		setcookie("uname",$_COOKIE["uname"], time()+(3600*$etime));
	} else { $_ret = 0; }
   }
?>

<table id="tools" border=0 cellspacing=0 cellpadding=0 width=850>
<tr><td>
<fieldset class="_collapsible"><div id="banner">
<?php Banner(); ?>
</div></fieldset>
<tr><td>
<div class="topnav" id="topnav">
<table border="0">
<tr>
<?php
if ($_ret) {
	echo "<td class=\"dropdown\"><a class=\"topnav-item nav-button ";
	if (!isset($menu) || ($menu=="main")) { echo "active";}
	echo "\" href=\"?menu=main\">Dashboard</a></td>"
	    . "<td class=\"dropdown\"><a class=\"topnav-item nav-button dropbtn ";
	if (isset($menu) && $menu=="report") { echo "active";}
	echo "\" href=\"javascript:void(0);\">Reports</a>\n";
	echo "<div class=\"dropdown-content\" style=\"margin-top: 35px;\">"
            ."<a href=\"?menu=report&module=access\">Access</a>"
	    ."<a href=\"?menu=report&module=account\">Accounting</a>";
	if ($_ret>=15) {
		echo "<a href=\"?menu=report&module=audit\">Audit</a>";
	}
	echo "</div></td>\n";
} else {
	echo "<td class=\"dropdown\"><a class=\"topnav-item nav-button ";
	if (!isset($menu) || ($menu=="main")) { echo "active";}
	echo "\" href=\"?menu=main\">Home</a></td>";
	echo "<td><a class=\"topnav-item nav-button ";
	if (isset($menu) && $menu=="change") { echo "active";}
	echo "\" href=\"?menu=change&module=change\">Change Password</a></td>";
	echo "<td><a class=\"topnav-item nav-button ";
	if (isset($menu) && $menu=="verify") { echo "active";}
	echo "\" href=\"?menu=verify&module=verify\">Verify Password</a></td>";
}
if ($_ret>=5) {
	echo "<td class=\"dropdown\"><a class=\"topnav-item nav-button dropbtn ";
	if (isset($menu) && $menu=="admin") { echo "active";}
	echo "\" href=\"javascript:void(0);\">Admin</a>\n";
	echo "<div class=\"dropdown-content\" style=\"margin-top: 35px;\">"
            ."<a href=\"?menu=admin&module=user\">User</a>"
            ."<a href=\"?menu=admin&module=user_group\">User Group</a>"
            ."<a href=\"?menu=admin&module=nas\">NAS</a>"
            ."<a href=\"?menu=admin&module=nas_group\">NAS Group</a>"
            ."<a href=\"?menu=admin&module=profile\">Profile</a>"
            ."<a href=\"?menu=admin&module=nas_acl\">NAS Policies</a>"
            ."<a href=\"?menu=admin&module=user_acl\">User ACL</a>"
            ."<a href=\"?menu=admin&module=attrib\">Attributes</a>"
	    ."<a href=\"?menu=admin&module=command\">Commands</a>"
            ."<a href=\"?menu=admin&module=vendor\">Vendor</a>";
	echo "</div></td>\n";
}
if ($_ret>=15) {
	echo "<td class=\"dropdown\"><a class=\"topnav-item nav-button dropbtn ";
	if (isset($menu) && $menu=="system") { echo "active";}
	echo "\" href=\"javascript:void(0);\">System</a>\n";
	echo "<div class=\"dropdown-content\" style=\"margin-top: 35px;\">"
            ."<a href=\"?menu=system&module=suser\">Users</a>"
            ."<a href=\"?menu=system&module=sengine\">Engines</a>"
            ."<a href=\"?menu=system&module=sprov\">Provision</a>"
            ."<a href=\"?menu=system&module=ssite\">Site</a>"
	    ."<a href=\"?menu=system&module=spass\">Password Options</a>"
	    ."<a href=\"?menu=system&module=sbackup\">Backup/Restore</a>"
	    ."<a href=\"?menu=system&module=sload\">Load Vendor File</a>";
	echo "</div></td>\n";
}
if ($_ret) {
	$_usern = $username?$username:$_COOKIE["uname"];
	echo "<td><a class=\"topnav-item nav-button\" href=\"javascript:logoff();\">Log off<br>".$_usern."</a></td>\n";
}
?>
</tr>
</table>
</div>
<tr><td>
<table border=0 cellspacing=0 cellpadding=0 width="100%" height="250">
<tr>
<?php /*     <td width="30%" valign="top" cellpadding="0" cellspacing="0">
	<div id="nav" class="menu"><fieldset class="_collapsible"><ul>
		<?php
			include("nav.php");
		?>
	</ul></fieldset></div> */ ?>
     <td width="70%" valign="top">
	<?php
//	if ($_ret&&$module) {
	if ($_ret) {
		$nmodule=$module?$module:"main";
		if ($site_config->{'init'} && $nmodule != "ssite") {
			echo "<script> window.location.href = \"index.php?menu=system&module=ssite\"; </script>";
		}
		include ($nmodule.".php");
	} else {
		$nmodule=$module?$module:"main";
		include ($nmodule.".php");
	}?>
     </td>
</tr>
</table>
<tr> <td><br>
<table border=0 width="100%" height=25>
<tr>
	<div id="footer">
	<td width="20%">&nbsp;<?php echo $_ERROR; ?>
	<td width="60%"><center><font color="#1c5d91"><?php echo "$copyrights"; ?></font></center>
	<td width="20%">&nbsp;
	</div>
</table>
<tr> <td><center><font size=-3 color="#1c5d91">(c)<?php echo "$copyrights_dates"; ?> <a href="mailto:baram01@hotmail.com">3 Youngs</a></center>
</table>
</td></tr>
</body>
</html>
<?php
CloseDatabase($dbi);
?>
