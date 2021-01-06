<?php
/*
    Copyright (C) 2003-2020 Young Consulting, Inc

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

04/12/2019 - Andrew Young
	Change how data is displayed
08/26/2016 - Andrew Young
	Remove HKEY for diplay
	Add vendor for display
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
function _add(obj) {
        resultForm = document.nasform;

        if (resultForm.option.value == "2") {
                resultForm.ip.disabled = false;
                resultForm.option.value = "1";
                resultForm._submit.value = "Add";
                _hover2();
        } else {
                addMe(obj);
        }
}

function _delete(obj)
{
	var msg = "Do you want really to delete "+obj+"?";

	if (confirm(msg)) {
		document.nasform.ip.value = obj;
		document.nasform.option.value = "3";
		document.nasform.submit();
	}
}

function _hover(nas)
{
	resultForm = document.nasform;
	getQueryXML(getVendorResults,"host","ip="+nas+"&host=2");
}

function _hover2()
{
	document.nasform.ip.value = "";
	document.nasform.hostgroup.value = "";
	document.nasform.hkey.value = "";
	document.nasform.prompt1.value = "";
	document.nasform.loginacl.value = "0";
	document.nasform.enableacl.value = "0";
	document.nasform.enable.value = "";
	document.nasform.vendor.value = "";
}

function getNASResults()
{
	if (getRequest.readyState == 4) {
		var xmldoc = getRequest.responseXML.documentElement;

		resultForm.ip.value = xmldoc.getElementsByTagName('ip')[0].firstChild.nodeValue;
		if (xmldoc.getElementsByTagName('hostgroup')[0].firstChild != null)
			resultForm.hostgroup.value = xmldoc.getElementsByTagName('hostgroup')[0].firstChild.nodeValue;
		else
			resultForm.hostgroup.value = "";
		if (xmldoc.getElementsByTagName('hkey')[0].firstChild != null)
			resultForm.hkey.value = xmldoc.getElementsByTagName('hkey')[0].firstChild.nodeValue;
		else
			resultForm.hkey.value = "";
		if (xmldoc.getElementsByTagName('enable')[0].firstChild != null)
			resultForm.enable.value = xmldoc.getElementsByTagName('enable')[0].firstChild.nodeValue;
		else
			resultForm.enable.value = "";
		if (xmldoc.getElementsByTagName('prompt')[0].firstChild != null)
			resultForm.prompt1.value = xmldoc.getElementsByTagName('prompt')[0].firstChild.nodeValue;
		else
			nasform.prompt1.value = "";
		resultForm.loginacl.value = xmldoc.getElementsByTagName('loginacl')[0].firstChild.nodeValue;
		resultForm.enableacl.value = xmldoc.getElementsByTagName('enableacl')[0].firstChild.nodeValue;
		resultForm.vendor.value = xmldoc.getElementsByTagName('vendor')[0].firstChild.nodeValue;
	}
	
}

function _modify(nas)
{
	resultForm = document.nasform;
	getQueryXML(getNASResults,"host","ip="+nas+"&host=2");
	document.nasform.ip.disabled = true;
	resultForm.option.value = "2";
	resultForm._submit.value = "Modify";
	document.getElementById("_nasadd").style.display = "";
}

function _group(nasgroup)
{
	document.nasform.group.value = nasgroup;
	document.nasform.ip.disabled = true;
	document.nasform.option.value = "4";
	document.nasform.action = "?module=nas";
	document.nasform.submit();
}

function _required()
{
	var form = document.nasform;
	var ret  = true;
	var focus;
	var msg  = "";

	if (! form.ip.value) {
		msg = msg + "Name is required.\n";
		focus = form.ip;
		ret = false;
	}
		
	if (! form.hkey.value) {
		msg = msg + "HKEY is not required but recommended.\n";
	}
	
	if (! form.prompt1.value) {
		msg = msg + "Prompt is not required but recommended.\n";
	}
	
	if (msg) alert(msg);
	if (focus) focus.focus();
	if (ret) form.ip.disabled = false;
	return ret;
}

//-->
</script>
<?php
switch ($option) {
   case 1:
	$crypt_enable = "''";
//	if ($enable) $crypt_enable = unixcrypt($enable);
	if ($enable) $crypt_enable = "ENCRYPT('$enable')";
	$result = @SQLQuery("INSERT INTO host (ip, hkey, enable, prompt, loginacl, enableacl, vendor, host) VALUES('$ip','$hkey',$crypt_enable,'$prompt1',$loginacl,$enableacl,$vendor,2)", $dbi);
	if (!@SQLError($dbi))
		Audit("nas_group","add","GROUP=".$ip,$dbi);
	break;
   case 2:
	$sqlcmd = "";
	if (!$enable) $sqlcmd = ", enable=''";
	if ($re_enable) $sqlcmd = ", enable=ENCRYPT('$enable')";
	$result = @SQLQuery("UPDATE host SET hkey='$hkey', prompt='$prompt1', loginacl=$loginacl, enableacl=$enableacl, vendor=$vendor $sqlcmd WHERE ip='$ip'", $dbi);
        if (!@SQLError($dbi)) {
                echo "<P><font color=\"green\">NAS Group($ip) modified.</font></P>";
		Audit("nas_group","change","GROUP=".$ip,$dbi);
	}
	break;
   case 3:
	$result = @SQLQuery("SELECT ip FROM host WHERE hostgroup='$ip'", $dbi);
	if (@SQLNumRows($result) > 0) 
                echo "<P><font color=\"red\">NAS Group($ip) cannot be deleted. Too many dependancies.</font></P>";
	else {
		$result = @SQLQuery("DELETE FROM host WHERE ip='$ip'", $dbi);
		Audit("nas_group","delete","GROUP=".$ip,$dbi);
	}
	break;
}

if ($debug) {
	$_ERROR = $_ERROR.@SQLError($dbi);
}

$acls = array();
$vnd_array = array();

$result = @SQLQuery("SELECT id, name FROM vendor ORDER BY name", $dbi);
while ($row = @SQLFetchRow($result)) {
	$vnd_array[$row[0]]=$row[1];
}
@SQLFreeResult($result);

$result = @SQLQuery("SELECT id FROM acl WHERE type!=1 GROUP BY id", $dbi);
$i = 0;
while ($row = @SQLFetchRow($result)) {
	$acls[$i] = $row[0];
	$i++;
}
@SQLFreeResult($result);
?>
<form name="nasform" method="post" action="?menu=admin&module=nas_group">
<fieldset class=" collapsible"><legend>NAS Group <?php if ($_ret > 9) { echo "<a href=\"javascript:_add('_nasadd')\"><img src=\"images/plus-new.gif\" border=\"0\" /></a>"; } ?></legend>
<table border=0 width="100%">
<tr><td>
        <div id="_nasadd" style="display:none">
        <fieldset class="_collapsible">
	<table class="_table">
	<tr><td>Name:</td><td><input type="text" id="ip" name="ip"></td>
	    <td>&nbsp;&nbsp;<input type="hidden" name="network" value='0'><input type="hidden" name="submask"></td>
	    <td>Vendor:</td><td><select name="vendor" style="width: 150px"><?php
		foreach ($vnd_array as $i=>$value) {
			echo "<option value=\"$i\">$value</option>";
		}
	     ?></select></td></tr>
	<tr><td>HKey:</td><td><input type="text" id="hkey" name="hkey"></td>
	    <td>&nbsp;&nbsp;</td>
	    <td></td><td><input type="hidden" name="hostgroup"><input type="hidden" name="group"></td></tr>
	<tr><td>Login Policy:</td><td><select name="loginacl"><option value="0"></option><?php
		foreach ($acls as $acl) {
			echo "<option value=\"$acl\">$acl</option>";
		} 
	    ?></select></td>
	    <td>&nbsp;&nbsp;</td>
	    <td>Enable Policy:</td><td><select name="enableacl"><option value="0"></option><?php
		foreach ($acls as $acl) {
			echo "<option value=\"$acl\">$acl</option>";
		}
	     ?></select></td></tr>
	<tr><td>Enable:</td><td><input type="password" name="enable"></td>
	    <td>&nbsp;&nbsp;</td>
	    <td>Re-Enable:</td><td><input type="password" name="re_enable" onChange="_checkpass(this,document.nasform.enable)"></td></tr>
	<tr><td>Prompt:</td><td colspan=4><textarea id="prompt1" name="prompt1" cols="55" rows="10"></textarea></td></tr>
	<tr><td><input name="option" value="1" type="hidden"></td><td><input type="submit" name="_submit" value="Add" onclick="return _required();"> <input type="reset" onClick="return confirm('Are you sure you want to reset the data?')"></td></tr>
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
        $('#ip').change(function() {
                if (/<.*>/.test($(this).val())) {
                        alert("Not allowed characters are inputted");
                        $(this).val("");
                        $(this).focus();
		}
        });

        $('#hkey').change(function() {
                if (/<.*>/.test($(this).val())) {
                        alert("Not allowed characters are inputted");
                        $(this).val("");
                        $(this).focus();
		}
        });

        $('#prompt1').change(function() {
                if (/<.*>/.test($(this).val())) {
                        alert("Not allowed characters are inputted");
                        $(this).val("");
                        $(this).focus();
		}
        });

        var src = "result.php?_ret="+admin_priv_lvl+"&_table=hostgroup";
            src += "&offset=0&vrows="+admin_vrows+"&_index=0";
        $.get(src, function (data, status) {
                document.getElementById("_results0").innerHTML = data;
        });
});
</script>

