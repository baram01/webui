<?php
/*
    Copyright (C) 2003-2020 Young Consulting, Inc

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

//if (!eregi("index.php",$_SERVER['PHP_SELF'])) {
if (!preg_match("/index.php/",$_SERVER['PHP_SELF'])) {
        Header("Location: index.php");
        die();
}

if ($_ret < 5) {
        echo "<script language=\"JavaScript\"> top.location.href=\"?module=main\"; </script>";
}
?>

<script language="Javascript">
<!--

function _check_profile_form()
{
	var ret = true;
	var form = document.profileform;
	var msg = "";

	if (!form.pid.value || form.pid.value==0) {
		msg = "ID cannot be blank or 0";
		form.pid.focus();
		ret = false;
	}

	if (!form.uid.value) {
		msg = msg + "\nName cannot be blank";
		if (ret) form.uid.focus();
		ret = false;
	}

	if (msg) alert(msg);

	return ret;
}

function _delete(pid, uid) {
        var msg = "Do you want really want to delete "+uid+"?";

        if (confirm(msg)) {
                document.profileform.pid.value = pid;
                document.profileform.uid.value = uid;
                document.profileform.option.value = "3";
                document.profileform.submit();
        }
}

function _add(obj) {
        document.profileform.pid.value = document.getElementById("_lastID").value;
	addMe(obj);
}
-->
</script>

<?php
switch ($option) {
  case 1:
//	@SQLQuery("INSERT INTO user (id, uid, user)  VALUES ($pid, '$uid', 3)", $dbi);
	@SQLQuery("INSERT INTO profile (id, uid)  VALUES ($pid, '$uid')", $dbi);
	if (!@SQLError($dbi))
		Audit("profile","add","UID=".$uid,$dbi);
	break;
  case 3:
	$result = @SQLQuery("SELECT id FROM acl WHERE value1=$pid", $dbi);
	if (@SQLNumRows($result)==0) {
//		@SQLQuery("DELETE FROM user WHERE uid='$uid'", $dbi);
		@SQLQuery("DELETE FROM profile WHERE uid='$uid'", $dbi);
		@SQLQuery("DELETE FROM node2 WHERE uid='$uid'", $dbi);
		Audit("profile","delete","UID=".$uid,$dbi);
	} else {
		echo "<P><font color=\"red\">Cannot delete profile ($uid).  Profile has dependancies.</font></P>";
	}
	if ($debug) {
		$_ERROR=@SQlError($dbi);
	}
	break;
}

?>
<form name="profileform" method="post" action="?menu=admin&module=profile">
<fieldset class="_collapsible"><legend>Profiles&nbsp;&nbsp;&nbsp;&nbsp;<?php if ($_ret > 9) { echo "<a href=\"javascript:_add('_profileadd')\"><img src=\"images/plus-new.gif\" border=\"0\" /></a>"; } ?></legend>
<table border=0 width="100%">
<tr><td>
	<div id="_profileadd" style="display:none">
	<fieldset class="_collapsible">
	<table class="_table">
	<tr><td width="50">ID:</td><td><input type="text" id="pid" name="pid" size=8></td>
    	<tr><td width="50">Name:</td><td><input type="text" id="uid" name="uid" size="20"></td>
	<tr><td width="50"><input name="option" value="1" type="hidden"></td><td><input type="submit" name="_submit" value="Add" width=8 onClick="return _check_profile_form()"></td>
	</table>
	</fieldset>
	</div>
</td></tr>
<tr><td><div id="_results0"></div></td></tr>
</table>
</fieldset>
</form>

<div id="theLayer" style="position:absolute;width:850;left:100;top:200;visibility:hidden">
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
        $('#pid').change(function() {
                if (isNaN($(this).val())) {
                        alert("Only integers are allowed");
                        $(this).val("");
                        $(this).focus();
		}
        });

        $('#uid').change(function() {
                var re = /^[a-zA-Z0-9._\-]+$/;
                if (!re.test($(this).val())) {
                        alert("Not valid profile name");
                        $(this).val("");
                        $(this).focus();
		}
        });

        var src = "result.php?_ret="+admin_priv_lvl+"&_table=profile";
            src += "&offset=0&vrows="+admin_vrows+"&_index=0";
        $.get(src, function (data, status) {
                document.getElementById("_results0").innerHTML = data;
        });
});
</script>

