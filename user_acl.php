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
        resultForm = document.aclform;

        if (resultForm.option.value == "2") {
                resultForm.id.disabled = false;
                resultForm.id.value = "";
                resultForm.name.disabled = false;
                resultForm.option.value = "1";
                resultForm._submit.value = "Add";
                _hover2();
        } else {
                resultForm.id.value = "";
                addMe(obj);
        }
}

function _add_acl(obj, value) {
        resultForm = document.aclform;

        if (resultForm.option.value == "2") {
                resultForm.id.disabled = false;
                resultForm.id.value = value;
                resultForm.name.disabled = false;
                resultForm.option.value = "1";
                resultForm._submit.value = "Add";
                _hover2();
        } else {
                resultForm.id.value = value;
                addMe(obj);
        }
}

function _delete(id, seq)
{
	var msg = "Do you want really to delete ACL ID="+id+" and Sequence="+seq+"?";

	if (confirm(msg)) {
		document.aclform.id.value = id;
		document.aclform.seq.value = seq;
		document.aclform.option.value = "3";
		document.aclform.submit();
	}
}

function _hover(acl)
{
	resultForm = document.aclform;
	getQueryXML(getVendorResults,"acl","type=1&id="+id+"&seq="+seq);
}

function _hover2()
{
        resultForm = document.aclform;
	resultForm.id.value = "";
	resultForm.seq.value = "";
	resultForm.permission.value = "";
	resultForm.netvalue.value = "";
}

function getVendorResults()
{
	if (getRequest.readyState == 4) {
		var xmldoc = getRequest.responseXML.documentElement;

		resultForm.id.value = xmldoc.getElementsByTagName('id')[0].firstChild.nodeValue;
		resultForm.seq.value = xmldoc.getElementsByTagName('seq')[0].firstChild.nodeValue;
		resultForm.oldseq.value = xmldoc.getElementsByTagName('seq')[0].firstChild.nodeValue;
		if (xmldoc.getElementsByTagName('permission')[0].firstChild != null)
			aclform.permission.value = xmldoc.getElementsByTagName('permission')[0].firstChild.nodeValue;
		else
			aclform.permission.value = "";
		if (xmldoc.getElementsByTagName('value')[0].firstChild != null)
			aclform.netvalue.value = xmldoc.getElementsByTagName('value')[0].firstChild.nodeValue;
		else
			aclform.netvalue.value = "";
	}
}

function _modify(id,seq)
{
	resultForm = document.aclform;
	getQueryXML(getVendorResults,"acl","type=1&id="+id+"&seq="+seq);
	resultForm.id.disabled = true;
	resultForm.option.value = "2";
	resultForm._submit.value = "Modify";
	document.getElementById("_acladd").style.display = "";
}

function _required()
{
	var form = document.aclform;
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
		
	if (! form.seq.value) {
		msg = msg + "Sequence is required.\n";
		if (!focus) {
			focus = form.seq;
		}
		ret = false;
	} else {
		var anum=/(^\d+$)/;
		if (!anum.test(form.seq.value)) {
			msg = msg + "Sequence is not valid.\n";
			focus = form.seq;
			ret = false;
		}
		if ((form.seq.value<0)||(form.seq.value>9998)) {
			msg = msg + "Sequence number must be between 1 and 9998.\n";
			focus = form.seq;
			ret = false;
		}
	}
	
	if (msg) alert(msg);
	if (focus) focus.focus();
	if (ret) form.id.disabled = false;
	return ret;
}

//-->
</script>
<?php

