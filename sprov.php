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
	if (isset($process_prov)) {
		$prov_config->{'process_prov'} = 1;
	} else {
		$prov_config->{'process_prov'} = 0;
	}
	if (isset($mail_secure)) {
		$prov_config->{'mail_secure'} = 1;
	} else {
		$prov_config->{'mail_secure'} = 0;
	}
	$prov_config->{'mail_relay'} = $mail_relay;
	$prov_config->{'mail_relay_port'} = $mail_relay_port;
	$prov_config->{'from'} = $from;
	$prov_config->{'mail_user'} = $mail_user;
	$prov_config->{'mail_password'} = $mail_password;

	$file = fopen($json_prov_file, "w");
	fwrite($file, json_encode($prov_config));
	fclose($file);

	$_STATUS = "Changes applied.";
	Audit("sprov","change","provision", $dbi);
}
?>
<script language="Javascript">
<!--
function _checkProvReq() {
        var form = document.passform;
        var msg = "";

        if (!form.mail_relay.value) {
                msg = "Mail relay is required";
                form.mail_relay.focus();
        }

	if (!form.mail_relay_port.value) {
		form.mail_relay_port.value = 25;
	}

	if (form.from.value) {
		var n = form.message.value.indexOf("'");
		if (n>0) {
			msg += "\nCharacter ' cannot be used in message";
			form.message.focus();
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
<fieldset class=" collapsible"><legend>Provision Configuration</legend>
<table border=0 width="100%">
<tr><td>
	<form name="provform" method="post" action="?menu=system&module=sprov" onSubmit="javascript: return _checkPassReq();">
	<fieldset class="_collapsible">
	<table class="_table">
	<tr><td>Provision:</td>
	    <td><input name="process_prov" type="checkbox" <?php if ($prov_config->{'process_prov'}) { echo "checked"; } ?>></td>
	    <td></td></tr>
	<tr><td>Secure:</td>
	    <td><input id="mail_secure" name="mail_secure" type="checkbox" <?php if ($prov_config->{'mail_secure'}) { echo "checked"; } ?>></td>
	    <td></td></tr>
	<tr><td>Mail Relay:</td>
	    <td><input id="mail_relay" name="mail_relay" type="text" size=30 value="<?php echo $prov_config->{'mail_relay'}; ?>" onchange="_verify(this,'server')"></td>
	    <td></td></tr>
	<tr><td>Mail Relay Port:</td>
	    <td><input id="mail_relay_port" name="mail_relay_port" type="text" size=30 value="<?php echo $prov_config->{'mail_relay_port'}; ?>"></td>
	    <td></td></tr>
	<tr><td>From:</td>
	    <td><input id="from" name="from" type="text" size=30 value="<?php echo $prov_config->{'from'}; ?>" onchange="_verify(this,'email')"></td>
	    <td></td></tr>
	<tr><td>Username:</td>
	    <td><input id="mail_user" name="mail_user" type="text" size=30 value="<?php echo $prov_config->{'mail_user'}; ?>"></td>
	    <td></td></tr>
	<tr><td>Password:</td>
	    <td><input id="mail_user" name="mail_password" type="text" size=30 value="<?php echo $prov_config->{'mail_password'}; ?>"></td>
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
	$('#mail_secure').click(function() {
		if (this.checked) {
			$('#mail_relay_port').val("465");
		} else {
			$('#mail_relay_port').val("25");
		}
	});
        $('#mail_relay').change(function() {
                var re =  /^\w+([\.-_]?\w+)*(\.\w{2,3})+$/;
                if (!re.test($(this).val())) {
                        alert("Not a server name");
                        $(this).val("");
                        $(this).focus();
                }
        });
        $('#mail_relay_port').change(function() {
                if (isNaN($(this).val())) {
                        alert("Only integers are allowed");
                        $(this).val("");
                        $(this).focus();
                }
        });
        $('#from').change(function() {
		var from = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
                if (!from.test($(this).val())) {
                        alert("Characters are not allowed <>");
                        $(this).val("");
                        $(this).focus();
                }
        });
        $('#mail_user').change(function() {
		var mail_user = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
                if (!mail_user.test($(this).val())) {
                        alert("Characters are not allowed <>");
                        $(this).val("");
                        $(this).focus();
                }
        });
});
</script>

