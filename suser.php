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

//if (!eregi("index.php",$_SERVER['PHP_SELF'])) {
if (!preg_match("/index.php/",$_SERVER['PHP_SELF'])) {
	Header("Location: index.php");
	die();
}

if ($_ret < 15) {
        echo "<script language=\"JavaScript\"> top.location.href=\"?module=main\"; </script>";
}

$sqlcmd = "";
$_svc = "";

switch ($option) {
   case 1:
	$_svc="add";
	if ($link) {
		$result = SQLQuery("SELECT uid FROM user WHERE uid='$uid'", $dbi);
		if (SQLNumRows($result) > 0) {
			$sqlcmd = sprintf("INSERT INTO admin (uid, comment, password, priv_lvl, link, vrows, disable, expire) VALUES ('%s','%s','%s',%d, %d, %d, %d, '%s')",$uid,$comment,"",$priv_lvl,1,$a_vrows,$disable,$expire);
		} else { $_ERROR="Admin User ($uid) not found"; }
	} else {
		if ($expire=="") $expire="0000-00-00 00:00:00";
		$sqlcmd = sprintf("INSERT INTO admin (uid, comment, password, priv_lvl, link, vrows, disable, expire) VALUES ('%s','%s','%s',%d, %d, %d, %d, '%s')",$uid,$comment,unixcrypt($password),$priv_lvl,0,$a_vrows,$disable,$expire);
	}
	break;

   case 2:
	$_svc="change";
	if ($link) {
		$result = @SQLQuery("SELECT uid FROM user WHERE uid='$uid'", $dbi);
		if (@SQLNumRows($result) > 0) {
			$sqlcmd = "UPDATE admin set comment='$comment', priv_lvl=$priv_lvl, link=1, vrows=$a_vrows, disable=$disable, expire='$expire' WHERE uid='$uid'";
		 } else { $_ERROR="Admin User ($uid) not found"; }
	} else {
//		if ($re_password) $re_password = ", password=ENCRYPT('$password')";
		if ($re_password) $re_password = ", password='".unixcrypt($password)."'";
		$sqlcmd = "UPDATE admin set comment='$comment', priv_lvl=$priv_lvl, link=0, vrows=$a_vrows, disable=$disable, expire='$expire' $re_password WHERE uid='$uid'";
	}
	break;

   case 3:
	$_svc="delete";
	$sqlcmd = "DELETE FROM admin WHERE uid='$uid'";
	break;
}

if ($sqlcmd) {
	SQLQuery($sqlcmd, $dbi);
	if (!@SQLError($dbi))
		Audit("suser", $_svc,"UID=".$uid, $dbi);
	if ($debug)
		$_ERROR=SQLError($dbi);
}

?>
<script language="Javascript">
<!--
function _checkRequired() {
        var form = document.userform;
        var msg = "";

        if (!form.uid.value ) {
                msg = msg + " Missing Username.\n";
		form.uid.focus();
        }

	if (!form.admlink.checked) {
        	if ((form.option.value!=2)&& !form.password.value ) {
			if (!msg) {
				form.password.focus();
			}
                	msg = msg + " Missing Password.\n";
		}
        }

        if (msg) {
                alert(msg);
                return false;
        }

	if (form.option.value == "2") {
		var msg = "Do you want really want to modify "+form.uid.value+"?";
		if (!confirm(msg)) {
			return false;
		}
		form.uid.disabled = false;
	}

	if (form.admlink.checked) {
		form.link.value = "1";
	} else {
		form.link.value = "0";
	}

	if (form.d_link.checked) {
		form.disable.value = "1";
	} else {
		form.disable.value = "0";
	}

        return true;
}

function _add(obj) {
        resultForm = document.userform;

        if (resultForm.option.value == "2") {
		resultForm.d_link.checked = false;
		resultForm.disable.value = "0";
                resultForm.uid.disabled = false;
                resultForm.uid.value = "";
                resultForm.comment.value = "";
		resultForm.priv_lvl.value = "1";
		resultForm.admlink.disabled = false;
		resultForm.a_vrows.value = "25";
		resultForm.expire.value = "";
                resultForm.option.value = "1";
                resultForm._submit.value = "Add";
        } else {
                addMe(obj);
        }
}

