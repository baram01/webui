<?php
if (!preg_match("/index.php/",$_SERVER['PHP_SELF'])) {
        Header("Location: index.php");
        die();
}

if ($_ret < 15) {
        echo "<script language=\"JavaScript\"> top.location.href=\"?module=main\"; </script>";
}

$_lic_host = ''; //`hostid`;
$_lic_type = ''; //$lic->{'license'};
$_lic_date = ''; //date('Y-m-d', $lic->{'date'});
$_STATUS = '';

if ($option == 2) {
	if (isset($site_init) && $site_init) {
		$site_config->{'init'} = 1;
	} else {
		$site_config->{'init'} = 0;
	}
	if (isset($license)) {
		$site_config->{'license'} = $license;
	} else {
		$site_config->{'license'} = "0";
	}
	$site_config->{'webui'} = $webui;
	$site_config->{'company_name'} = $company_name;
	$site_config->{'message'} = $message;
	if (isset($_FILES["logo_file"]["name"])) {
	   $file_toupload = basename($_FILES["logo_file"]["name"]);
	   $image_file = $target_dir.$file_toupload;
	   if (! empty($file_toupload)) {
		if (file_exists($image_file)) {
			$_STATUS = "Status: Image already exists";
		} else {
			if (exif_imagetype($_FILES["logo_file"]["tmp_name"])) {
			   list($width, $height, $type, $attr) = getimagesize($_FILES["logo_file"]["tmp_name"]);
			   if (($width > 151) || ($height > 151)) {
				$_STATUS = "Status: Logo too large";
			   } else {
			     if (move_uploaded_file($_FILES["logo_file"]["tmp_name"], $image_file)) {
				if ($file_toupload != $site_config->{'logo'}) {
					unlink($target_dir.$site_config->{'logo'});
				}
				$site_config->{'logo'} = $file_toupload;
				$_STATUS = "Status: Image uploaded";
			      } else {
				$_STATUS = "Status: Problem uploading image";
			      }
			   }
			} else {
				$_STATUS = "Status: not an image file";
			}
		}
	   }
	}
	$file = fopen($json_site_file, "w");
	fwrite($file, json_encode($site_config));
	fclose($file);

	$_STATUS .= "<p>Changes applied.";
	Audit("ssite","change","site",$dbi);
}
?>
<script language="Javascript">
<!--
function _checkSiteReq() {
        var form = document.siteform;
        var msg = "";

        if (!form.company_name.value) {
                msg = "Company Name is required";
                form.company_name.focus();
        }

	if (form.message.value) {
		var n = form.message.value.indexOf("'");
		if (n>0) {
			msg += "\nCharacter ' cannot be used in message";
			form.message.focus();
		}
	}

        if (form.logo_file.value) {
                form.enctype="multipart/form-data";
        }

        if (msg) {
                alert(msg);
                return false;
        }

        return true;
}
//-->
</script>
<fieldset class=" collapsible"><legend>Site Configuration</legend>
<table border=0 width="100%">
<tr><td>
	<form name="siteform" method="post" action="?menu=system&module=ssite" onSubmit="javascript: return _checkSiteReq();">
	<fieldset class="_collapsible">
	<table class="_table">
<!--
	<tr><td>Host ID:</td><td><?php echo $_lic_host; ?></td>
	    <td></td>
	    <td></td></tr>
	<tr><td>License:</td><td><?php echo $_lic_type; ?></td>
	    <td>Expiration: <?php echo $_lic_date; ?></td>
	    <td></td></tr>
	<tr><td>License Key:</td><td colspan="3"><input name="license" type="text" size="80" value="<?php echo $site_config->{'license'}; ?>"></td>
	    <td></td>
	    <td></td></tr>
-->
<?php
	if ($site_config->{'init'}) {
		echo "
	<tr><td colspan=\"5\"><font color=\"red\">Initial Configuration</font> - please enter License key if receive one, WebUI FQDN if different, Company Name, Company Logo if have one and Legal message</td></tr>
	<tr><td colspan=\"5\">&nbsp;</td></tr>
	<tr><td>License Key:</td><td colspan=\"3\"><input name=\"license\" type=\"text\" size=\"80\" value=\"".$site_config->{'license'}."\"></td>
	    <td></td>
	    <td><input name=\"site_init\" value=\"0\" type=\"hidden\"></td></tr>
		";
		$site_config->{'webui'}="https://".$_SERVER['HTTP_HOST'];
	}
?>
	<tr><td>WebUI FQDN:</td><td><input id="webui" name="webui" type="text" size="30" value="<?php echo $site_config->{'webui'}; ?>" title="Put FQDN here"></td>
	    <td></td>
	    <td></td></tr>
	<tr><td>Company Name:</td><td><input id="company_name" name="company_name" type="text" size="30" value="<?php echo $site_config->{'company_name'}; ?>"></td>
	    <td></td>
	    <td></td></tr>
	<tr><td>Company Logo:</td>
	    <td><input name="logo" type="text" size="30" value="<?php echo $site_config->{'logo'}; ?>" disabled></td>
	    <td><input name="logo_file" id="logo_file" type="file" accept="image/*" data-type="image"></td>
	    <td><font size=1>**max logo size - 150x150 pixel</font></td></tr>
	<tr><td>Message:</td>
	    <td colspan="2"><textarea id="message" name="message" rows="3" cols="50"><?php echo $site_config->{'message'}; ?></textarea></td></tr>
	    <td></td></tr>
	<tr><td></td>
	    <td><input type="submit" name="_submit" value="Save"></td>
	    <td><input name="option" value="2" type="hidden"></td>
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
	$('#webui').change(function() {
		var re = /^http[s]:\/\/([a-zA-Z0-9.]+)$/;
		if (!re.test($(this).val())) {
			alert("Only allow format http[s]://FQDN or IP.");
			// $(this).val("");
			$(this).focus();
		}
	});
	$('#company_name').change(function() {
		var re = /^([a-zA-Z0-9 _\-&,.]+)$/;
		if (!re.test($(this).val())) {
			alert("Only allow alphanumeric and these special charasters _-&");
			// $(this).val("");
			$(this).focus();
		}
	});
	$('#message').change(function() {
		var re = /^([a-zA-Z0-9 _\-&,.:]+)$/;
		if (!re.test($(this).val())) {
			alert("Only allow alphanumeric and these special charasters _-&");
			// $(this).val("");
			$(this).focus();
		}
	});
});
</script>
