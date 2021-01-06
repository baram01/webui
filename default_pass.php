<?php
/*
    Copyright (C) 2003-2019 Young Consulting, Inc

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

Changes:
*/

//if (!eregi("index.php",$_SERVER['PHP_SELF'])) {
if (!preg_match("/index.php/",$_SERVER['PHP_SELF'])) {
        Header("Location: index.php");
        die();
}

if ($_ret < 5) {
        echo "<script language=\"JavaScript\"> top.location.href=\"?module=main\"; </script>";
}
?>

<script language="Javascript" src="js/user.js"></script>

<?php
$where = "";
if ($option == 1) {
	$sqlcmd = "UPDATE user set";
	if ($re_password) $sqlcmd .= " password='".unixcrypt($password)."'";
	if ($re_enable) $sqlcmd .= ", enable='".unixcrypt($enable)."'";
	$checkuid = $_POST['chk_uid'];
	if (!empty($checkuid)) {
		$N = count($checkuid);
		echo "<P><font color=\"red\">To update $N.</font></P>";
		for ($i=0; $i < $N; $i++) {
			$result = @SQLQuery("$sqlcmd WHERE uid='".$checkuid[$i]."'",$dbi);
			if (!@SQLError($dbi))
				echo "<P><font color=\"red\">User(".$checkuid[$i].") modified.</font></P>";
		}
	} else {
			echo "<P><font color=\"red\">None to modify.</font></P>";
	}

//	$result = @SQLQuery("$sqlcmd WHERE uid='$uid'",$dbi);
//	if (!@SQLError($dbi))
//		echo "<P><font color=\"red\">User($uid) modified.</font></P>";
}

if ($debug) {
	$_ERROR.=@SQlError($dbi);
}

?>
<form name="userform" method="post" action="?module=default_pass">
<fieldset class="_collapsible"><legend>Set User Default Password</legend>
<table border=0 width="100%">
<tr><td>
	<div id="_user" class="_scrollwindow">
	<table border=1 class="_table2">
	<tr><th>&nbsp;</th><th>ID</th><th>User</th><th>Group</th><th>Comment</th><th>Expires</th><th>ACL</th>
<?php
$result = @SQLQuery("SELECT disable, id, uid, gid, comment, expires, acl_id FROM user WHERE user=1 $where ORDER BY id", $dbi);
while ($row = @SQLFetchArray($result)) {
	$style = "";
	$acl = $row["acl_id"]?$row["acl_id"]:"&nbsp;";
	if ($row["disable"]) $style="style=\"color:red\"";
	else {
                if (strcmp($row["expires"],"0000-00-00 00:00:00")) {
                        $_now = strtotime("now");
                        $_expires = strtotime($row["expires"]);

                        if ($_now > $_expires) {
                                $style="style=\"color:red\"";
                        } else if ((($_expires - $_now) <= $changetime*24*60*60)) {
                                $style="style=\"color:orange\"";
                        }
                }
	}

	echo "<tr><td><input type=\"checkbox\" name=\"chk_uid[]\" value=\"".$row["uid"]."\"></td>"
	    ."<td $style>".$row["id"]."</td>"
	    ."<td width=90 $style>".$row["uid"]."</td>"
	    ."<td width=90 $style>".$row["gid"]."</td>"
	    ."<td width=190 $style>".$row["comment"]."</td>"
	    ."<td $style>".$row["expires"]."</td>"
	    ."<td $style>".$acl."</td>"
	   // ."<td><a href=\"javascript:_modify('".$row["id"]."','".$row["uid"]."','1')\" title=\"Modify User\"><img src=\"images/modify.gif\" width=25 border=0></a>"."</td>"
	    ."</tr>\n";
}
?>
	</table>
	</div>
<tr><td>
	<table class="_table">
    	<tr class="_passwords"><td width="50">Password:</td><td><input type="password" name="password" size="20"></td>
	    <td>&nbsp;&nbsp;</td>
    	    <td width="100">Re-Password:</td><td><input type="password" name="re_password" size="20" onblur="_checkpass(this,document.userform.password,document.userform.min_passwd)"></td>
    	<tr class="_passwords"><td width="50">Enable:</td><td><input type="password" name="enable" size="20"></td>
	    <td>&nbsp;&nbsp;</td>
    	    <td width="100">Re-Enable:</td><td><input type="password" name="re_enable" size="20" onblur="_checkpass(this,document.userform.enable)"></td>
	<tr><td width="50"><input name="option" value="1" type="hidden"><input name="min_passwd" type="hidden"></td><td><input type="submit" name="_submit" value="Change" width=8> <input type="reset" onClick="return confirm('Are you sure you want to reset the data?')"></td>
	</table>
</table>
</fieldset>
</form>

<?php
echo "<script language=\"Javascript\">\n"
    ."    document.userform.min_passwd.value = $pass_size;\n"
    ."</script>\n";
?>

