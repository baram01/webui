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

04/11/2020 - Andrew Young
	Fix bugs with UI
05/04/2019 - Andrew Young
	Add dynamic query
*/

//if (!eregi("index.php",$_SERVER['PHP_SELF'])) {
if (!preg_match("/index.php/",$_SERVER['PHP_SELF'])) {
        Header("Location: index.php");
        die();
}

if ($_ret < 5) {
        echo "<script language=\"JavaScript\"> top.location.href=\"?module=main\"; </script>";
}
?>

<script language="JavaScript" src="js/calendar3.js"></script>
<script language="Javascript" src="js/user.js"> </script>
<script language="Javascript" src="js/result.js"> </script>

<?php
if (!isset($gid)) $gid="";

switch ($option) {
  case 1:
	$result = @SQLQuery("INSERT INTO user (id, uid, gid, comment, auth, password, enable, arap, pap, chap, mschap, expires, b_author, a_author, svc_dflt, cmd_dflt, maxsess, acl_id, user) VALUES ($id, '$uid', '$gid', '$comment', $auth, ENCRYPT('$password'),ENCRYPT('$enable'),'$arap',ENCRYPT('$pap'),'$chap','$mschap','$expires','$b_author','$a_author',$svc_dflt,$cmd_dflt,$maxsess,$acl_id,2)", $dbi);
	if (!@SQLError($dbi)) {
		echo "<P><font color=\"red\"> Group($uid) added.</font></P>";
		Audit("user_group","add","UID=".$uid,$dbi);
	}
	break;
  case 2:
        $sqlcmd = "UPDATE user set comment='$comment', gid='$gid', auth=$auth, expires='$expires', disable=$disable, b_author='$b_author', a_author='$a_author', svc_dflt=$svc_dflt, cmd_dflt=$cmd_dflt, maxsess=$maxsess, acl_id=$acl_id, shell='$shell', homedir='$homedir'";
        if ($re_password) $sqlcmd .= ", password='".unixcrypt($password)."'";
       // if ($re_password) $sqlcmd .= ", password=ENCRYPT('$password')";
        if ($re_enable) $sqlcmd .= ", enable='".unixcrypt($enable)."'";
       // if ($re_enable) $sqlcmd .= ", enable=ENCRYPT('$enable')";
        if ($re_pap) $sqlcmd .= ", pap='".unixcrypt($pap)."'";
       // if ($re_pap) $sqlcmd .= ", pap=ENCRYPT('$pap')";
        if ($re_arap) $sqlcmd .= ", arap='$arap'";
        if ($re_chap) $sqlcmd .= ", chap='$chap'";
        if ($re_mschap) $sqlcmd .= ", mschap='$mschap'";
        $result = @SQLQuery("$sqlcmd WHERE uid='$uid'",$dbi);
	if (!@SQLError($dbi)) {
		echo "<P><font color=\"red\"> Group($uid) modified.</font></P>";
		Audit("user_group","change","UID=".$uid,$dbi);
	}
	break;
  case 3:
	$result = @SQLQuery("SELECT id FROM acl WHERE value='$uid'", $dbi);
	$num_acl = @SQLNumRows($result);
	$result = @SQLQuery("SELECT uid FROM user WHERE gid='$uid'", $dbi);
	$num_group = @SQLNumRows($result);
	if (($num_acl > 0)||($num_group > 0)) {
		echo "<P><font color=\"red\">Cannot delete user($uid). There are too many dependancies.</font></P>";
	} else {
		$result = @SQLQuery("DELETE FROM node2 WHERE uid='$uid'", $dbi);
		$result = @SQLQuery("DELETE FROM user WHERE uid='$uid'", $dbi);
		Audit("user_group","delete","UID=".$uid,$dbi);
	}
	break;
}

if ($debug) {
	$_ERROR=$_ERROR.@SQlError($dbi);
}

?>
<form id="userform" name="userform" method="post" action="index.php?menu=admin&module=user_group">
<fieldset class="_collapsible"><legend>User Groups <?php if ($_ret > 9) {
	echo "<a href=\"javascript:_add('_useradd','2','')\" title=\"Add User Group\"><img src=\"images/plus-new.gif\" border=\"0\" /></a>"; } ?></legend>
