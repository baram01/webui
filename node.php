<?php
/*
    Copyright (C) 2003-2019 3 Youngs, Inc

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

//$dbi = OpenDatabase($dbhost, $dbuname, $dbpass, $dbname);
$dbi = OpenDatabase($db_config);

if (checkLoginXML($_COOKIE["login"],$dbi) < 5) {
	CloseDatabase($dbi);
        echo "<script language=\"JavaScript\"> top.location.href=\"index.php?module=main\"; </script>";
}

?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="js/jquery-ui/jquery-ui.css" />
<link rel="stylesheet" type="text/css" href="css/style.css" />
<style>
.ui-autocomplete-loading {
    background: white url("images/ui-anim_basic_16x16.gif") right center no-repeat;
}
</style>
<script src="js/jquery.js"></script>
<script src="js/jquery-ui/jquery-ui.js"></script>
<script src="js/tacacs.js"></script>
</head>
<body>
<!-- <script language="JavaScript" src="js/ajax.js"></script> -->
<!-- <script language="JavaScript" src="js/autosuggest.js"></script> -->
<script language="Javascript">
<!--
function _add(obj) {
        resultForm = document.serviceform;

        if (resultForm.option.value == "2") {
                resultForm.id.disabled = false;
                resultForm.name.disabled = false;
                resultForm.option.value = "1";
                resultForm._submit.value = "Add";
                _hover2();
        } else {
                addMe(obj);
        }
}

function _check_serviceform(svcform)
{
	var ret = true;
	var form = svcform;
	var msg = "";

<?php
  if ($_service == 56) {
     echo "
	if (!form.seq.value) {
		msg = \"Sequence cannot be blank\\n\";
		form.seq.focus();
		ret = false;
	} else {
		var anum=/(^\d+$)/;
		if (!anum.test(form.seq.value)) {
			msg = \"Sequence is not valid\\n\";
			form.seq.focus();
			ret = false;
		}
	}\n";
  } else {
     echo "
	if ((form.service.value==59)&&!form.svc_name.value) {
		msg = \"Service name cannot be blank\\n\";
		form.svc_name.focus();
		ret = false;
	}\n";
  }
?>

	if (!form.auth.value) {
		msg += "Auth is required\n";
		if (ret) form.auth.focus();
		ret = false;
	}

	if (!form.value1.value) {
		msg = <?php echo ($_service==56)?"Command":"Attribute";?>+" cannot be blank\n";
		if (ret) form.value1.focus();
		ret = false;
	}

	if (!form.value.value) {
		msg = msg + <?php echo ($_service==56)?"Arguments":"Value";?>+" cannot be blank\n";
		if (ret) form.value.focus();
		ret = false;
	}

	if (ret) form.attrfmt.disabled = false;

	if (msg) alert(msg);

	return ret;
}

function _delete(svcform, obj) {
        var msg = "Do you want really want to delete "+obj+"?";
	var form = svcform;
	var ret = false;

        if (confirm(msg)) {
		ret = true;
                form.seq.value = obj[0];
                form.service.value = obj[1];
                form.type.value = obj[2];
                form.value.value = obj[3];
                form.value1.value = obj[4];
                form.vid.value = obj[5];
                form.svc_name.value = obj[6];
                form.attrid.value = obj[7];
                form.auth.value = obj[9];
                form.option.value = "3";
                form.submit();
        }
	return ret;
}

function _modify(obj) {
	var form = document.serviceform;

        form.seq.value = obj[0];
        form.service.value = obj[1];
        form.type.value = obj[2];
        form.value.value = obj[3];
        form.value1.value = obj[4];
        form.vid.value = obj[5];
        form.svc_name.value = obj[6];
        form.attrid.value = obj[7];
        form.attrfmt.value = obj[8];
        form.auth.value = obj[9];
        form.option.value = "2";
        form._submit.value = "Modify";
}

function getAttribResults() {
	if (getRequest.readyState == 4) {
		if (getRequest.responseXML) {
			var xmldoc = getRequest.responseXML.getElementsByTagName('attribute')[0];
			attribs.length = 0;
			for (var i=0; i < xmldoc.childNodes.length; i++) {
				if (xmldoc.getElementsByTagName('name')[i]) {
					attribs[i] = xmldoc.getElementsByTagName('name')[i].firstChild.nodeValue;
				}
			}
		}
	}
}

function getCommandResults() {
	if (getRequest.readyState == 4) {
		if (getRequest.responseXML) {
			var xmldoc = getRequest.responseXML.getElementsByTagName('command')[0];
			attribs.length = 0;
			for (var i=0; i < xmldoc.childNodes.length; i++) {
				if (xmldoc.getElementsByTagName('name')[i]) {
					commands[i] = xmldoc.getElementsByTagName('name')[i].firstChild.nodeValue;
				}
			}
		}
	}
}

function _getAttribs(obj) {
	resultForm = document.serviceform;
	getQueryXML(getAttribResults,"attribute","vid="+obj.value);
}

var svc_info = new Object;
var attribs = new Array();
var commands = new Array();

<?php
$attr_auth = array("","tacacs","radius");
$_sname = ($_service==56)?"command":"attribute";
switch ($option) {
  case 1:
	$result = @SQLQuery("INSERT INTO node2 (id, uid, seq, service, svc_name, type, value, value1, attrid, attrfmt, vid, auth)  VALUES ($pid, '$uid', $seq, $service, '$svc_name', $type, '$value', '$value1', $attrid, $attrfmt, $vid, $auth)", $dbi);
	if (!@SQLError($dbi))
		Audit("node2","add","UID=".$uid." SVC=".$_sname,$dbi);
	break;
  case 2:
/*	$result = @SQLQuery("UPDATE node2 set service=$service, seq=$seq, type=$type, value='$value', value1='$value1', attrid=$attrid, attrfmt=$attrfmt, auth=$auth WHERE uid='$uid'", $dbi);
	if (!@SQLError($dbi))
		Audit("node2","change","UID=".$uid." SVC=".$_sname,$dbi); */
	break;
  case 3:
	if ($service == 59) {
		$result = @SQLQuery("DELETE FROM node2 WHERE uid='$uid' AND service=$service AND seq=$seq AND svc_name='$svc_name' AND type=$type AND value='$value' AND value1='$value1' AND vid=$vid AND auth=$auth", $dbi); 
	} else {
		$result = @SQLQuery("DELETE FROM node2 WHERE uid='$uid' AND service=$service AND seq=$seq AND type=$type AND value='$value' AND value1='$value1' AND vid=$vid AND auth=$auth", $dbi); 
	}
	if (!@SQLError($dbi))
		Audit("node2","delete","UID=".$uid." SVC=".$_sname,$dbi);
	break;
}