function _modify(uid, comment, priv_lvl, link, vrows, disable, expire) {
	resultForm = document.userform;

	if (disable==1) resultForm.d_link.checked = true;
	else resultForm.d_link.checked = false;
	resultForm.uid.value = uid;
	resultForm.uid.disabled = true;
	if (uid == "admin") resultForm.admlink.disabled = true;
	else resultForm.admlink.disabled = false;
	resultForm.comment.value = comment;
	resultForm.priv_lvl.value = priv_lvl;
	if (link==1) resultForm.admlink.checked = true;
	else resultForm.link.checked = false;
	resultForm.a_vrows.value = vrows;
	resultForm.expire.value = expire;
	resultForm._submit.value = "Modify";
	resultForm.option.value = "2";
	document.getElementById("_userAdd").style.display = "";
}

function _delete(uid) {
	var msg = "Do you want really want to delete "+uid+"?";

	if (confirm(msg)) {
		document.userform.uid.value = uid;
		document.userform.option.value = "3";
		document.userform.submit();
	}
}

//-->
</script>
<form name="userform" method="post" action="?menu=system&module=suser">
<fieldset class="_collapsible"><legend>System Admin Users <?php if ($_ret > 9) { echo "<a href=\"javascript:_add('_userAdd')\"><img src=\"images/plus-new.gif\" border=\"0\" /></a>"; } ?></legend>
<table border=0 width="100%">
<tr><td>
        <div id="_userAdd" style="display:none">
        <fieldset class="_collapsible">
	<table class="_table">
	<tr><td>Disable:</td>
	    <td><input id="d_link" name="d_link" type="checkbox"><input id="disable" name="disable" type="hidden"></td>
	    <td>Expire:</td>
	    <td><input name="expire" id="expire" type="text" autocomplete="off"> <font size=-2>eg. 2021-01-09 00:00:00</font></td></tr>
	<tr><td>Username:</td>
	    <td><input id="uid" name="uid" type="text" size="25"></td>
	    <td></td>
	    <td></td></tr>
	<tr><td>Comment:</td>
	    <td colspan="3"><input id="comment" name="comment" type="text" size="50"></td> </tr>
	<tr><td>Linked:</td>
	    <td><input name="admlink" type="checkbox"></td>
	    <td><input type="hidden" name="link"></td>
	    <td></td></tr>
	<tr class="_passwords><td>Password:</td>
	    <td><input name="password" type="password" size="25"></td>
	    <td>Re-Password:</td>
	    <td><input name="re_password" type="password" size="25" onBlur="javascript:return _checkpass(this,document.forms['userform'].elements['password']);"></td></tr>
	<tr><td>Privelege:</td>
	    <td><select name="priv_lvl">
		<option value="1">1 - Report Only</option>
		<option value="5">5 - View</option>
		<option value="10">10 - Update</option>
		<option value="15">15 - Super User</option>
		</select> </td>
	    <td></td>
	    <td></td></tr>
	<tr><td>Rows to view:</td>
	    <td><select name="a_vrows">
<?php foreach($_vrows as $_item) {
	if (!$_item) {
echo "			<option value=\"$_item\">all</option>";
	} else {
echo "			<option value=\"$_item\">$_item</option>";
	}
      } ?>
		</select></td>
	    <td></td>
	    <td></td></tr>
	<tr><td><input name="option" value="1" type="hidden"></td>
	    <td><input type="submit" name="_submit" value="Add" onClick="return _checkRequired();"></td>
	    <td></td>
	    <td></td></tr>
	</table>
	</fieldset>
	</div>
</td></tr>
<tr>
	<td><div id="_result0"></div></td>
</tr>
</table>
</fieldset>
</form>

<script>
$(document).ready(function() {
        $('#uid').change(function() {
                if (/[^a-zA-Z0-9\._\-]/.test($(this).val())) {
                        alert("Not a valid user name");
                        $(this).val("");
                        $(this).focus();
                }
        });

        $('#comment').change(function() {
		var re = /^([a-zA-Z0-9 _,&\-]+)$/;
                if (!re.test($(this).val())) {
                        alert("Not a valid comment inputted");
                        $(this).val("");
                        $(this).focus();
                }
        });

	$("#expire").datetimepicker({dateFormat:'yy-mm-dd', timeInput: true, showHour: false, showMinute:false, showSecond:false,  timeFormat: 'HH:mm:ss'});

	var src = "result.php?_ret="+admin_priv_lvl+"&_table=admin&vrows="+admin_vrows;
	$.get(src, function (data, status) {
		document.getElementById("_result0").innerHTML = data;
	});

        $('#search').change(function() {
                var new_src = src;
                if ($(this).val()) {
                        var _s = $(this).val().indexOf("=");
                        if (_s > 0) {
                                new_src += "&"+$(this).val();
                        } else {
                                new_src += "&user="+$(this).val();
                        }
                }
                $.get(new_src, function (data, status) {
                        document.getElementById("_result0").innerHTML = data;
                });
        });
});
</script>
