<?php
if (!preg_match("/index.php/",$_SERVER['PHP_SELF'])) {
        Header("Location: index.php");
        die();
}

if ($_ret < 15) {
	echo "<script language=\"javascript\"> top.location.href=\"?module=main\"; </script>";
}

function zip($source, $destination) {
	exec('tar czf tmp/'.$destination.' '.$source);

	echo "<iframe src=\"tmp/$destination\" type=\"appication/x-gzip\" style=\"display:none;\"></iframe>\n";
//	unlink('tmp/'.$destination);
}

function unzip($source) {
	exec('tar xzf '. $source);
	echo "Restored";
}

switch ($option) {
 case 1:
	zip('cust', 'cust_bck.tgz');
	break;

 case 2:
	// $file_toupload = $_FILES['file_source']['name'];
	$file_ = $_FILES['file_source']['tmp_name'];
	if (! empty($file_)) {
		unzip($file_);
	}
	break;
}

?>
<script>
function do_service () {
	var msg;

	if ($('#option').val() == 2) {
		if (/tgz$/.test($('#file_source').val())) {
			form.enctype="multipart/form-data";
		}
	}

	if (msg) {
		alert(msg);
		return false;
	}

	return true;
}
</script>

<fieldset class=" collapsible"><legend>Backup/Restore</legend>
<table border="0">
<tr> <td>
	<form name="backup" method="post" action="?menu=system&module=sbackup" onsubmit="return do_service();">
	<fieldset class="_collapsible">Backup Configuration
	<table class="_table">
	<tr><td width="80">&nbsp;<input id="option" name="option" type="hidden"  value="2" /></td><td><input id="back_up" type="submit" value="Backup" onclick="javascript: $('#option').val(1)" /></td>
	</table>
	</fieldset>
	</form>
	<form id="r_form" method="post" action="?menu=system&module=sbackup" enctype="multipart/form-data">
	<fieldset class="_collapsible">Restore Configuration
	<table class="_table">
	<tr><td width="80">File:</td><td><input name="file" id="file" type="file" accept=".tgz"></td>
	<tr><td width="80">&nbsp;</td><td><input type="submit" id="restore" value="Restore" /></td>
	</table>
	</fieldset>
	</form>
</td> </tr>
<tr>
	<td><div id="status"> </div></td>
</tr>
</table>
</fieldset>

<script type="text/javascript">
        $(document).ready(function(e) {
                $("#r_form").on('submit', (function(e) {
                        e.preventDefault();

                        $.ajax({
                                url: 'upload.php?_index=0&_option=2',
                                type: 'post',
                                data: new FormData(this),
                                contentType: false,
                                cache: false,
                                processData: false,
				beforeSend: function() {
					$("#status").html("Uploading...");
				},
                                success: function(response) {
                                        if (response == 0) {
                                                $("#status").html("file not uploaded");
                                        } else {
                                                $("#status").html(response);
                                                $("#r_form")[0].reset();
                                        }
                                },
                                error: function(e) {
                                        $("#status").html(e);
                                }
                        });
                }));
        });
</script>

