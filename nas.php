<?php
/*
    Copyright (C) 2003-2021 Young Consulting, Inc

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

03/07/2021 - Andrew Young
	Add IPv6 support
04/12/2019 - Andrew Young
	Add support host name
	Change ACL to Policy
08/26/2016 - Andrew Young
	Remove display of HKEY
	Add display of vendor
*/

//if (!eregi("index.php",$_SERVER['PHP_SELF'])) {
if (!preg_match("/index.php/",$_SERVER['PHP_SELF'])) {
	Header("Location: index.php");
	die();
}

if ($_ret < 5) {
	echo "<script language=\"JavaScript\"> top.location.href=\"index.php?module=main\"; </script>";
}
?>
<script language="JavaScript">
<!--
function _add(obj) {
        resultForm = document.nasform;

        if (resultForm.option.value == "2") {
                resultForm.ip.disabled = false;
                resultForm.name.value = "";
                resultForm.enable.value = "";
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
	getQueryXML(getVendorResults,"host","ip="+nas+"&host=1");
}

function _hover2()
{
	document.nasform.ip.value = "";
	document.nasform.hostgroup.value = "";
	document.nasform.hkey.value = "";
	document.nasform.prompt.value = "";
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
		if (xmldoc.getElementsByTagName('name')[0].firstChild != null)
			resultForm.name.value = xmldoc.getElementsByTagName('name')[0].firstChild.nodeValue;
		else
			resultForm.name.value = "";
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
			resultForm.prompt1.value = "";
		resultForm.loginacl.value = xmldoc.getElementsByTagName('loginacl')[0].firstChild.nodeValue;
		resultForm.enableacl.value = xmldoc.getElementsByTagName('enableacl')[0].firstChild.nodeValue;
		resultForm.vendor.value = xmldoc.getElementsByTagName('vendor')[0].firstChild.nodeValue;
	}
	
}

function _modify(nas)
{
	resultForm = document.nasform;
	getQueryXML(getNASResults,"host","ip="+nas+"&host=1");
	resultForm.ip.disabled = true;
	resultForm.option.value = "2";
	resultForm._submit.value = "Modify";
	document.getElementById("_nasadd").style.display = "";
}

function _required()
{
	var form = document.nasform;
	var ret  = true;
	var focus;
	var msg  = "";

	if (! form.ip.value) {
		msg = msg + "IP is required.\n";
		focus = form.ip;
		ret = false;
	}
		
	if (! form.name.value) {
		msg = msg + "Name is not required but recommended.\n";
	}

	if (! form.hkey.value) {
		msg = msg + "HKEY is not required but recommended.\n";
	}
	
	if (! form.prompt1.value) {
		msg = msg + "Prompt is not required but recommended.\n";
	}
	
	if ( form.enable.value ) {
		if (form.enable.value != form.re_enable.value) {
			msg = msg + "Enable does not match.\n";
			focus = form.enable;
			ret = false;
		}
	}

	if (msg) alert(msg);
	if (focus) focus.focus();
	if (ret) form.ip.disabled = false;
	return ret;
}