if ($debug) {
	$_ERROR.=@SQlError($dbi);
}

$node_type = array();
$svc_type = array();

$node_type[50]          = "arg";
$node_type[51]          = "optarg";
$svc_type[52]           = "exec";
$svc_type[53]           = "slip";
$svc_type[54]           = "ppp";
$svc_type[55]           = "arap";
//$svc_type[56]         = "cmd";
$node_type[57]          = "permit";
$node_type[58]          = "deny";
$svc_type[59]           = "svc";

$cmd_array = array();
$svc_array = array();
$vnd_array = array();
$cmd = 0;
$svc = 0;

$result = @SQLQuery("SELECT id, name FROM vendor ORDER by name", $dbi);
while ($row = @SQLFetchRow($result)) {
	$vnd_array[$row[0]]=$row[0]?$row[1]:"All";
}

$_where = ($_service==56)?"AND service=56":"AND service!=56";

$result = @SQLQuery("SELECT seq, service, svc_name, type, value, value1, attrid, attrfmt, vid, auth FROM node2 WHERE uid='$uid' $_where ORDER BY service, seq, value1", $dbi);
while ($row = @SQLFetchArray($result)) {
	echo "svc_info[$svc] = new Array();\n"
	    ."svc_info[$svc][0] = ".$row["seq"].";\n"
	    ."svc_info[$svc][1] = ".$row["service"].";\n"
	    ."svc_info[$svc][2] = ".$row["type"].";\n"
	    ."svc_info[$svc][3] = '".$row["value"]."';\n"
	    ."svc_info[$svc][4] = '".$row["value1"]."';\n"
	    ."svc_info[$svc][5] = ".$row["vid"].";\n"
	    ."svc_info[$svc][6] = '".$row["svc_name"]."';\n"
	    ."svc_info[$svc][7] = '".$row["attrid"]."';\n"
	    ."svc_info[$svc][8] = '".$row["attrfmt"]."';\n"
	    ."svc_info[$svc][9] = '".$row["auth"]."';\n";
        $svc_array[$svc]["seq"] = $row["seq"];
        $svc_array[$svc]["service"] = $row["service"];
        $svc_array[$svc]["type"] = $row["type"];
        $svc_array[$svc]["value"] = $row["value"];
        $svc_array[$svc]["value1"] = $row["value1"];
        $svc_array[$svc]["vid"] = $row["vid"];
        $svc_array[$svc]["svc_name"] = $row["svc_name"];
        $svc_array[$svc]["attrid"] = $row["attrid"];
        $svc_array[$svc]["attrfmt"] = $row["attrfmt"];
        $svc_array[$svc]["auth"] = $row["auth"];
        $svc++;
}
echo "\n";
?>
-->
</script>
<?php
if ($_service == 56) 
	include("node_command.php");
else
	include("node_service.php");

echo $_ERROR;
?>
</body>
</html>
