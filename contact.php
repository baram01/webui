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

require_once("config.php");
require_once("mainfile.php");

//$dbi=OpenDatabase($dbhost, $dbuname, $dbpass, $dbname);
$dbi=OpenDatabase($db_config);

if (checkLoginXML($_COOKIE["login"],$dbi) < 5) {
	CloseDatabase($dbi);
	echo "<script language=\"Javascript\"> top.location.href=\"index.php?module=main\"; </script>";
}


switch ($option) {
   case 1:
	$result = @SQLQuery("INSERT INTO contact_info (uid, fname, surname, address1, address2, address3, phone, email) VALUES ('$uid', '$fname', '$surname', '$address1', '$address2', '$address3', '$phone', '$email')", $dbi);

	$result = @SQLQuery("UPDATE user SET comment='$fname $surname' WHERE uid='$uid'", $dbi);
	Audit("contact","add","UID=".$uid,$dbi);
	break;
   case 2:
	$result = @SQLQuery("UPDATE contact_info SET fname='$fname', surname='$surname', address1='$address1', address2='$address2', address3='$address3', phone='$phone', email='$email' WHERE uid='$uid'", $dbi);

	$result = @SQLQuery("UPDATE user SET comment='$fname $surname' WHERE uid='$uid'", $dbi);
	Audit("contact","change","UID=".$uid,$dbi);
	break;
   case 3:
	Audit("contact","delete","UID=".$uid,$dbi);
	break;
	
}

$row = array();
$result = @SQLQuery("SELECT * FROM contact_info WHERE uid='$uid'", $dbi);
if (SQLNumRows($result) > 0) {
	$option = 2;
	$row = @SQLFetchArray($result);
	@SQLFreeResult($result);
} else {
	$option = 1;
}
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="css/style.css" />
<link rel="stylesheet" type="text/css" href="css/style-addition.css">
<link rel="stylesheet" type="text/css" href="js/jquery-ui-1.12.1/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="js/jquery-ui-1.12.1/jquery-ui.theme.css">
</head>
<body>
<script language="JavaScript" src="js/tacacs.js"></script>
<script type="text/javascript" src="js/jquery-2.2.4.js"></script>
<script language="Javascript">
<!--
function _require() {
<?php
if (isset($update)) {
 	echo "parent.document.userform.comment.value = document.contactform.fname.value + \" \" + document.contactform.surname.value;";
}
?>
	return true;
}
-->
</script>
<form name="contactform" method="post" action="contact.php?uid=<?php echo $uid; ?>">
<fieldset class="_collapsible"><legend id="_serviceset">Contact Information for <?php echo $uid; ?></legend>
<table border=0 width="100%" class="_table2">
<tr><td>
	<table class="_table2">
	<tr><td>Name:</td>
	    <td><input name="fname" id="fname" type="text" size="30" value="<?php if (isset($row["fname"])) echo $row["fname"]; ?>"></td>
	    <td><input title="surname" name="surname" id="surname" type="text" size="30" value="<?php if (isset($row["surname"])) echo $row["surname"]; ?>"></td>
	</tr>
	<tr><td>Address:</td>
	    <td colspan="2"><input name="address1" id="address1" type="text" size="66" value="<?php if (isset($row["address1"])) echo $row["address1"]; ?>"</td>
	</tr>
	<tr><td></td>
	    <td colspan="2"><input name="address2" id="address2" type="text" size="66" value="<?php if (isset($row["address2"])) echo $row["address2"]; ?>"></td>
	</tr>
	<tr><td></td>
	    <td colspan="2"><input name="address3" id="address3" type="text" size="66" value="<?php if (isset($row["address3"])) echo $row["address3"]; ?>"></td>
	</tr>
	<tr><td>Phone:</td>
	    <td colspan="2"><input name="phone" id="phone" type="text" size="20" value="<?php if (isset($row["phone"])) echo $row["phone"]; ?>"> <font size=-1>eg. +1 222-333-4444</font></td>
	</tr>
	<tr><td>Email:</td>
	    <td colspan="2"><input name="email" id="email" type="text" size="66" value="<?php if (isset($row["email"])) echo $row["email"]; ?>"></td>
	</tr>
	<tr><td><input name="option" type="hidden" value="<?php echo $option; ?>"></td>
	    <td><input name="_submit" type="submit" value="<?php if ($option==1) echo "Add"; else echo "Modify"; ?>" onClick="return _require();">&nbsp;<input type="reset" onClick="return confirm('Are you sure that you want to reset?');"></td>
	</tr></table>
</td></tr>
</table>
</fieldset>
</form>
<?php
CloseDatabase($dbi);
?>
<script>
$(document).ready(function() {
	$("#fname").change(function() {
                var re = /^[a-zA-Z0-9\._\-]*$/;
                if (!re.test($(this).val())) {
                        alert("Not a valid first name");
                        $(this).val("");
                        $(this).focus();
                }
        });

	$("#surname").change(function() {
                var re = /^[a-zA-Z0-9\._\-]*$/;
                if (!re.test($(this).val())) {
                        alert("Not a valid surname");
                        $(this).val("");
                        $(this).focus();
                }
        });

	$("#phone").change(function() {
                var re = /^\+[0-9 \-\(\)]+$/;
                if (!re.test($(this).val())) {
                        alert("Not a valid phone number");
                        $(this).val("");
                        $(this).focus();
                }
        });

	$("#email").change(function() {
                var re = /^[a-zA-Z0-9.!#$%&'*+\=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
                if (!re.test($(this).val())) {
                        alert("Not a valid email");
                        $(this).focus();
                }
        });

});
</script>
</body>
</html>