switch ($option) {
   case 1:
	$network = preg_split('/\//', $netvalue);
	if (count($network)==1) $network[1]=32;
	$result = @SQLQuery("INSERT INTO acl (id, seq, permission, value, value1, submask, type) VALUES ($id, $seq, $permission, '$netvalue', INET_ATON('".$network[0]."'), INET_ATON('".$netmask[$network[1]]."'), 1)", $dbi);
	$result = @SQLQuery("SELECT seq FROM acl WHERE id=$id AND seq=9999", $dbi);
	if (@SQLNumRows($result) < 1) {
		$result = @SQLQuery("INSERT INTO acl (id, seq, permission, value, value1, submask, type) VALUES ($id, 9999, 58, '0.0.0.0/0', INET_ATON('0.0.0.0'), INET_ATON('0.0.0.0'), 1)", $dbi);
		Audit("user_acl","add", "ACL=".$id." SEQ=".$seq, $dbi);
	} 
	break;
   case 2:
	$network = preg_split('/\//', $netvalue);
	if (count($network)==1) $network[1]=32;
	$result = @SQLQuery("UPDATE acl SET seq=$seq, permission=$permission, value='$netvalue', value1=INET_ATON('".$network[0]."'), submask=INET_ATON('".$netmask[$network[1]]."') WHERE id=$id AND seq=$oldseq AND type=1", $dbi);
	Audit("user_acl","change", "ACL=".$id." SEQ=".$seq, $dbi);
	break;
   case 3:
	$result = @SQLQuery("SELECT uid FROM user WHERE acl_id=$id", $dbi);
	$numuser = @SQLNumRows($result);
	$result = @SQLQuery("SELECT seq FROM acl WHERE id=$id AND type=1", $dbi);
	if (@SQLNumRows($result)>2) {
		$result = @SQLQuery("DELETE FROM acl WHERE id=$id AND seq=$seq AND type=1", $dbi);
		$result = @SQLQuery("SELECT seq FROM acl WHERE id=$id AND type=1", $dbi);
		if (@SQLNumRows($result) == 1) {
			$result = @SQLQuery("DELETE FROM acl WHERE id=$id AND type=1", $dbi);
		}
		Audit("user_acl","delete", "ACL=".$id." SEQ=".$seq, $dbi);
	} else {
		if ($numuser<1) {
			$result = @SQLQuery("DELETE FROM acl WHERE id=$id AND seq=$seq AND type=1", $dbi);
			$result = @SQLQuery("SELECT seq FROM acl WHERE id=$id AND type=1", $dbi);
			if (@SQLNumRows($result) == 1) {
				$result = @SQLQuery("DELETE FROM acl WHERE id=$id AND type=1", $dbi);
			}
			Audit("user_acl","delete", "ACL=".$id." SEQ=".$seq, $dbi);
		} else
			echo "<P><font color=\"red\">Cannot delete ACL($id). There are too many dependancies.</font></P>";
	}
	break;
}
if ($debug) {
	$_ERROR=$_ERROR." ".@SQLError($dbi);
}

$perm_type = array(57=>"permit", "deny");
?>
<form name="aclform" method="post" action="?menu=admin&module=user_acl">
<fieldset class=" collapsible"><legend>User ACL <?php if ($_ret > 9) { echo "<a href=\"javascript:_add('_acladd')\"><img src=\"images/plus-new.gif\" border=\"0\" /></a>"; } ?></legend>
<table border=0 width="100%">
<tr><td>
        <div id="_acladd" style="display:none">
        <fieldset class="_collapsible">
	<table class="_table">
	<tr><td>ID:</td><td><input type="text" id="id" name="id" size=6></td></tr>
	<tr><td>Sequence:</td><td><input type="text" id="seq" name="seq" size=6><input type="hidden" name="oldseq"></td></tr>
	<tr><td>Permission:</td><td><select name="permission"><?php
		foreach ($perm_type as $i=>$j) {
			echo "<option value=\"$i\">$j</option>";
		}
	    ?></select></td></tr>
	<tr><td>IP Address/mask:</td><td><input type="text" id="netvalue" name="netvalue" style="width: 150px"></td></tr>
	<tr><td><input name="option" value="1" type="hidden"><input name="type" value="2" type="hidden"></td><td><input type="submit" name="_submit" value="Add" onclick="return _required();"> <input type="reset" onClick="return confirm('Are you sure you want to reset the data?')"></td></tr>
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

        $('#seq').change(function() {
                if (isNaN($(this).val())) {
                        alert("Only integers are allowed");
                        $(this).val("");
                        $(this).focus();
                }
        });

	$('#netvalue').change(function() {
		var re = /^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/;
		var s = $(this).val().split('/');

		if ((!re.test(s[0]))||(s[0]=="255.255.255.255")) {
			alert("Not valid IPv4 address");
			$(this).val("");
			$(this).focus();
		}

		if (s.length > 1) {
			if (isNaN(s[1])) { 
				alert("Not a valid IP maskbits");
				$(this).css("background-color", "pink");
				$(this).get(0).focus();
			}

			if ((Number(s[1])<0)||(Number(s[1])>32)) {
				alert("Not a valid IP maskbits");
				$(this).css("background-color", "pink");
				$(this).focus();
			}
		}
	});

        var src = "result.php?_ret="+admin_priv_lvl+"&_table=user_acl";
            src += "&offset=0&vrows="+admin_vrows+"&_index=0";
        $.get(src, function (data, status) {
                document.getElementById("_results0").innerHTML = data;
        });
});
</script>
