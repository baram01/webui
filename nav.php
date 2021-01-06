<?php

$tacacs_menu = array (
	array('?menu=admin&module=nas_acl','NAS ACL',true),
	array('?menu=admin&module=nas','NAS',true),
	array('?menu=admin&module=nas_group','NAS Group',true),
	array('?menu=admin&module=user_acl','User ACL',true),
	array('?menu=admin&module=user','Users',true),
	array('?menu=admin&module=user_group','User Groups',true),
	array('?menu=admin&module=profile','Profiles',true),
	array('?menu=admin&module=vendor','Vendors',true),
	array('?menu=admin&module=attrib','Attributes',true),
	array('?menu=admin&module=command','Commands',true)
);

$system_options = array (
	array('?menu=system&module=spass','Password',true),
);

$system_menu = array (
	array('?menu=system&module=suser','Users',true),
	array('?menu=system&module=sprov','Provision',true),
	array('?menu=system&module=ssite','Site',true),
	array($system_options,'Options',false)
);

$admin_menu = array (
	array($tacacs_menu,'TACACS',false),
	array($system_menu,'System',false)
);

$admin5_menu = array (
	array($tacacs_menu,'TACACS',false),
);

$report_menu = array (
	array('?menu=report&module=access','Access',true),
	array('?menu-report&module=account','Accounting',true)
);

$report15_menu = array (
	array('?menu=report&module=access','Access',true),
	array('?menu=report&module=account','Accounting',true),
	array('?menu=report&module=audit','Audit',true)
);

$main_menu = array (
	array('?menu=main&module=change','Change Password',true),
	array('?menu=main&module=verify','Verify Password',true)
);

$priv15_menu = array (
	array($admin_menu,'Administration',false),
	array($report15_menu,'Reports',false),
	array('javascript:logoff();','Logout',true)
);

$priv5_menu = array (
	array($admin5_menu,'Administration',false)
);

$priv1_menu = array (
	array($report_menu,'Reports',false),
	array('javascript:logoff();','Logout',true)
);

function Nav($menu, $submenu)
{
	global $BROWSER_AGENT;

	foreach ($menu as $mnu) {
		if ($mnu[2]) {
			echo "<li><a href=\"".$mnu[0]."\">".$mnu[1]."</a></li>\n";
		} else {
//			echo "<li><a href=\"javascript:_active('".$mnu[1]."');\">".$mnu[1]."</a>";
			echo "<li><a href=\"javascript:getPage(0);\">".$mnu[1]."</a>";
			echo "<div id=\"".$mnu[1]."\"><ul>\n";
			Nav($mnu[0],true);
			echo "</ul></div></li>\n";
		}
	}
}

Nav($main_menu, false);

switch($_ret) {
case 15:
		Nav($priv15_menu, false);
		break;
case 10:
case 5:		Nav($priv5_menu, false);
		break;
}

if (($_ret > 0) && ($_ret < 15))	Nav($priv1_menu, false);


?>

