<?php
/*
    Copyright (C) 2003-2021 Young Consulting, Inc

    This program is free software; you can redistribute it and/or modify it
    under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
                                                                                
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
                                                                                
    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

Changes:
02/02/2010	Andrew Young
	-Fix bug in first insert
03/10/2019	Andrew Young
	-Auth method support for json
05/04/2019	Andrew Young
	-Added dynamic query
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

<!-- <script language="JavaScript" src="js/calendar3.js"></script> -->
<script language="Javascript" src="js/user.js"></script>
<script language="Javascript" src="js/result.js"></script>


<?php
function Prov_email($_uid, $_dbi) {
	$result = file_get_contents("send_prov.php?_ret=5&_uid=$_uid");
	echo $result;
}

$where = "";
switch ($option) {
  case 1:
	if (isset($uid)&&!empty($uid)) {
	    $sqlcmd = "INSERT INTO user (id, uid, gid, comment, auth, password, enable, arap, pap, chap, mschap, expires, b_author, a_author, svc_dflt, cmd_dflt, maxsess, acl_id, shell, homedir, user, flags";
	$sqlcmd .= ") VALUES ($id, '$uid', '$gid', '$comment', $auth, ENCRYPT('$password'),ENCRYPT('$enable'),'$arap',ENCRYPT('$pap'),'$chap','$mschap','$expires','$b_author','$a_author',$svc_dflt,$cmd_dflt,$maxsess,$acl_id,'$shell','$homedir', 1, $flags";
	    $sqlcmd .= ")";
	    $result = @SQLQuery("$sqlcmd", $dbi);
	    if (!@SQLError($dbi)) {
		echo "<P><font color=\"green\">User(".substr($uid,0,20).") added.</font></P>";
		Audit("user","add","UID=".$uid,$dbi);
		if ($prov_config->{'process_prov'}) {
			Prov_email($uid, $dbi);
		}
	    } else {
		echo "<P><font color=\"red\">User(".substr($uid,0,20).") add failed.</font></P>";
	    }
	} else {
		echo "<P><font color=\"red\">Blank User ID cannot be added.</font></P>";
	}
	break;
  case 2:
	if (!isset($b_author)) $b_author = "";
	if (!isset($a_author)) $a_author = "";
	$sqlcmd = "UPDATE user set id=$id, gid='$gid', comment='$comment', auth=$auth, expires='$expires', disable=$disable, b_author='$b_author',a_author='$a_author',svc_dflt=$svc_dflt,cmd_dflt=$cmd_dflt,maxsess=$maxsess,acl_id=$acl_id, shell='$shell', homedir='$homedir'";
	if ($re_password) $sqlcmd .= ", password=ENCRYPT('$password')";
	if ($re_enable) $sqlcmd .= ", enable=ENCRYPT('$enable')";
	if ($re_arap) $sqlcmd .= ", arap='$arap'";
	if ($re_pap) $sqlcmd .= ", pap=ENCRYPT('$pap')";
	if ($re_chap) $sqlcmd .= ", chap='$chap'";
	if ($re_mschap) $sqlcmd .= ", mschap='$mschap'";
	$sqlcmd .= ", flags=$flags";
	if ($flags < 3) $sqlcmd .= ", fail=0";
	$result = @SQLQuery("$sqlcmd WHERE uid='$uid'",$dbi);
	if (!@SQLError($dbi)) {
		echo "<P><font color=\"green\">User($uid) modified.</font></P>";
		Audit("user","change","UID=".$uid,$dbi);
	} else {
		echo "<P><font color=\"red\">User($uid) modify failed.</font></P>";
	}
	break;
  case 3:
	$result = @SQLQuery("SELECT id FROM acl WHERE value='$uid'", $dbi);
	if (@SQLNumRows($result) > 0) {
		echo "<P><font color=\"red\">Cannot delete user($uid). There are too many dependancies.</font></P>";
	} else {
		$result = @SQLQuery("DELETE FROM node2 WHERE uid='$uid'", $dbi);
		$result = @SQLQuery("DELETE FROM user WHERE uid='$uid'", $dbi);
		$result = @SQLQuery("DELETE FROM contact_info WHERE uid='$uid'", $dbi);
		Audit("user","delete","UID=".$uid,$dbi);
	}
	break;
  case 4:
	$where = "AND gid='$group'";
	break;
}

if ($debug) {
	$_ERROR.=@SQlError($dbi);
}

?>
<form id="userform" name="userform" method="post" action="index.php?menu=admin&module=user">
<fieldset class="_collapsible"><legend>Users <?php if ($group) echo "in group <font color=\"black\">$group</font>"; ?><?php if ($_ret > 9) {
	echo "<a href=\"javascript:_add('_useradd','1','";
	if ($pass_complex->{'use_temp'}) echo $pass_complex->{'temp_pass'};
	echo "');\" title=\"Add User\"><img src=\"images/plus-new.gif\" border=\"0\" /></a>"; } ?><!-- &nbsp; &nbsp;<a class="trig_popup" title="Search for user"><img src="images/search.gif" border="0" width="20"></img></a> --></legend>
<!--
<div class="popup_search">
	<span class="search"</span>
	<div>
		<div class="popupClose">&times;</div>
		<form id="_Search">
		<table>
			<tr><td>User:</td><td><input type="text" id="_uid" name="_uid"></td>
			<tr><td><input type="submit"></td><td>&nbsp;</td>
		</table>
		</form>
	</div>
</div>
-->
<table border=0 width="100%">
<tr><td>
        <div id="_useradd" style="display:none">
        <fieldset class="_collapsible">
	<table class="_table">
	<tr><td width="50">Disable:</td><td><input type="checkbox" name="check_disable" onclick="Javascript:_checked(this,document.userform.disable)"><input type="hidden" name="disable" value="0"></td>
	    <td>&nbsp;&nbsp;</td>
	    <td>Locked:</td><td><input type="checkbox" name="check_lock" onclick="Javascript: _checked2(this,document.userform.flags,8)" disabled></td>
	<tr><td width="50">ID:</td><td><input type="text" id="id" name="id" size=5 value="<?php echo $start_uid; ?>"></td>
	    <td>&nbsp;&nbsp;</td>
    	    <td width="100">Access List:</td><td><select id="acl_id" name="acl_id" size="1" style="width: 150px"><option value="0"><?php
	$result = @SQLQuery("SELECT id FROM acl WHERE type=1 GROUP BY id",$dbi);
	while ($row = SQLFetchRow($result)) {
		echo "<option value=\"".$row[0]."\">".$row[0];
	} ?></td>
    	<tr><td width="50">User ID:</td><td><input type="text" id="uid" name="uid" size="20"></td>
	    <td>&nbsp;&nbsp;</td>
    	    <td width="100">Group:</td><td><select name=gid size=1 style="width: 150px"><option value=""><?php $result = @SQLQuery("SELECT uid FROM user WHERE user=2", $dbi);
	while ($row = @SQLFetchRow($result)) {
		echo "<option value=\"".$row[0]."\"";
		if (isset($group) && ($row[0]==$group)) echo " selected";
		echo ">".$row[0]."</option>";
	} ?></select>
    	<tr><td width="50">Comment:</td><td colspan="4"><input type="text" id="comment" name="comment" size="64">&nbsp;<a href="Javascript:_open_contact(document.userform.uid,document.forms['userform'].elements['comment'])" title="Update contact info"><img width=25 src="images/identity.gif" border=0></img></a></td>
    	<tr><td width="50">Auth Meth:</td><td><select name="auth" size="1" style="width: 150px" onchange="_check_method(this)"><?php
	foreach ($auth_method as $i=>$method) {
		echo "<option value=\"$i\">$method";
	} ?></td>
	    <td>&nbsp;&nbsp;</td>
    	    <td width="100">Expires:</td><td><input type="text" name="expires" id="expires" size="20"><!-- &nbsp;<a href="Javascript:open_tcalendar(document.forms['userform'].elements['expires']);"><img src="images/cal.gif" width="16" height="16" border="0" alt="Click here to pick a date"></img></a> --></td>
	<tr class="_passwords"><td colspan="2">Change Password at next login:&nbsp;&nbsp;<input type="checkbox" name="check_flags" onclick="Javascript:_checked2(this,document.userform.flags,2)"><input type="hidden" name="flags" value="0"></td>
	    <td>&nbsp;&nbsp;</td>
	    <td></td><td></td>
    	<tr class="_passwords"><td width="50">Password:</td><td><input type="password" id="password" name="password" autocomplete="new-password" size="20"></td>
	    <td>&nbsp;&nbsp;</td>
    	    <td width="100">Re-Password:</td><td><input type="password" id="re_password" name="re_password" size="20"></td>
    	<tr class="_passwords"><td width="50">Enable:</td><td><input type="password" id="enable" name="enable" autocomplete="new-password" size="20"></td>
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
<!--    	<tr><td width="50">Before Authorization:</td><td><input type="hidden" name="b_author" size="20"></td>
	    <td>&nbsp;&nbsp;</td>
    	    <td width="100">After Authorization:</td><td><input type="hidden" name="a_author" size="20"></td> -->
    	<tr><td width="50">Service Default:</td><td><input type="checkbox" name="check_svc_dflt" onclick="Javascript:_checked(this,document.userform.svc_dflt);"><input type="hidden" name="svc_dflt" value="0"></td>
	    <td>&nbsp;&nbsp;</td>
    	    <td width="100">Command Default:</td><td><input type="checkbox" name="check_cmd_dflt" onclick="Javascript:_checked(this,document.userform.cmd_dflt);"><input type="hidden" name="cmd_dflt" value="0"></td>
    	<tr><td width="50">Max Session:</td><td><input type="text" name="maxsess" size="20" value="0"></td>
    	<tr><td width="50">Shell:</td><td><input type="text" name="shell" size="20"></td>
            <td>&nbsp;&nbsp;</td>
    	    <td width="100">Home Directory:</td><td colspan="2"><input type="text" name="homedir" size="40"></td>
	<tr><td width="50"><input name="option" id="option" value="1" type="hidden"></td><td><input type="submit" name="_submit" value="Add" width=8 /> <input type="reset" onClick="return confirm('Are you sure you want to reset the data?');"></td>
<!--	<tr><td width="50"><input name="option" value="1" type="hidden"></td><td><input type="submit" name="_submit" value="Add" width=8 onclick="return _check_user_form(1)" /> <input type="reset" onClick="return confirm('Are you sure you want to reset the data?');"></td> -->
	</table>
	</fieldset>
	</div>
</td></tr>
<tr><td><div id="_results0"></div></td></tr>
</table>
</fieldset>
</form>

<div id="theLayer" style="position:fixed;width:850;left: 50px;top: 200px;visibility:hidden">
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
/**
$(window).load(function () {
    $(".trig_popup").click(function(){
       $('.popup_search').show();
    });
//    $('.popup_search').click(function(){
//        $('.popup_search').hide();
//    });
    $('.popupClose').click(function(){
        $('.popup_search').hide();
    });
});
**/

