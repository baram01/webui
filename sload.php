<?php
/*
    Copyright (C) 2021  Young Consulting, Inc



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
?>

<fieldset class=" collapsible"><legend>Load Vendor Dictionary File</legend>
<table border="0">
<tr> <td>
	<form id="load_rad" method="post" action="?menu=system&module=sload" enctype="multipart/form-data">
	<table class="_table">
	<tr><td width="80">File:</td><td><input name="file" id="file" type="file" accept="dictionary.*"></td>
	<tr><td width="80">&nbsp;</td><td><input type="submit" id="load" value="Load" /></td>
	</table>
	</form>
</td> </tr>
<tr>
	<td><div id="status"> </div></td>
</tr>
</table>
</fieldset>

<script type="text/javascript">
	$(document).ready(function(e) {
		$("#load_rad").on('submit', (function(e) {
			e.preventDefault();

			$.ajax({
				url: 'upload.php?_index=0&_option=1',
				type: 'post',
				data: new FormData(this),
				contentType: false,
				cache: false,
				processData: false,
				beforeSend: function() {
					$("#status").fadeOut();
				},
				success: function(response) {
					var obj = JSON.parse(response);
					var msg = "<font color=\"";

					if (obj.status) { msg += "red\">"; }
					else { msg += "green\">"; }
					msg += obj.message + "</font>";
					$("#status").html(msg).fadeIn();

					$("#load_rad")[0].reset();
				},
				error: function(e) {
					$("#status").html(e).fadeIn();
				}
			});
		}));
	});
</script>
