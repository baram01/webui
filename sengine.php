<?php
/*
    Copyright (C) 2020  Young Consulting, Inc



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

if (!preg_match("/index.php/",$_SERVER['PHP_SELF'])) {
        Header("Location: index.php");
        die();
}

if ($_ret < 15) {
	echo "<script language=\"javascript\"> top.location.href=\"?module=main\"; </script>";
}

$_svc = "";
$sqlcmd = "";

switch ($option) {
  case 1:
	$_svc = "add";
	$sqlcmd = "INSERT INTO config (engine, license) VALUES ('$engine', '$license')";
	break;
  case 3:
	$_svc = "delete";
	$sqlcmd = "DELETE FROM config WHERE engine='$engine'";
	break;
}

if ($sqlcmd) {
	SQLQuery($sqlcmd, $dbi);
	if (!@SQLError($dbi))
                Audit("sengine", $_svc,"ENGINE=".$engine, $dbi);
        if ($debug)
                $_ERROR=SQLError($dbi).$sqlcmd;
}
?>
<script language="Javascript">
<!--
function _add(obj) {
        resultForm = document.engineform;

        if (resultForm.option.value == "2") {
                resultForm.engine.value = "";
                resultForm.license.value = "";
                resultForm.option.value = "1";
                resultForm._submit.value = "Add";
        } else {
                addMe(obj);
        }
}

function _delete(engine) {
	var msg = "Do you really want to delete "+engine+"?";

	if (confirm(msg)) {
		document.engineform.engine.value = engine;
		document.engineform.option.value = "3";
		document.engineform.submit();
	}
}
//-->
</script>

<form name="engineform" id="engineform" method="post" action="?menu=system&module=sengine">
<fieldset class=" collapsible"><legend>Engines <?php if ($_ret > 9) { echo "<a href=\"javascript:_add('_engineAdd')\"><img src=\"images/plus-new.gif\" border=\"\" /></a>";} ?></legend>
<table border="0">
<tr> <td>
	<div id="_engineAdd" style="display:none">
	<fieldset class="_collapsible">
	<table class="_table">
	<tr><td>Engine:</td><td><input name="engine" id="engine"></td>
	<tr><td>License:</td><td><input name="license" id="license" value=<?php echo $site_config->license; ?>></td>
	<tr><td width="80">&nbsp;<input name="option" value="1" type="hidden"></td><td><input type="submit" id="_add" value="Add" /></td>
	</table>
	</fieldset>
	</div>
</td> </tr>
<tr>
	<td><div id="_status"> </div></td>
</tr>
</table>
</fieldset>
</form>

<script type="text/javascript">
$(document).ready(function(e) {
	$('#engine').change(function() {
		if (/[^a-zA-Z0-9\._\-]/.test($(this).val())) {
			alert("Not a valid engine name");
			$(this).val("");
			$(this).focus();
		}
	});

	var src = "result.php?_ret="+admin_priv_lvl+"&_table=config&vrows="+admin_vrows;
	$.get(src, function (data, status) {
		document.getElementById("_status").innerHTML = data;
	});
});
</script>
