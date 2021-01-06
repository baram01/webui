<?php
if (!preg_match("/index.php/",$_SERVER['PHP_SELF'])) {
        Header("Location: index.php");
        die();
}

if ($_ret < 15) {
        echo "<script language=\"JavaScript\"> top.location.href=\"?module=main\"; </script>";
}

$_STATUS = '';

if ($option == 2) {
	$pass_complex->{'pass_size'} = $pass_size;
	$pass_complex->{'changetime'} = $pass_changetime;
	$pass_complex->{'expiretime'} = $pass_expiretime;
	$pass_complex->{'complexity'} = (isset($pass_complexity_on))?1:0;
	$pass_complex->{'repeat'} = $pass_repeat;
	$pass_complex->{'upper'} = (isset($pass_upper_on))?1:0;
	$pass_complex->{'lower'} = (isset($pass_lower_on))?1:0;
	$pass_complex->{'number'} = (isset($pass_number_on))?1:0;
	$pass_complex->{'special'} = (isset($pass_special_on))?1:0;
	$pass_complex->{'multi'} = $pass_multi;
	$pass_complex->{'use_temp'} = (isset($use_temp_on))?1:0;
	$pass_complex->{'temp_pass'} = $temp_pass;

	$file = fopen($json_pass_file, "w");
	fwrite($file, json_encode($pass_complex));
	fclose($file);

	$_STATUS = "Changes applied.";
	Audit("spass","change","complexity", $dbi);
}
?>
<script language="Javascript">
<!--
function _checkReq() {
        var form = document.passform;
        var msg = "";

        if (!form.pass_size.value) {
                msg = "Length of password is required";
                form.company_name.focus();
        }

	if (form.pass_complexity_on.checked == true) {
		if (form.pass_repeat.value < 1) {
			msg += "\nReuse password minimum is 1";
			form.pass_repeat.value = 1;
		}

		if (form.pass_multi.value < 1) {
			msg += "\Consecutive characters minimum is 1";
			form.pass_multi.value = 1;
		}
	}

	if (form.use_temp_on.checked == true) {
		if (!form.temp_pass.value) {
			msg += "\nTemp password is required when Temp password on/off";
			form.temp_pass.focus();
		}
	}

        if (msg) {
                alert(msg);
                return false;
        }

        return true;
}
//-->
</script>
<fieldset class=" collapsible"><legend>Password Options</legend>
<table border=0 width="100%">
<tr><td>
	<form name="passform" method="post" action="?menu=system&module=spass" onSubmit="javascript: return _checkReq();">
	<fieldset class="_collapsible">
	<table class="_table">
	<tr><td>Minimum length of password:</td>
	    <td colspan="3"><input id="pass_size" name="pass_size" type="text" size="8" value="<?php echo $pass_complex->{'pass_size'}; ?>"></td>
	    <td></td></tr>
	<tr><td>Inform user to change password when:</td>
	    <td><input id="pass_changetime" name="pass_changetime" type="text" size="8" value="<?php echo $pass_complex->{'changetime'}; ?>"></td>
	    <td>days left to expire</td></tr>
	<tr><td>Passwords will expire in:</td>
	    <td><input id="pass_expiretime" name="pass_expiretime" type="text" size="8" value="<?php echo $pass_complex->{'expiretime'}; ?>"></td>
	    <td>days</td></tr>
	<tr><td>&nbsp;</td>
	    <td></td>
	    <td></td></tr>
	<tr><td>Complexity On/Off:</td>
	    <td><input name="pass_complexity_on" type="checkbox" <?php if ($pass_complex->{'complexity'}) { echo "checked"; } ?>></td>
	    <td></td></tr>
	<tr><td>Can reuse password after:</td>
	    <td><input id="pass_repeat" name="pass_repeat" type="text" size="8" value="<?php echo $pass_complex->{'repeat'}; ?>"></td>
	    <td>times</td></tr>
	<tr><td>Require at least one uppercase letter:</td>
	    <td><input name="pass_upper_on" type="checkbox" <?php if ($pass_complex->{'upper'}) { echo "checked"; } ?>></td>
	    <td></td></tr>
	<tr><td>Require at least one lowercase letter:</td>
	    <td><input name="pass_lower_on" type="checkbox" <?php if ($pass_complex->{'lower'}) { echo "checked"; } ?>></td>
	    <td></td></tr>
	<tr><td>Require at least one number:</td>
	    <td><input name="pass_number_on" type="checkbox" <?php if ($pass_complex->{'number'}) { echo "checked"; } ?>></td>
	    <td></td></tr>
	<tr><td>Require at least one special character:</td>
	    <td><input name="pass_special_on" type="checkbox" <?php if ($pass_complex->{'special'}) { echo "checked"; } ?>></td>
	    <td></td></tr>
	<tr><td>Can only have:</td>
	    <td><input id="pass_multi" name="pass_multi" type="text" size="8" value="<?php echo $pass_complex->{'multi'}; ?>"></td>
	    <td>character(s) consecutively</td></tr>
	<tr><td>&nbsp;</td>
	    <td></td>
	    <td></td></tr>
	<tr><td>Temp password On/Off:</td>
	    <td><input name="use_temp_on" type="checkbox" <?php if ($pass_complex->{'use_temp'}) { echo "checked"; } ?>></td>
	    <td></td></tr>
	<tr><td>Temp Password:</td>
	    <td colspan="2"><input name="temp_pass" type="text" size="15" value="<?php echo $pass_complex->{'temp_pass'}; ?>"></td>
	    <td></td></tr>
	<tr><td><input name="option" value="2" type="hidden"></td>
	    <td><input type="submit" name="_submit" value="Save"></td>
	    <td></td></tr>
	</table>
	</fieldset>
	</form>
</td></tr>
<tr><td>
	<div id="_pstatus" class="_scollwindow"><?php echo $_STATUS; ?></div>
</td></tr>
</table>
</fieldset>

<script>
$(document).ready(function() {
	var re = /^([0-9]+)$/;
	$('#pass_size').change(function() {
		if (!re.test($(this).val())) {
			alert("Number only");
			$(this).val("");
			$(this).focus();
		}
	});
	$('#pass_changetime').change(function() {
		if (!re.test($(this).val())) {
			alert("Number only");
			$(this).val("");
			$(this).focus();
		}
	});
	$('#pass_expiretime').change(function() {
		if (!re.test($(this).val())) {
			alert("Number only");
			$(this).val("");
			$(this).focus();
		}
	});
	$('#pass_repeat').change(function() {
		if (!re.test($(this).val())) {
			alert("Number only");
			$(this).val("");
			$(this).focus();
		}
	});
	$('#pass_multi').change(function() {
		if (!re.test($(this).val())) {
			alert("Number only");
			$(this).val("");
			$(this).focus();
		}
	});
});
</script>