$(document).ready(function() {
	$('#uid').change(function() {
		var re = /^[a-zA-Z0-9\._\-@]+$/;
		if (!re.test($(this).val())) {
			alert("Not a valid user name");
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
		if ($(this).val() == $('#uid').val()) {
			alert("You cannot have password and username be the same");
			$(this).val("");
			$(this).focus();
		} else {
		   if ($(this).val().length < <?php echo $pass_complex->{'pass_size'}; ?>) {
			alert("Minimum password length is "+<?php echo $pass_complex->{'pass_size'}; ?>);
			$(this).val("");
			$(this).focus();
		    }
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

	$('#re_pap').change(function() {
		if ($('#pap').val() != $(this).val()) {
			alert("PAP does not match");
			$('#pap').val("");
			$(this).val("");
			$('#pap').focus();
		}
	});

	var src = "result.php?_ret="+admin_priv_lvl+"&_table=user";
	    src += "&offset=0&vrows="+admin_vrows+"&_index=0";
<?php if (isset($group) && $group) echo "	    src += \"&group=$group\";\n"; ?>
	$.get(src, function (data, status) {
		document.getElementById("_results0").innerHTML = data;
	});

	$('#userform').submit(function(event) {
		var p = false;
		var msg = "";

		if ($('#uid').val()=="") {
			msg += "User ID cannot be blank";
			$('#uid').focus();
			p = true;
		}

		if ($("input[name=option]").val()==2) {
			$('#uid').prop("disabled",false);
		} else {
		    if ($('#password').val()=="") {
			if (p) {
				msg += "\n";
			}
			msg += "Password cannot be blank";
			$('#password').focus();
			p = true;
		    }
		}

		if ($('#comment').val()=="") {
			if (p) {
				msg += "\n";
			}
			msg += "Comment is not required but recommended.";
		}

		if ($('#expires').val()=="") {
			$('#expires').val("0000-00-00 00:00:00");
		}

		if (msg!="") {
			alert(msg);
			if (p) {
				event.preventDefault();
				return false;
			}
		}
	});

	$( "#expires" ).datetimepicker({dateFormat:'yy-mm-dd', timeInput: true, showHour:false, showMinute:false, showSecond:false, timeFormat: 'HH:mm:ss'});
});
</script>