<table border=0 width="100%">
<tr><td>
        <div id="_useradd" style="display:none">
        <fieldset class="_collapsible">
	<table class="_table">
	<tr><td width="50">Disable:</td><td><input type="checkbox" name="check_disable" onclick="Javascript:_checked(this,document.userform.disable)"><input type="hidden" name="disable" value="0"></td>
	<tr><td width="50">ID:</td><td><input type="text" id="id" name="id" size=6 value="0"></td>
	    <td>&nbsp;&nbsp;</td>
    	    <td width="100">Access List:</td><td><select id="acl_id" name="acl_id" size="1" style="width: 150px"><option value="0"><?php
	$result = @SQLQuery("SELECT id FROM acl WHERE type=1 GROUP BY id",$dbi);
	while ($row = SQLFetchRow($result)) {
		echo "<option value=\"".$row[0]."\">".$row[0];
	} ?></td>
    	<tr><td width="50">Group Name:</td><td><input type="text" id="uid" name="uid" size="20"><!-- <input type="hidden" name="gid"> --><input type="hidden" name="group"></td>
	    <td>&nbsp;&nbsp;</td>
	    <td colspan="2"><div id="_ldapgroup" <?php if (!isset($auth)) $auth=0; if (($auth == 0) || ($auth == 1) || ($auth == 3)) echo "style=\"display:none\""; ?> >LDAP Group: <input type="text" id="gid" name="gid" value=""></div></td>
    	<tr><td width="50">Comment:</td><td colspan="4"><input type="text" id="comment" name="comment" size="65"></td>
    	<tr><td width="50">Auth Meth:</td><td><select id="auth" name="auth" size="1" style="width: 150px"><!-- onchange="_check_method(this)" --><?php
	foreach ($auth_method as $i=>$method) {
		echo "<option value=\"$i\">$method</option>";
	} ?></td>
	    <td>&nbsp;&nbsp;</td>
    	    <td width="100">Expires:</td><td><input type="text" name="expires" id="expires" size="20" autocomplete="off"><!-- &nbsp;<a href="Javascript:open_tcalendar(document.forms['userform'].elements['expires']);"><img src="images/cal.gif" width="16" height="16" border="0" alt="Click here to pick a date"></img></a> --></td>
    	<tr class="_passwords"><td width="50">Password:</td><td><input id="password" type="password" name="password" size="20"></td>
	    <td>&nbsp;&nbsp;</td>
    	    <td width="100">Re-Password:</td><td><input type="password" id="re_password" name="re_password" size="20"></td>
    	<tr class="_passwords"><td width="50">Enable:</td><td><input type="password" id="enable" name="enable" size="20"></td>
	    <td>&nbsp;&nbsp;</td>
    	    <td width="100">Re-Enable:</td><td><input type="password" id="re_enable" name="re_enable" size="20"></td>
    	<tr class="_passwords"><td width="50">PAP:</td><td><input type="password" id="pap" name="pap" size="20"></td>
	    <td>&nbsp;&nbsp;</td>
    	    <td width="100">Re-PAP:</td><td><input type="password" id="re_pap" name="re_pap" size="20"></td>
    	<tr class="_passwords"><td width="50">ARAP:</td><td><input type="password" id="arap" name="arap" size="20"></td>
	    <td>&nbsp;&nbsp;</td>
    	    <td width="100">Re-ARAP:</td><td><input type="password" id="re_arap" name="re_arap" size="20"></td>
    	<tr class="_passwords"><td width="50">CHAP:</td><td><input type="password" id="chap" name="chap" size="20"></td>
	    <td>&nbsp;&nbsp;</td>
    	    <td width="100">Re-CHAP:</td><td><input type="password" id="re_chap" name="re_chap" size="20"></td>
    	<tr class="_passwords"><td width="50">MSCHAP:</td><td><input type="password" id="mschap" name="mschap" size="20"></td>
	    <td>&nbsp;&nbsp;</td>
    	    <td width="100">Re-MSCHAP:</td><td><input type="password" id="re_mschap" name="re_mschap" size="20"></td>
<!--    	<tr><td width="50">Before Authorization:</td><td><input type="text" name="b_author" size="20"></td>
	    <td>&nbsp;&nbsp;</td>
    	    <td width="100">After Authorization:</td><td><input type="text" name="a_author" size="20"></td> -->
    	<tr><td width="50">Service Default:</td><td><input type="checkbox" name="check_svc_dflt" onclick="Javascript:_checked(this,document.userform.svc_dflt);"><input type="hidden" name="svc_dflt" value="0"></td>
	    <td>&nbsp;&nbsp;</td>
    	    <td width="100">Command Default:</td><td><input type="checkbox" name="check_cmd_dflt" onclick="Javascript:_checked(this,document.userform.cmd_dflt);"><input type="hidden" name="cmd_dflt" value="0"></td>
    	<tr><td width="50">Max Session:</td><td><input type="text" name="maxsess" size="20" value="0"></td>
	<tr><td width="50"><input name="option" value="1" type="hidden"></td><td><input type="submit" name="_submit" value="Add" width=8 onClick="return _check_user_form(2)"> <input type="reset" onClick="return confirm('Are you sure you want to reset the data?')"></td>
	</table>
	</fieldset>
	</div>
