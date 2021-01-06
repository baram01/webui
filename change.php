<?php
switch ($option) {
case 1:
	if (!isset($expiretime)) { $expiretime = "0000-00-00 00:00:00"; }
	$ret = updatePassword($type, $uid, $oldpass, $newpass, $expiretime, $dbi);
	echo "<script language=\"JavaScript\">";
	if ($ret > 0) {
		echo " alert('Changed password for $uid');";
		Audit("chg_pass","change",$type." UID=".$uid, $dbi);
	} else if ($ret < 0) {
		echo " alert('You cannot reuse last ".$pass_complex->{'repeat'}." passwords for $uid');";
	} else {
		echo " alert('Cannot change password for $uid');";
		Audit("chg_pass","fail",$type." UID=".$uid, $dbi);
	}
	echo "close();</script>";
}

?>
<script language="JavaScript">
<!--
function check_complexity(element)
{
	var form = element.form;
	var res;
	var ret = 0, msg;

	var pass_complex = <?php echo $pass_complex->{'complexity'}; ?>;
	var pass_upper = <?php echo $pass_complex->{'upper'}; ?>;
	var pass_lower = <?php echo $pass_complex->{'lower'}; ?>;
	var pass_number = <?php echo $pass_complex->{'number'}; ?>;
	var pass_special = <?php echo $pass_complex->{'special'}; ?>;
	var pass_multi = <?php echo $pass_complex->{'multi'}; ?>;

	if (element.value.length < <?php echo $pass_complex->{'pass_size'}; ?>) {
		msg = "Password is too small.  Minimum size is <?php echo $pass_complex->{'pass_size'}; ?> characters.";
		ret = 1;
	} else if ((form.type.value=="password")&&(element.value == element.form.oldpass.value)) {
		msg = "New password cannot be the same as old password";
		ret = 1;
	} else {
	   if (pass_complex == <?php echo $pass_complex->{'complexity'}; ?>) {
		msg = "Password must have at least ";
		res = element.value.match(/[A-Z]/g);
		if (pass_upper && (res != null) && (res.length < pass_upper)) {
			msg = msg + " " + pass_upper + " uppercase";
			ret = 1; 
		} else if (pass_upper && (res == null)) {
			msg = msg + " " + pass_upper + " uppercase";
			ret = 1; 
		}
		
		res = element.value.match(/[a-z]/g);
		if (pass_lower && (res != null) && (res.length < pass_lower)) {
			if (ret) msg += ",";
			msg = msg + " " + pass_lower + " lowercase";
			ret = 1;
		} if (pass_lower && (res == null)) {
			if (ret) msg += ",";
			msg = msg + " " + pass_lower + " lowercase";
			ret = 1;
		}

		res = element.value.match(/[0-9]/g);
		if (pass_number && (res != null) && (res.length < pass_number)) {
			if (ret) msg += ",";
			msg = msg + " " + pass_number + " number";
			ret = 1;
		} else if (pass_number && (res == null)) {
			if (ret) msg += ",";
			msg = msg + " " + pass_number + " number";
			ret = 1;
		}

		res = element.value.match(/[!"#$%&'()*+,\-./:;<=>?@[\\\]^_`{|}~]/g);
		if (pass_special && (res != null)&&(res.length < pass_special)) {
			if (ret) msg += ",";
			msg = msg + " " + pass_special + " special";
			ret = 1;
		} else if (pass_special && (res == null)) {
			if (ret) msg += ",";
			msg = msg + " " + pass_special + " special";
			ret = 1;
		}

		res = element.value.match(/(.)\1{1}/g);
		if (pass_multi && (res != null) && (res.length < pass_multi)) {
			if (ret) msg += ",";
			msg = msg + " " + pass_multi + " consecutive";
			ret = 1;
		}
	   }
	}

	if (ret) {	
		alert(msg);
		element.value = "";
		setTimeout(function(){element.focus();},5);
	}
}

function check(element)
{
	var form = element.form;
	var ret = 0, msg;

	if (element.value != form.newpass.value) {
		msg = "Password does not match. Please retry.";
		ret = 1;
	}

	if (ret) {	
		alert(msg);
		element.value = form.newpass.value = "";
		form.newpass.focus();
	}
}

function _check(obj)
{
	var ret = true;

	if (obj.form.uid.value == "") {
		alert("Username is required.");
		obj.form.uid.focus();
		ret = false;
	} else if (obj.form.oldpass.value == "") {
		alert("Password is required.");
		obj.form.oldpass.focus();
		ret = false;
	} else if (obj.form.newpass.value == "") {
		alert("New Password cannot be blank.");
		obj.form.newpass.focus();
		ret = false;
	}

	return ret;
}
		
-->
</script>
<div id="change" class="_table">
       <form name="change_password" method="post" action="?module=change&option=1">
	   <fieldset class=" collapsible"><legend>Change Password</legend>
           <table align="left" border=0>
	      <tr><td>Username:</td><td><input type="text" name="uid"></td></tr>
	      <tr><td>Password:</td><td><input type="password" name="oldpass"></td></tr>
	      <tr><td>Change:  </td><td><select name="type">
		  <option value="password">password
		  <option value="enable">enable
		  <option value="pap">pap
		                   </select>
	      <tr><td>New Password:</td><td><input type="password" name="newpass" onchange="check_complexity(this);"></td></tr>
	      <tr><td>Re-type Password:</td><td><input type="password" name="retype" onblur="check(this);"></td></tr>
	      <tr><td></td><td><input type="submit" value="Change" onClick="return _check(this);"></td></tr>
	  </table>
	  </fieldset>
       </form>
</div>
