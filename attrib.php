<?php
/*
    Copyright (C) 2003-2020 3 Youngs, Inc

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

********************************

04/04/202 Andrew Young
	add focus for description
03/29/2019 Andrew Young
	add attribute octets
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
<script language="Javascript" src="js/result.js"></script>
<script language="JavaScript">
<!--
var _lastID = 0;

function _add(obj) {
        resultForm = document.attribform;

        if (resultForm.option.value == "2") {
                resultForm.id.disabled = false;
                resultForm.id.value = resultForm._lastID.value;
                resultForm.name.disabled = false;
                resultForm.option.value = "1";
                resultForm._submit.value = "Add";
                _hover2();
        } else {
                resultForm.id.value = resultForm._lastID.value;
                addMe(obj);
        }
}

function _delete(id, name, vid, auth, vendor)
{
	var vendor_name = vendor?vendor:"All";
	var msg = "Do you want really to delete Attribute="+name+" for Vendor="+vendor_name+"?";

	if (confirm(msg)) {
		document.attribform.id.value = id;
		document.attribform.vid.value = vid;
		document.attribform.auth.value = auth;
		document.attribform.option.value = "3";
		document.attribform.submit();
	}
}

function _hover(id, vid)
{
	resultForm = document.attribform;
	getQueryXML(getVendorResults,"attribute","id="+id+"&vid="+vid);
}

function _hover2()
{
	document.attribform.id.value = _lastID;
	document.attribform.name.value = "";
	document.attribform.vid.value = "";
	document.attribform.descr.value = "";
	document.attribform.type.value = "0";
	document.attribform.auth.value = "0";
}

function getVendorResults()
{
	if (getRequest.readyState == 4) {
		var xmldoc = getRequest.responseXML.documentElement;

		resultForm.id.value = xmldoc.getElementsByTagName('id')[0].firstChild.nodeValue;
		resultForm.name.value = xmldoc.getElementsByTagName('name')[0].firstChild.nodeValue;
		if (xmldoc.getElementsByTagName('descr')[0].firstChild != null)
			resultForm.descr.value = xmldoc.getElementsByTagName('descr')[0].firstChild.nodeValue;
		else
			resultForm.descr.value = "";
		resultForm.type.value = xmldoc.getElementsByTagName('type')[0].firstChild.nodeValue;
		resultForm.auth.value = xmldoc.getElementsByTagName('auth')[0].firstChild.nodeValue;
		resultForm.vid.value = xmldoc.getElementsByTagName('vid')[0].firstChild.nodeValue;
	}
}

function _modify(id, vid, auth)
{
	resultForm = document.attribform;
	resultForm.old_vid.value = vid;
	getQueryXML(getVendorResults,"attribute","id="+id+"&vid="+vid+"&auth="+auth);
	resultForm.id.disabled = true;
	resultForm.name.disabled = true;
	resultForm.descr.focus();
	resultForm.option.value = "2";
	resultForm._submit.value = "Modify";
	document.getElementById("_attribadd").style.display = "";
}

function _required()
{
	var form = document.attribform;
	var ret  = true;
	var focus;
	var msg  = "";

	if (! form.id.value) {
		msg = msg + "ID is required.\n";
		focus = form.id;
		ret = false;
	} else {
		var anum=/(^\d+$)/;
		if (!anum.test(form.id.value)) {
			msg = msg + "ID is not valid.\n";
			focus = form.id;
			ret = false;
		}
	}
		
	if (! form.name.value) {
		msg = msg + "Name is required.\n";
		if (!focus) {
			focus = form.name;
		}
		ret = false;
	}
	
	if (msg) alert(msg);
	if (focus) focus.focus();
	if (ret) { form.id.disabled = false; form.name.disabled = false; }
	return ret;
}

//-->
</script>
<?php
switch ($option) {
   case 1:
	$result = @SQLQuery("INSERT INTO attribute (id, name, descr, type, auth, vid) VALUES ($id, '$name', '$descr', $type, $auth, $vid)", $dbi);
	break;
   case 2:
	$_v = $vid;
	if ($vid != $old_vid) $_v = $old_vid;
	$result = @SQLQuery("UPDATE attribute SET descr='$descr', type=$type, auth=$auth, vid=$vid WHERE id=$id AND vid=$_v", $dbi);
	break;
   case 3:
	$result = @SQLQuery("SELECT id FROM node2 WHERE attr=$id AND vid=$vid AND auth=$auth", $dbi);
	if (@SQLNumRows($result)<1) {
		$result = @SQLQuery("DELETE FROM attribute WHERE id=$id AND vid=$vid AND auth=$auth", $dbi);
	} else {
		echo "<P><font color=\"red\">Cannot delete Attribute($id, $name) for Vendor($vid). There are too many dependancies.</font></P>";
	}
	break;
}
if ($debug) {
	$_ERROR=$_ERROR." ".@SQLError($dbi);
}

$json_attr_format_file =  $target_dir."attr_format.json";
$attr_format = json_decode(file_get_contents($json_attr_format_file));
$attr_auth = array("","tacacs","radius");

$result = @SQLQuery("SELECT id, name FROM vendor ORDER BY name", $dbi);
while ($row = @SQLFetchArray($result)) {
	$vendor[$row[0]]=$row[0]?$row[1]:"All";
}
?>
<form name="attribform" method="post" action="?menu=admin&module=attrib">
<fieldset class=" collapsible"><legend>Attributes <?php if ($_ret > 9) { echo "<a href=\"javascript:_add('_attribadd')\"><img src=\"images/plus-new.gif\" border=\"0\" /></a>"; } ?></legend>
<table border=0 width="100%">
<tr><td>
	<div id="_attribadd" style="display:none">
        <fieldset class="_collapsible">
	<table class="_table">
	<tr><td>ID:</td><td><input type="text" id="id" name="id" size=6 onChange="return _verify(this,'num');"></td></tr>
	<tr><td>Attribute:</td><td><input type="text" id="name" name="name" size=20></td></tr>
	<tr><td>Description:</td><td><input type="text" id="descr" name="descr" size=20></td></tr>
	<tr><td>Format:</td><td><select name="type"><?php
		foreach ($attr_format as $i=>$j) {
			echo "<option value=\"$i\">$j</option>";
		}
	    ?></select></td></tr>
	<tr><td>Authen:</td><td><select name="auth"><?php
		foreach ($attr_auth as $i=>$j) {
			echo "<option value=\"$i\">$j</option>";
		}
	    ?></select></td></tr>
	<tr><td>Vendor:</td><td><select name="vid"><?php
		foreach ($vendor as $i=>$j) {
			echo "<option value=\"$i\">$j</option>";
		}
	    ?></select></td></tr>
	<tr><td><input name="option" value="1" type="hidden"><input name="old_vid" value="0" type="hidden"></td><td><input type="submit" name="_submit" value="Add" onclick="return _required();"> <input type="reset" onClick="return confirm('Are you sure you want to reset the data?')"></td></tr>
	</table>
	</fieldset>
	</div>
</td></tr>
<tr><td><div id="_results0"></div></td></tr>
</table>
</fieldset>
</form>

<script>
$(document).ready(function() {
        $('#id').change(function() {
                if (isNaN($(this).val())) {
                        alert("Only integers are allowed");
                        $(this).val("");
                        $(this).focus();
                }
        });

        $('#name').change(function() {
		var re = /^([a-zA-Z0-9_\-]+)$/;
                if (!re.test($(this).val())) {
                        alert("Not allowed characters are inputted");
                        $(this).val("");
                        $(this).focus();
                }
        });

        $('#descr').change(function() {
                if (/[<>]/.test($(this).val())) {
                        alert("Not allowed characters are inputted");
                        $(this).val("");
                        $(this).focus();
                }
        });

        var src = "result.php?_ret="+admin_priv_lvl+"&_table=attribute";
            src += "&offset=0&vrows="+admin_vrows+"&_index=0";
        $.get(src, function (data, status) {
                document.getElementById("_results0").innerHTML = data;
        });
});
</script>