</td></tr>
<tr><td><div id="_results0"></div></td></tr>
</table>
</fieldset>
</form>

<div id="theLayer" style="position:absolute;width:850;left:50;top:200;visibility:hidden">
<table id="theLayerTable" border="0" width="850" bgcolor="#000000" cellspacing="0" cellpadding="1" height="100">
<tr>
<td width="100%">
  <table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
  <td id="titleBar" style="cursor:move" width="100%" bgcolor="#9999CC">
  <ilayer width="100%" onSelectStart="return false">
  <layer width="100%" onMouseover="isHot=true;if (isN4) ddN4(theLayer)" onMouseout="isHot=false">
  <font face="Arial" color="#FFFFFF"><div id="titleName"></div></font>
  </layer>
  </ilayer>
  </td>
  <td style="cursor:hand" valign="top" bgcolor="red">
  <a href="Javascript:hideMe()"><font face=arial color="#FFFFFF" style="text-decoration:none">X</font></a>
  </td>
  </tr>
  <tr>
  <td width="100%" bgcolor="#FFFFFF" style="padding:4px" colspan="2">
	<iframe id="_nodeframe" width="100%" height="300" frameborder="0" scrolling="yes"></iframe>
  </td>
  </tr>
  </table> 
</td>
</tr>
</table>
</div>

<script>
$(document).ready(function() {
        $('#uid').change(function() {
		var re = /^[a-zA-Z0-9\._\-]+$/;
                if (!re.test($(this).val())) {
                        alert("Not a valid group name");
                        $(this).val("");
                        $(this).focus();
                }
        });

        $('#comment').change(function() {
		var re = /^[a-zA-Z0-9 _,&\-\$]*$/;
                if (!re.test($(this).val())) {
                        alert("There characters being inputted that are not allowed");
                        $(this).val("");
                        $(this).focus();
                }
        });

        $('#password').change(function() {
                if ($(this).val().length < <?php echo $pass_complex->{'pass_size'}; ?>) {
                        alert("Minimum password length is "+<?php echo $pass_complex->{'pass_size'}; ?>);
                        $(this).val("");
                        $(this).focus();
                }
        });

        $('#re_password').change(function() {
                if ($('#password').val() != $(this).val()) {
                        alert("Password does not match");
                        $('#password').val("");
                        $(this).val("");
                        $('#password').focus();
                }
        });

        $('#enable').change(function() {
                if ($(this).val().length < <?php echo $pass_complex->{'pass_size'}; ?>) {
                        alert("Minimum enable password length is "+<?php echo $pass_complex->{'pass_size'}; ?>);
                        $(this).val("");
                        $(this).focus();
                }
        });

        $('#re_enable').change(function() {
                if ($('#enable').val() != $(this).val()) {
                        alert("Enable does not match");
                        $('#enable').val("");
                        $(this).val("");
                        $('#enable').focus();
                }
        });

	$( "#expires" ).datetimepicker({dateFormat:'yy-mm-dd', timeInput: true, showHour:false, showMinute:false, showSecond:false, timeFormat: 'HH:mm:ss'});

        $('#re_pap').change(function() {
                if ($('#pap').val() != $(this).val()) {
                        alert("PAP does not match");
                        $('#pap').val("");
                        $(this).val("");
                        $('#pap').focus();
                }
        });

        $('#auth').change(function() {
                if ($('#auth').val() == 1) {
                        _show_class('tr','_passwords');
			document.getElementById("_ldapgroup").style.diplay="none";
                } else if ($('#auth').val() == 3) { //LDAP
                        _hide_class('tr','_passwords');
			document.getElementById("_ldapgroup").style.display="";
		} else {
                        _hide_class('tr','_passwords');
			document.getElementById("_ldapgroup").style.display="none";
                }
        });

        var src = "result.php?_ret="+admin_priv_lvl+"&_table=usergroup";
            src += "&offset=0&vrows="+admin_vrows+"&_index=0";
        $.get(src, function (data, status) {
                document.getElementById("_results0").innerHTML = data;
        });

        $('#search').change(function() {
                var new_src = src;
                if ($(this).val()) {
                        var _s = $(this).val().indexOf("=");
                        if (_s > 0) {
                                new_src += "&"+$(this).val();
                        } else {
                                new_src += "&group="+$(this).val();
                        }
                }
                $.get(new_src, function (data, status) {
                        document.getElementById("_results0").innerHTML = data;
                });
        });
});
</script>

