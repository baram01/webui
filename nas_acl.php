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
*/

//if (!eregi("index.php",$_SERVER['PHP_SELF'])) {
if (!preg_match("/index.php/",$_SERVER['PHP_SELF'])) {
	Header("Location: index.php");
	die();
}

if ($_ret < 5) {
	echo "<script language=\"JavaScript\"> top.location.href=\"?menu=main&module=main\"; </script>";
}
?>
<script language="JavaScript">
<!--
document.getElementById("_search").style="visibility:hidden";

function _add(obj) {
        resultForm = document.aclform;

        if (resultForm.option.value == "2") {
                resultForm.id.disabled = false;
                resultForm.id.value = "";
                // resultForm.name.disabled = false;
                // resultForm.name.value = "";
                resultForm.option.value = "1";
                resultForm._submit.value = "Add";
                _hover2();
        } else {
                resultForm.id.disabled = false;
                resultForm.id.value = "";
                // resultForm.name.disabled = false;
                // resultForm.name.value = "";
                addMe(obj);
        }
}

function _add_acl(obj, _id, _name) {
        resultForm = document.aclform;

        if (resultForm.option.value == "2") {
                resultForm.id.disabled = false;
                resultForm.id.value = _id;
                // resultForm.name.disabled = false;
                // resultForm.name.value = _name;
                resultForm.option.value = "1";
                resultForm._submit.value = "Add";
                _hover2();
        } else {
                resultForm.id.disabled = true;
                resultForm.id.value = _id;
                // resultForm.name.disabled = true;
                // resultForm.name.value = _name;
                addMe(obj);
        }
}

function _delete(id, seq)
{
	var msg = "Do you want really to delete Policy ID="+id+" and Sequence="+seq+"?";

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
	getQueryXML(getVendorResults,"acl","type=2&id="+id+"&seq="+seq);
}

function _hover2()
{
	document.aclform.id.value = "";
	document.aclform.seq.value = "";
	document.aclform.permission.value = "";
	document.aclform.ugvalue.value = "";
	document.aclform.value1.value = "";
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
			aclform.ugvalue.value = xmldoc.getElementsByTagName('value')[0].firstChild.nodeValue;
		else
			aclform.ugvalue.value = "";
		if (xmldoc.getElementsByTagName('value1')[0].firstChild != null)
			aclform.value1.value = xmldoc.getElementsByTagName('value1')[0].firstChild.nodeValue;
		else
			aclform.value1.value = "";
	}
}

