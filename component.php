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

if ($_ret < 5) {
	echo "<script language=\"JavaScript\"> top.location.href=\"?module=main\"; </script>";
}
?>
<script language="JavaScript">
<!--
function _delete(obj)
{
	var msg = "Do you want really to delete "+obj+"?";

	if (confirm(msg)) {
		document.compform.id.value = obj;
		document.compform.option.value = "3";
		document.compform.submit();
	}
}

function _hover(comp)
{
	resultForm = document.compform;
	getQueryXML(getCompResults,"vcomponent","id="+comp);
}

function _hover2(comp)
{
	document.compform.id.value = "";
	document.compform.vid.value = "";
	document.compform.component.value = "";
	document.compform.description.value = "";
}

function getCompResults()
{
	if (getRequest.readyState == 4) {
		var xmldoc = getRequest.responseXML.documentElement;

		resultForm.id.value = xmldoc.getElementsByTagName('id')[0].firstChild.nodeValue;
		resultForm.vid.value = xmldoc.getElementsByTagName('vid')[0].firstChild.nodeValue;
		resultForm.component.value = xmldoc.getElementsByTagName('component')[0].firstChild.nodeValue;
		if (xmldoc.getElementsByTagName('description')[0].firstChild != null)
			compform.description.value = xmldoc.getElementsByTagName('description')[0].firstChild.nodeValue;
		else
			compform.description.value = "";
	}
	
}

function _modify(comp)
{
	resultForm = document.compform;
	getQueryXML(getCompResults,"vcomponent","id="+comp);
	resultForm.option.value = "2";
	resultForm._submit.value = "Modify";
}

function _required()
{
	var form = document.compform;
	var ret  = true;
	var focus;
	var msg  = "";

	if (! form.id.value) {
		msg = msg + "ID is required.\n";
		focus = form.id;
		ret = false;
	}

	if (! form.description.value) {
		msg = msg + "Description is required.\n";
		if (!focus) focus = form.description;
		ret = false;
	}

	if (msg) alert(msg);
	if (focus) focus.focus();
	return ret;
}

//-->
</script>
<?php
switch ($option) {
   case 1:
	$sqlcmd = sprintf("INSERT INTO vcomponent VALUES(%d,%d,%d,'%s')", $id,$vid, $component, $description );
	break;
   case 2:
	$sqlcmd = sprintf("UPDATE vcomponent SET vid=%d, component=%d, description='%s' WHERE id=%d", $vid, $component, $description, $id);
	break;
   case 3:
	$sqlcmd = sprintf("DELETE FROM vcomponent WHERE id=%d", $id);
	break;
   default:
	$sqlcmd = "";
}
if ($sqlcmd != "") {
	if (!SQLQuery($sqlcmd,$dbi)) {
		$_ERROR="Cannot do transaction. SQL Error:- ".SQLError()." ".$sqlcmd;
	}
}
?>
<form name="compform" method="post" action="?module=component">
<fieldset class="collapsible"><legend>Vendor Components</legend>
<table border=0 width="100%">
<tr><td>
        <div id="_comps" class="_scrollwindow">
	<table border=1 width="100%" class="_table2">
           <tr><th>ID</th><th>Vendor</th><th>Component</th><th>Description</th>
<?php
$comp_array = array();
$vnd_array = array();

$result = @SQLQuery("SELECT id, description FROM component", $dbi);
while ($row = @SQLFetchRow($result)) {
	$comp_array[$row[0]] = $row[1];
}
@SQLFreeResult($result);

$result = @SQLQuery("SELECT id, name FROM vendor ORDER BY name", $dbi);
while ($row = @SQLFetchRow($result)) {
	$vnd_array[$row[0]] = $row[1];
}
@SQlFreeResult($result);

$result = @SQLQuery("SELECT id, vid, component, description FROM vcomponent ORDER BY id", $dbi);
while ($row=@SQLFetchArray($result)) {
	if ($row["id"]) {
	    echo "<tr><td>".$row["id"]."</td>"
		."<td>".$vnd_array[$row["vid"]]."</td>"
		."<td>".$comp_array[$row["component"]]."</td>"
		."<td>".$row["description"]."</td>"
		."<td><a href=\"javascript:_modify('".$row["id"]."')\"><img src=\"images/modify.gif\" width=25 border=0></img></a></td>";
	    echo "<td><a href=\"javascript:_delete('".$row["id"]."')\"><img src=\"images/trash.gif\" width=25 border=0></img></a></td></tr>\n";
	}
}
SQLFreeResult($result);
?>
	</table>
        </div>
</td></tr>
<tr><td>
	<table class="_table">
	<tr><td>ID:</td><td><input type="text" name="id" size=6 value="0"></td></tr>
	<tr><td>Vendor:</td><td><select name="vid" style="width: 150px"><?php
		foreach ($vnd_array as $i=>$j) {
			if ($i)
				echo "<option value=\"$i\">$j</option>";
		}
	    ?></select></td></tr>
	<tr><td>Component:</td><td><select name="component" style="width: 150px"><?php
		foreach ($comp_array as $i=>$j) {
			echo "<option value=\"$i\">$j</option>";
		}
	    ?></td></tr>
	<tr><td>Description:</td><td><input type="text" name="description" size=70></td></tr>
	<tr><td><input name="option" value="1" type="hidden"></td><td><input type="submit" name="_submit" value="Add" onclick="return _required();"> <input type="reset" onClick="return confirm('Are you sure you want to reset the data?')"></td></tr>
	</table>
</td></tr>
</table>
</fieldset>
</form>
