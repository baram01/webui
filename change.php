<?php
switch ($option) {
case 1:
	if (!isset($expiretime)) { $expiretime = "0000-00-00 00:00:00"; }
	$ret = updatePassword($type, $uid, $oldpass, $newpass, $expiretime, $dbi);
	if (($type=="password") && ($ret > 0)) {
		updateOther("enable", $uid, $newpass, $_enable, $dbi);
	}
	echo "<script language=\"JavaScript\">";
	if ($ret > 0) {
		echo " alert('Changed $type for $uid');";
		Audit("chg_pass","change"," UID=".$uid." ".$type, $dbi);
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
	      <tr><td>Username:</td><td><input type="text" id="uid" name="uid"></td></tr>
	      <tr><td>Password:</td><td><input type="password" id="oldpass" name="oldpass"></td></tr>
	      <tr><td>Change:  </td><td><select name="type" id="type">
		  <option value="password">password
		  <option value="pap">pap
		                   </select>
	      <tr><td>New Password:</td><td><input type="password" id="newpass" name="newpass"></td></tr>
	      <tr><td>Re-type Password:</td><td><input type="password" id="retype" name="retype"></td></tr>
	      <tr class="_enpass"><td>Sync Enable:</td><td><input type="checkbox" id="sync" name="sync"></td></tr>
	      <tr class="_enpass"><td>New Enable:</td><td><input type="password" id="_enable" name="_enable"></td></tr>
	      <tr class="_enpass"><td>Re-type Enable:</td><td><input type="password" id="re_enable" name="re_enable"></td></tr>
	      <tr><td></td><td><input type="submit" id="_submit" name="_submit" value="Change"></td></tr>
	  </table>
	  </fieldset>
       </form>
</div>
<script>
$(document).ready(function() {
	var _sync = 0;

	$('#type').change(function(){
		if($(this).val() == "pap") {
			$('._enpass').hide();
			$('#_enable').prop("disabled", true);
			$('#re_enable').prop("disabled", true);
		} else {
			$('._enpass').show();
			$('#_enable').prop("disabled", false);
			$('#re_enable').prop("disabled", false);
		}
	});
	$('#newpass').keyup(function() {
		var password = $(this).val();
		if (password.length < <?php echo $pass_complex->{'pass_size'}; ?>) {
			$(this).css("border-bottom-color", "#dc3545");
		} else {
			$(this).css("border-bottom-color", "#28a745");
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
			var msg = "Password must have at ";
			var ret = 0;

			var pass_upper = <?php echo $pass_complex->{'upper'}; ?>;
			var pass_lower = <?php echo $pass_complex->{'lower'}; ?>;
			var pass_number = <?php echo $pass_complex->{'number'}; ?>;
			var pass_special = <?php echo $pass_complex->{'special'}; ?>;
			var pass_multi = <?php echo $pass_complex->{'multi'}; ?>;

			var re = /[A-Z]/;
			if (pass_upper && !re.test($(this).val())) {
				msg = msg + "least " + pass_upper + " uppercase ";
				ret = 1;
			}

			re = /[a-z]/;
			if (pass_lower && !re.test($(this).val())) {
				msg = msg + "least " + pass_lower + " lowercase ";
				ret = 1;
			}

			re = /[0-9]/;
			if (pass_number && !re.test($(this).val())) {
				msg = msg + "least " + pass_number + " number ";
				ret = 1;
			}

			re = /[!"#$%&'()*+,\-./:;<=>?@[\\\]^_`{|}~]/;
			if (pass_special && !re.test($(this).val())) {
				msg = msg +  "least " + pass_special + " special ";
				ret = 1;
			}

			re = /(.)\1{<?php echo $pass_complex->{'multi'}; ?>}/g;
			if (pass_multi && re.test($(this).val())) {
				msg = msg + "most " + (pass_multi) + " consecutive characters";
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
		if ($(this).val() != $('#newpass').val()) {
			alert("New password does not match");
			$(this).val("");
			$(this).focus();
		}
	});
	$('#re_enble').change(function() {
		if ($(this).val() != $('#_enable').val()) {
			alert("New enable does not match");
			$(this).val("");
			$(this).focus();
		}
	});
	$('#sync').click(function() {
		if ($(this).prop("checked") == true) {
			$('#_enable').val($('#newpass').val());
			$('#re_enable').val($('#newpass').val());
			$('#_enable').prop("disabled", true);
			$('#re_enable').prop("disabled", true);
			_sync = 1;
		} else {
			$('#_enable').prop("disabled", false);
			$('#re_enable').prop("disabled", false);
			_sync = 0;
		}
	});
	$('#_submit').on("click", function() {
		var msg = "";
		if (!$('#uid').val()) {
			msg = "Missing username\n";
			$('#uid').css("border-bottom-color", "#dc3545");
		} else {
			$('#uid').css("border-bottom-color", "#28a745");
		}
		if (!$('#oldpass').val()) {
			msg = msg + "Missing current password\n";
			$('#oldpass').css("border-bottom-color", "#dc3545");
		} else {
			$('#oldpass').css("border-bottom-color", "#28a745");
		}
		if (!$('#newpass').val()) {
			msg = msg + "Missing new password\n";
			$('#newpass').css("border-bottom-color", "#dc3545");
			$('#retype').css("border-bottom-color", "#dc3545");
		}
		if (!$('#_enable').val() && ($('#type').val() == "password")) {
			msg = msg + "Missing new enable";
			$('#_enable').css("border-bottom-color", "#dc3545");
			$('#re_enable').css("border-bottom-color", "#dc3545");
		}

		if (msg) {
			alert(msg);
			event.preventDefault();
		}
		$('#_enable').prop("disabled", false);
	});
});
</script>