function _modify(id,name,seq)
{
	resultForm = document.aclform;
	getQueryXML(getVendorResults,"acl","type=2&id="+id+"&seq="+seq);
	resultForm.id.disabled = true;
	// resultForm.name.disabled = true;
	// resultForm.name.value = name;
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

function modifyTableRowspan(column) {
        var prevText = "";
        var counter = 0;

        column.each(function (index) {
            var textValue = $(this).text();

            if (index === 0) {
                prevText = textValue; 
            }
            
            if (textValue !== prevText || index === column.length - 1) {
                var first = index - counter;

                if (index === column.length - 1) {
                    counter = counter + 1;
                }

                column.eq(first).attr('rowspan', counter);

                if (index === column.length - 1)
                {
                    for (var j = index; j > first; j--) {
                        column.eq(j).remove();
                    }
                } else {
                    for (var i = index - 1; i > first; i--) {
                        column.eq(i).remove();
                    }
                }
                prevText = textValue;
                counter = 0;
            }

            counter++;
        });
}

//-->
</script>
<?php
switch ($option) {
   case 1:
	// $result1 = @SQLQuery("INSERT INTO acl_name (id, name, type) VALUES ($id, '$name', 2)", $dbi);
	$result = @SQLQuery("INSERT INTO acl (id, seq, permission, value, value1, type) VALUES ($id, $seq, $permission, '$ugvalue', $value1, 2)", $dbi);
	if (!@SQLError($dbi)) {
		Audit("nas_acl", "add", "ACL=".$id." SEQ=".$seq, $dbi);
	} else {
		echo "<font color='red'>Entry already in ACL</font>";
	}
	break;
   case 2:
	$result = @SQLQuery("UPDATE acl SET seq=$seq, permission=$permission, value='$ugvalue', value1=$value1 WHERE id=$id AND seq=$oldseq AND type=2", $dbi);
	if (!@SQLError($dbi)) {
		Audit("nas_acl", "change", "ACL=".$id." SEQ=".$seq, $dbi);
	} else {
		echo "<font color='red'>Cannot be modified</font>";
	}
	break;
   case 3:
	$result = @SQLQuery("SELECT ip FROM host WHERE loginacl=$id or enableacl=$id", $dbi);
	$numnas = @SQLNumRows($result);
	$result = @SQLQuery("SELECT seq FROM acl WHERE id=$id AND type=2", $dbi);
	if (@SQLNumRows($result)>1) {
		$result = @SQLQuery("DELETE FROM acl WHERE id=$id AND seq=$seq AND type=2", $dbi);
	} else {
		if ($numnas<1) {
			$result = @SQLQuery("DELETE FROM acl WHERE id=$id AND seq=$seq AND type=2", $dbi);
			// $result = @SQLQuery("DELETE FROM acl_name WHERE id=$id", $dbi);
			Audit("nas_acl", "delete", "ACL=".$id." SEQ=".$seq, $dbi);
		} else
			echo "<P><font color=\"red\">Cannot delete ACL($id). There are too many dependancies.</font></P>";
	}
	break;
}
if ($debug) {
	$_ERROR=$_ERROR." ".@SQLError($dbi);
}

$perm_type = array(57=>"permit", "deny");
$profile = array(0=>'&nbsp;');

// $result = @SQLQuery("SELECT id, uid from user where user=3", $dbi);
$result = @SQLQuery("SELECT id, uid from profile", $dbi);
while ($row = @SQLFetchArray($result)) {
	$profile[$row[0]] = $row[1];
}
@SQLFreeResult($result);
?>
<form name="aclform" method="post" action="?menu=admin&module=nas_acl">
<fieldset class=" collapsible"><legend>NAS Policies <?php if ($_ret > 9) { echo "<a href=\"javascript:_add('_acladd')\" title=\"Add new policies or policy sequences\"><img src=\"images/plus-new.gif\" border=\"0\" /></a>"; } ?></legend>
<table border=0 width="100%">
<tr><td>
        <div id="_acladd" style="display:none">
        <fieldset class="_collapsible">
	<table class="_table">
	<tr><td>ID:</td><td><input type="text" id="id" name="id" size=6 onChange="return _verify(this,'num');"></td><!-- <td>Name:</td><td><input type="text" id="name" name="name"></td> --></tr>
	<tr><td>Sequence:</td><td><input type="text" id="seq" name="seq" size=6 onChange="return _verify(this,'num');"><input type="hidden" name="oldseq"></td></tr>
	<tr><td>Permission:</td><td><select name="permission"><?php
		foreach ($perm_type as $i=>$j) {
			echo "<option value=\"$i\">$j</option>";
		}
	    ?></select></td></tr>
	<tr><td>User/Group:</td><td><select name="ugvalue" style="width: 160px"><option value="allusers">allusers</option><?php
		$result=@SQLQuery("SELECT uid, user FROM user WHERE user!=3 ORDER BY user DESC", $dbi);
		while ($row = @SQLFetchArray($result)) {
			echo "<option value=\"".$row["uid"]."\">".$row["uid"];
			if ($row["user"]=="2") echo " (Group)";
			echo "</option>";
		}
		@SQLFreeResult($result);
	    ?></select></td></tr>
	<tr><td>Profile:</td><td><select name="value1" style="width: 160px"><?php
		foreach ($profile as $i=>$j) {
			echo "<option value=\"$i\">$j</option>";
		}
	    ?></select></td></tr>
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

        $('#name').change(function() {
		var re = /^[a-zA-Z0-9 _\-]*$/;
                if (!re.test($(this).val())) {
                        alert("There characters being inputted that are not allowed");
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

        var src = "result.php?_ret="+admin_priv_lvl+"&_table=nas_acl";
            src += "&offset=0&vrows="+admin_vrows+"&_index=0";
        $.get(src, function (data, status) {
                document.getElementById("_results0").innerHTML = data;
        });
});
</script>

