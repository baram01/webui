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
	echo "<script language=\"JavaScript\"> top.location.href=\"?module=main\"; </script>";
}
?>
<!-- <script language="JavaScript" src="js/calendar3.js"></script> -->
<script language="Javascript" src="js/result.js"></script>
<script language="JavaScript">
<!--
function _add(obj) {
	resultForm = document.vendorform;

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

function _delete(obj)
{
	var msg = "Do you want really to delete "+obj+"?";

	if (confirm(msg)) {
		document.vendorform.id.value = obj;
		document.vendorform.option.value = "3";
		document.vendorform.submit();
	}
}

function _hover(vendor)
{
	resultForm = document.vendorform;
	getQueryXML(getVendorResults,"vendor","vid="+vendor);
}

function _hover2()
{
	document.vendorform.id.value = "";
	document.vendorform.name.value = "";
	document.vendorform.url.value = "";
	document.vendorform.scname.value = "";
	document.vendorform.scemail.value = "";
	document.vendorform.tsphone.value = "";
	document.vendorform.tsemail.value = "";
	document.vendorform.contract.value = "";
	document.vendorform.start.value = "";
	document.vendorform.end.value = "";
}

function getVendorResults()
{
	if (getRequest.readyState == 4) {
		var xmldoc = getRequest.responseXML.documentElement;

		resultForm.id.value = xmldoc.getElementsByTagName('id')[0].firstChild.nodeValue;
		resultForm.name.value = xmldoc.getElementsByTagName('name')[0].firstChild.nodeValue;
		if (xmldoc.getElementsByTagName('url')[0].firstChild != null)
			vendorform.url.value = xmldoc.getElementsByTagName('url')[0].firstChild.nodeValue;
		else
			vendorform.url.value = "";
		if (xmldoc.getElementsByTagName('scname')[0].firstChild != null)
			vendorform.scname.value = xmldoc.getElementsByTagName('scname')[0].firstChild.nodeValue;
		else
			vendorform.scname.value = "";
		if (xmldoc.getElementsByTagName('scemail')[0].firstChild != null)
			vendorform.scemail.value = xmldoc.getElementsByTagName('scemail')[0].firstChild.nodeValue;
		else
			vendorform.scemail.value = "";
		if (xmldoc.getElementsByTagName('tsphone')[0].firstChild != null)
			vendorform.tsphone.value = xmldoc.getElementsByTagName('tsphone')[0].firstChild.nodeValue;
		else
			vendorform.tsphone.value = "";
		if (xmldoc.getElementsByTagName('tsemail')[0].firstChild != null)
			vendorform.tsemail.value = xmldoc.getElementsByTagName('tsemail')[0].firstChild.nodeValue;
		else
			vendorform.tsemail.value = "";
		if (xmldoc.getElementsByTagName('contract')[0].firstChild != null)
			vendorform.contract.value = xmldoc.getElementsByTagName('contract')[0].firstChild.nodeValue;
		else
			vendorform.contract.value = "";
		if (xmldoc.getElementsByTagName('start')[0].firstChild != null)
			vendorform.start.value = xmldoc.getElementsByTagName('start')[0].firstChild.nodeValue;
		else
			vendorform.start.value = "";
		if (xmldoc.getElementsByTagName('end')[0].firstChild != null)
			vendorform.end.value = xmldoc.getElementsByTagName('end')[0].firstChild.nodeValue;
		else
			vendorform.end.value = "";
	}
	
}

function _modify(vendor)
{
	resultForm = document.vendorform;
	getQueryXML(getVendorResults,"vendor","vid="+vendor);
	resultForm.id.disabled = true;
	//resultForm.name.disabled = true;
	resultForm.option.value = "2";
	resultForm._submit.value = "Modify";
	document.getElementById("_vendoradd").style.display = "";
	resultForm.name.focus();
}

function _required()
{
	var form = document.vendorform;
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
	if (ret) form.id.disabled = false;
	return ret;
}

//-->
</script>
<?php
switch ($option) {
   case 1:
	if (!$start) { $start = "0000-00-00"; }
	if (!$end) { $end = "0000-00-00"; }
	$sqlcmd = sprintf("INSERT INTO vendor VALUES(%d,'%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')",
		$id,$name, $url, $scname, $scphone, $scemail, $tsphone, $tsemail,
		$contract, $start, $end );
	if (SQLQuery($sqlcmd,$dbi)) {
		Audit("vendor","add","ID=".$id." VENDOR=".$name,$dbi);
	}
	break;
   case 2:
	$sqlcmd = sprintf("UPDATE vendor SET name='%s', url='%s', scname='%s', scphone='%s', scemail='%s', tsphone='%s', tsemail='%s', contract='%s', start='%s', end='%s' WHERE id=%d",
		$name, $url, $scname, $scphone, $scemail, $tsphone, $tsemail,
		$contract, $start, $end, $id);
	if (SQLQuery($sqlcmd,$dbi)) {
		Audit("vendor","change","ID=".$id." VENDOR=".$name,$dbi);
	}
	break;
   case 3:
	$result = @SQLQuery("SELECT id FROM attribute WHERE vid='$id'", $dbi);
	if (@SQLNumRows($result)<1) {
		$result = @SQLQuery("SELECT ip FROM host WHERE vid='$id'", $dbi);
		if (@SQLNumRows($result)<1) {
			$sqlcmd = sprintf("DELETE FROM vendor WHERE id=%d", $id);
			if (SQLQuery($sqlcmd,$dbi)) {
				Audit("vendor","delete","ID=".$id." VENDOR=".$name,$dbi);
			}
		} else
                	echo "<P><font color=\"red\">Cannot delete vendor($id). There are too many dependancies.</font></P>";
	} else
                echo "<P><font color=\"red\">Cannot delete vendor($id). There are too many dependancies.</font></P>";
	break;
   default:
	$sqlcmd = "";
}

if ($debug && SQLError($dbi)) {
		$_ERROR="Cannot do transaction. SQL Error:- ".@SQLError($dbi)." ".$sqlcmd;
}
	
/*
if ($sqlcmd != "") {
	if (!SQLQuery($sqlcmd,$dbi)) {
		$_ERROR="Cannot do transaction. SQL Error:- ".@SQLError($dbi)." ".$sqlcmd;
	}
}
*/
?>
<form name="vendorform" method="post" action="?menu=admin&module=vendor">
<fieldset class="collapsible"><legend>Vendors <?php if ($_ret > 9) { echo "<a href=\"javascript:_add('_vendoradd')\"><img src=\"images/plus-new.gif\" border=\"0\" /></a>"; } ?></legend>
<table border=0 width="100%">
<tr><td>
	<div id="_vendoradd" style="display:none">
	<fieldset class="_collapsible">
	<table class="_table">
	<tr><td>ID:</td><td><input type="text" id="id" name="id" size=6 onChange="return _verify(this,'num');" <?php if ($option==4) echo "readOnly> <font color=\"red\">**This field is read-only</font>"; else echo ">"; ?></td></tr>
	<tr><td>Name:</td><td><input type="text" id="name" name="name" size=20></td></tr>
	<tr><td>Web Site:</td><td><input type="text" id="url" name="url" size=70></td></tr>
	<tr><td>Sales Contact Name:</td><td><input type="text" name="scname" size=50></td></tr>
	<tr><td>Sales Contact Email:</td><td><input type="text" name="scemail" size=70 onChange="return _verify(this,'email');"></td></tr>
	<tr><td>Sales Contact Phone:</td><td><input type="text" name="scphone"></tr>
	<tr><td>Tech Support Phone:</td><td><input type="text" name="tsphone"></td></tr>
	<tr><td>Tech Support Phone:</td><td><input type="text" name="tsphone"></td></tr>
	<tr><td>Tech Support Email:</td><td><input type="text" name="tsemail" size=70 onChange="return _verify(this,'email');"></td></tr>
	<tr><td>Contract Number:</td><td><input type="text" name="contract"></td></tr>
	<tr><td>Contract Start Date:</td><td><input type="text" id="start" name="start"> <!-- <a href="Javascript:open_calendar(document.forms['vendorform'].elements['start']);"><img src="images/cal.gif" border=0></img></a> --> <font size=-2>eg. 2003-04-01 (Year-Month-Day)</font></td></tr>
	<tr><td>Contract End Date:</td><td><input type="text" id="end" name="end"> <!-- <a href="Javascript:open_calendar(document.forms['vendorform'].elements['end']);"><img src="images/cal.gif" border=0></img></a> --> <font size=-2>eg. 2003-04-01 (Year-Month-Day)</font></td></tr>
	<tr><td><input name="option" value="1" type="hidden"></td><td><input type="submit" name="_submit" value="Add" onclick="return _required();"> <input type="reset" onClick="return confirm('Are you sure you want to reset the data?')"></td></tr>
	</table>
	</fieldset>
	</div>
</td></tr>
<tr><td><div id="_results0"></div></td></tr>
<!-- <tr><td>
        <div id="_vendors" class="_scrollwindow">
	<table border=1 width="100%" class="_table2">
           <tr><th>ID</th><th>Name</th><th>Contract#</th><th>Tech Support</th><th>Start Date</th><th>End Date</th>
<?php
/*
$sqlcmd = "SELECT id, name, contract, tsphone, start, end  FROM vendor ORDER BY id";
$result = SQLQuery($sqlcmd, $dbi);
while ($row=SQLFetchArray($result)) {
	if ($row["id"]) {
	    if ($_ret > 9) {
	      echo "<tr><td><a href=\"javascript:_modify('".$row["id"]."')\" title=\"Modify Vendor\">".$row["id"]."</a></td>"
		."<td>".$row["name"]."</td>"
		."<td>".$row["contract"]."</td>"
		."<td>".$row["tsphone"]."</td>"
		."<td><center>".$row["start"]."</center></td>"
		."<td><center>".$row["end"]."</center></td>"
	    	."<td><a href=\"javascript:_delete('".$row["id"]."')\"><img src=\"images/trash.gif\" width=25 border=0></img></a></td></tr>\n";
	    } else {
	      echo "<tr><td>".$row["id"]."</td>"
		."<td>".$row["name"]."</td>"
		."<td>".$row["contract"]."</td>"
		."<td>".$row["tsphone"]."</td>"
		."<td><center>".$row["start"]."</center></td>"
		."<td><center>".$row["end"]."</center></td>";
	    }
	}
}
SQLFreeResult($result); */
?>
	</table>
        </div>
</td></tr> -->
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
                if (/[<>]/.test($(this).val())) {
                        alert("Not allowed characters are inputted");
                        $(this).val("");
                        $(this).focus();
                }
        });

        $('#url').change(function() {
                if (/[<>]/.test($(this).val())) {
                        alert("Not allowed characters are inputted");
                        $(this).val("");
                        $(this).focus();
                }
        });

	$('#start').datetimepicker({dateFormat: 'yy-mm-dd', timeInput: true, showHour: false, showMinute: false, showSecond: false, timeFormat: 'HH:mm:ss'});

	$('#end').datetimepicker({dateFormat: 'yy-mm-dd', timeInput: true, showHour: false, showMinute: false, showSecond: false, timeFormat: 'HH:mm:ss'});

        var src = "result.php?_ret="+admin_priv_lvl+"&_table=vendor";
            src += "&offset=0&vrows="+admin_vrows+"&_index=0";
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
                                new_src += "&vendor="+$(this).val();
                        }
                }
                $.get(new_src, function (data, status) {
                        document.getElementById("_results0").innerHTML = data;
                });
        });
});
</script>
