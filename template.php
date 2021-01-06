<?php
/*
    Copyright (C) 2003-2009 Young Consulting, Inc

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

//if (!eregi("index.php",$_SERVER['PHP_SELF'])) {
if (!preg_match("/index.php/",$_SERVER['PHP_SELF'])) {
	Header("Location: index.php");
	die();
}

if (!$_ret) {
	echo "<script language=\"JavaScript\"> top.location.href=\"?module=main\"; </script>";
}
?>
<form name="_templateform" method="post" action="index.php?module=_template&option=1">
<fieldset class=" collapsible"><legend>Template</legend>
<table border=0 width="100%">
<tr><td>
        <div id="_template" class="_scrollwindow">
	<table border=1 width="100%" class="_table2">
           <tr><th>Titles</th>
<?php
$formdata = array();
/*
$sqlcmd = "SELECT * FROM _template";
$result = SQLQuery($sqlcmd, $dbi);
while ($row=SQLFetchArray($result)) {
	if (($row["id"] == $id)&&($option!=1)) {
		$formdata = $row;
		if (!strcmp($row["start"],"0000-00-00")) $formdata["start"]="";
		if (!strcmp($row["end"],"0000-00-00")) $formdata["end"]="";

	}
	echo "<script language=\"Javascript\">"
	    ."vendor_info[".$row["id"]."] = new Array();"
	    ."vendor_info[".$row["id"]."][0] = ".$row["id"].";"
	    ."vendor_info[".$row["id"]."][1] = '".$row["name"]."';"
	    ."vendor_info[".$row["id"]."][2] = '".$row["url"]."';"
	    ."vendor_info[".$row["id"]."][3] = '".$row["scname"]."';"
	    ."vendor_info[".$row["id"]."][4] = '".$row["scemail"]."';"
	    ."vendor_info[".$row["id"]."][5] = '".$row["tsphone"]."';"
	    ."vendor_info[".$row["id"]."][6] = '".$row["tsemail"]."';"
	    ."vendor_info[".$row["id"]."][7] = '".$row["contract"]."';"
	    ."vendor_info[".$row["id"]."][8] = '".$row["start"]."';"
	    ."vendor_info[".$row["id"]."][9] = '".$row["end"]."';</script>";
	if ($row["id"]) {
	    echo "<tr><td>".$row["id"]."</td>"
		."<td>".$row["name"]."</td>"
		."<td>".$row["contract"]."</td>"
		."<td>".$row["tsphone"]."</td>"
		."<td><center>".$row["start"]."</center></td>"
		."<td><center>".$row["end"]."</center></td>"
		."<td><img src=\"images/modify.gif\" width=25 border=0 onclick=\"_modify2(vendor_info[".$row["id"]."]);\"></td>";
	    echo "<td><input type=\"image\" src=\"images/trash.gif\" width=25 border=0 onclick=\"_delete(".$row["id"].")\"></td></tr>\n";
	}
}
SQLFreeResult($result);
*/
?>
	</table>
        </div>
</td></tr>
<tr><td>
	<table class="_table">
	<tr><td>Template ID:</td><td><input type="text" name="id" size=6 onChange="return _verify(this,'num');" value="<?php echo $formdata["id"]; ?>" <?php if ($option==4) echo "readOnly> <font color=\"red\">**This field is read-only</font>"; else echo ">"; ?></td></tr>
	</table>
</td></tr>
<tr><td>
	<center><input type="submit" name="_submit" value="Add" onclick="return _required();">
		<input type="reset" onClick="return confirm('Are you sure you want to reset the data?')"></center>
</td></tr>
</table>
</fieldset>
</form>