//-->
</script>
<?php
$where = "";
switch ($option) {
   case 1:
	$network = preg_split('/\//', $ip);
	$network6 = "";
	$submask6 = "";
	$netmask6 = "";
	if (count($network)==1) {
		$maskbits = 32;
		$submask6 = Submask6(128);
		$netmask6 = Netmask6(128);
		if (preg_match("/:/", $network[0])) {
			$network6 = $network[0];
			$network[0] = "255.255.255.255";
		} else {
			$network6 = "::FFFF:".$network[0];
		}
	} else {
		if (preg_match("/:/",$network[0])) {
			$network6 = $network[0];
			$network[0]="255.255.255.255";
			$maskbits = 32;
			$submask6 = Submask6($network[1]);
			$netmask6 = Netmask6($network[1]);
		} else {
			$network6 = "::FFFF:".$network[0];
			$maskbits = $network[1];
			$submask6 = Submask6(96+$maskbits);
			$netmask6 = Netmask6(96+$maskbits);
		}
	}
//	$crypt_enable = "";
	// if ($enable) $crypt_enable = unixcrypt($enable);
	if ($enable) {
		$result = @SQLQuery("INSERT INTO host (ip, name, hostgroup, hkey, enable, prompt, network, submask, loginacl, enableacl, vendor, host) VALUES('$ip','$name','$hostgroup','$hkey','".crypt($enable)."','$prompt1',INET_ATON('".$network[0]."'),INET_ATON('".$netmask[$maskbits]."'),$loginacl,$enableacl,$vendor,1)", $dbi);
		//$result = @SQLQuery("INSERT INTO host (ip, name, hostgroup, hkey, enable, prompt, network, submask, loginacl, enableacl, vendor, host) VALUES('$ip','$name','$hostgroup','$hkey',ENCRYPT('$enable'),'$prompt1',INET_ATON('".$network[0]."'),INET_ATON('".$netmask[$maskbits]."'),$loginacl,$enableacl,$vendor,1)", $dbi);
	} else {
		$result = @SQLQuery("INSERT INTO host (ip, name, hostgroup, hkey, enable, prompt, network, submask, loginacl, enableacl, vendor, host) VALUES('$ip','$name','$hostgroup','$hkey','','$prompt1',INET_ATON('".$network[0]."'), INET_ATON('".$netmask[$maskbits]."'), $loginacl,$enableacl,$vendor,1)", $dbi);
	}
        if (!@SQLError($dbi))
		Audit("nas","add","IP=".$ip,$dbi);
	break;
   case 2:
	$sqlcmd = "";
	if (!$enable) $sqlcmd = ", enable=''";
	if ($re_enable) $sqlcmd = ", enable='".crypt($enable)."'";
//	if ($re_enable) $sqlcmd = ", enable=ENCRYPT('$enable')";
	$result = @SQLQuery("UPDATE host SET name='$name', hostgroup='$hostgroup', hkey='$hkey', prompt='$prompt1', loginacl=$loginacl, enableacl=$enableacl, vendor=$vendor $sqlcmd WHERE ip='$ip'", $dbi);
        if (!@SQLError($dbi)) {
                echo "<P><font color=\"green\">NAS($ip) modified.</font></P>";
		Audit("nas","change","IP=".$ip,$dbi);
	}
	break;
   case 3:
	$result = @SQLQuery("DELETE FROM host WHERE ip='$ip'", $dbi);
	if (!@SQLError($dbi)) {
		Audit("nas","delete","IP=".$ip,$dbi);
	}
	break;

   case 4:
	$where = "AND hostgroup='$group'";
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
<form name="nasform" method="post" action="index.php?menu=admin&module=nas">
<fieldset class=" collapsible"><legend>NAS <?php if ($group) { echo "in group $group"; } ?><?php if ($_ret > 9) { echo "<a href=\"javascript:_add('_nasadd')\"><img src=\"images/plus-new.gif\" border=\"0\" /></a>"; } ?></legend>
<table border=0 width="100%">
<tr><td>
        <div id="_nasadd" style="display:none">
        <fieldset class="_collapsible">
	<table class="_table">
	<tr><td>IP:</td><td><input type="text" id="ip" name="ip" onChange="return _verify(this,'subnet');"></td>
	    <td>&nbsp;&nbsp;<input type="hidden" name="network"><input type="hidden" name="submask"></td>
	    <td>Vendor:</td><td><select name="vendor" style="width: 150px"><?php
		foreach ($vnd_array as $i=>$value) {
			echo "<option value=\"$i\">$value</option>";
		}
	     ?></select></td></tr>
	<tr><td>Name:</td><td><input type="text" id="name" name="name"></td>
	    <td>&nbsp;&nbsp;</td>
	    <td>Group:</td><td><select name="hostgroup" style="width: 150px"><option value=""></option><?php
		$result = @SQLQuery("SELECT ip FROM host WHERE host=2", $dbi);
		while ($row = @SQLFetchArray($result)) {
			echo "<option value=\"".$row[0]."\"";
			if (isset($group) && ($row[0]==$group)) echo " selected";
			echo ">".$row[0]."</option>";
		}
	    ?></select></td></tr>
	<tr><td>HKey:</td><td><input type="text" id="hkey" name="hkey"></td>
	    <td>&nbsp;&nbsp;</td>
	    <td>&nbsp;&nbsp;</td></tr>
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
<!-- <tr><td>
        <div id="_nas" class="_scrollwindow">
	<table border=1 class="_table2">
           <tr><th>IP</th><th>Name</th><th>Group</th><th>Vendor</th><th>Prompt</th><th>Login<br>Policy</th><th>Enable<br>Policy</th>
<?php
/*
$result = @SQLQuery("SELECT ip, name, hostgroup, vendor, prompt, loginacl, enableacl FROM host WHERE host=1 $where ORDER BY ip ASC", $dbi);
while ($row=SQLFetchArray($result)) {
	$lacl = $row["loginacl"]?$row["loginacl"]:"&nbsp;";
	$eacl = $row["enableacl"]?$row["enableacl"]:"&nbsp;";
	$prmt = $row["prompt"]?$row["prompt"]:"&nbsp;";
	if ($_ret > 9 ) {
	  echo "<tr><td width=80><a href=\"javascript:_modify('".$row["ip"]."')\" title=\"Modify NAS\">".$row["ip"]."</a></td>"
	    ."<td>".$row["name"]."</td>"
	    ."<td width=80>".$row["hostgroup"]."</td>"
	    ."<td width=80>".$vnd_array[$row["vendor"]]."</td>"
	    ."<td width=230>".$prmt."</td>"
	    ."<td width=45><center>".$lacl."</center></td>"
	    ."<td width=45><center>".$eacl."</center></td>"
	    ."<td><a href=\"javascript:_delete('".$row["ip"]."')\" title=\"Delete NAS\"><img src=\"images/trash.gif\" width=25 border=0></img></a></td></tr>\n";
	} else {
          echo "<tr><td width=80>".$row["ip"]."</td>"
            ."<td width=80>".$row["hostgroup"]."</td>"
            ."<td width=80>".$vnd_array[$row["vendor"]]."</td>"
            ."<td width=190>".$prmt."</td>"
            ."<td width=45><center>".$lacl."</center></td>"
            ."<td width=45><center>".$eacl."</center></td>";
	}
}
@SQLFreeResult($result);
*/
?>
	</table>
        </div>
</td></tr> -->
</table>
</fieldset>
</form>

<script>
$(document).ready(function() {
	var re = /^([a-zA-Z0-9 _&\-\.]+)$/;
	$('#name').change(function() {
		if (!re.test($(this).val())) {
			alert("Not allowed characters are inputted");
			$(this).val("");
			$(this).focus();
		}
	});

	$('#prompt1').change(function() {
                if (!re.test($(this).val())) {
                        alert("Not allowed characters are inputted");
                        $(this).val("");
                        $(this).focus();
		}
	});

	re = /^([a-zA-Z0-9_&\-\.%$#@!*?\(\)]+)$/;
	$('#hkey').change(function() {
		if (!re.test($(this).val())) {
			alert("Not allowed characters are inputted");
			$(this).val("");
			$(this).focus();
		}
	});

        var src = "result.php?_ret="+admin_priv_lvl+"&_table=host";
            src += "&offset=0&vrows="+admin_vrows+"&_index=0";
<?php if (isset($group) && $group) echo "          src += \"&group=$group\";\n"; ?>
        $.get(src, function (data, status) {
                document.getElementById("_results0").innerHTML = data;
        });

        $('#search').change(function() {
                var new_src = src;
                if ($(this).val()) {
                        var _s = $(this).val().indexOf("=");
                        if (_s > 0) {
                                new_src += "&"+$(this).val();
                        } else {
                                new_src += "&ip="+$(this).val();
                        }
                }
                $.get(new_src, function (data, status) {
                        document.getElementById("_results0").innerHTML = data;
                });
        });
});
</script>
