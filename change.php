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

<div id="change" class="_table">
       <form name="change_password" method="post" action="?module=change&option=1">
	   <fieldset class=" collapsible"><legend>Change Password</legend>
           <table align="left" border=0>
	      <tr><td>Username:</td><td><input type="text" name="uid"></td></tr>
	      <tr><td>Password:</td><td><input type="password" id="oldpass" name="oldpass"></td></tr>
	      <tr><td>Change:  </td><td><select name="type">
		  <option value="password">password
		  <option value="enable">enable
		  <option value="pap">pap
		                   </select>
	      <tr><td>New Password:</td><td><input type="password" id="newpass" name="newpass"></td></tr>
	      <tr><td>Re-type Password:</td><td><input type="password" id="retype" name="retype"></td></tr>
	      <tr><td></td><td><input type="submit" value="Change" onClick="return _check(this);"></td></tr>
	  </table>
	  </fieldset>
       </form>
</div>
<script>
$(document).ready(function() {
	$('#newpass').keyup(function() {
		var password = $('#newpass').val();
		if (password.length < <?php echo $pass_complex->{'pass_size'}; ?>) {
			$('#newpass').css("border-bottom-color", "#dc3545");
		} else {
			$('#newpass').css("border-bottom-color", "#28a745");
		}
	});
	$('#newpass').change(function() {
		if ($(this).val() == $('#oldpass').val()) {
			alert("New password cannot be same as current");
			$(this).val("");
			$(this).focus();
			return;
		}

		if ($(this).val().length < <?php echo $pass_complex->{'pass_size'}; ?>) {
			alert("Password is too small. Minimum size is <?php echo $pass_complex->{'pass_size'}; ?> characters.");
			$(this).val("");
			$(this).focus();
			return;
		}

		if (<?php echo $pass_complex->{'complexity'}; ?>) {
			var msg = "Password must have at least";
			var ret = 0;

			var pass_upper = <?php echo $pass_complex->{'upper'}; ?>;
			var pass_lower = <?php echo $pass_complex->{'lower'}; ?>;
			var pass_number = <?php echo $pass_complex->{'number'}; ?>;
			var pass_special = <?php echo $pass_complex->{'special'}; ?>;
			var pass_multi = <?php echo $pass_complex->{'multi'}; ?>;

			var re = /[A-Z]/;
			if (pass_upper && !re.test($(this).val())) {
				msg = msg + " " + pass_upper + " uppercase";
				ret = 1;
			}

			re = /[a-z]/;
			if (pass_lower && !re.test($(this).val())) {
				msg = msg + " " + pass_lower + " lowercase";
				ret = 1;
			}

			re = /[0-9]/;
			if (pass_number && !re.test($(this).val())) {
				msg = msg + " " + pass_number + " number";
				ret = 1;
			}

			re = /[!"#$%&'()*+,\-./:;<=>?@[\\\]^_`{|}~]/;
			if (pass_special && !re.test($(this).val())) {
				msg = msg +  " " + pass_special + " special";
				ret = 1;
			}

			re = /(.)\1{<?php echo $pass_complex->{'multi'}; ?>}/g;
			if (pass_multi && re.test($(this).val())) {
				msg = msg + " and allowed " + (pass_multi - 1) + " consecutive";
				ret = 1;
			}

			if (ret) {
				alert(msg);
				$(this).val("");
				$(this).focus();
			}
		}
	});
	$('#retype').change(function() {
		if ($(this).val() == $('#newpass').val()) {
			alert("New password does not match");
			$(this).val("");
			$(this).focus();
		}
	});
});
</script>
