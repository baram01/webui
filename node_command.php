<?php
/*
    Copyright (C) 2003-2021 3 Youngs, Inc

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

if (checkLoginXML($_COOKIE["login"],$dbi) < 5) {
	CloseDatabase($dbi);
        echo "<script language=\"JavaScript\"> top.location.href=\"index.php?module=main\"; </script>";
}

/*
echo "<script language=\"Javascript\">\n";
$_cmd = 0;
//$result = @SQLQuery("SELECT name FROM command WHERE vid=0", $dbi);
$result = @SQLQuery("SELECT name FROM command", $dbi);
while ($row = @SQLFetchArray($result)) {
        echo "commands[$_cmd] = \"".$row[0]."\";";
        $_cmd++;
}
echo "</script>\n"; */
?>
<script language="Javascript">
<!--
$( function() {
    $( "#command" ).autocomplete({
      source: function( request, response ) {
        $.ajax( {
          url: "queryjson.php?table=command",
          dataType: "jsonp",
          data: {
            term: request.term,
            vid: $( "#vid" ).val()
          },
          success: function( data ) {
            response( data );
          }
        } );
      },
      minLength: 2,
      select: function( event, ui ) {
        $( "#command" ).val( ui.item.name );
        $( "#attrid" ).val( ui.item.id );
        return false;
      }
    })
    .autocomplete( "instance" )._renderItem = function (ul, item ) {
        return $( "<li>")
                .append( "<div>" + item.name + "</div>" )
                .appendTo( ul );
    };
} );
-->
</script>
<form name="serviceform" method="post" action="node.php?<?php echo "_ret=$_ret&pid=$pid&uid=$uid&_service=56";?>">
<fieldset class="_collapsible"><legend id="_commandset">For <?php echo $uid; echo "<a href=\"javascript:_add('_node_svc_add')\"><img src=\"images/plus-new.gif\" border=\"0\" /></a>"; ?></legend>
<table border=0 width="100%">
<tr><td>
	<div id="_node_svc_add" style="display:none">
	<fieldset class="_collapsible">
	<table class="_table">
	<tr><td width="50">Sequence:</td><td><input type="text" id="seq" name="seq" size="5"></td></tr>
	<tr><td width="50">Permit:</td><td><select name="type"><option value="57">permit<option value="58">deny</select></td></tr>
	<tr><td width="50">Vendor:</td><td><select name="vid" onchange="javascript:_getCommands(this)"><?php
		foreach ($vnd_array as $i=>$value) {
			echo "<option value=\"$i\">$value";
		}
		?></select></td></tr>
	<tr><td width="50">Command:</td><td><div class="ui-widget"><input type="text" id="command" name="value1" size="20"></div></td>
	<tr><td width="50">Argument:</td><td><input type="text" id="value" name="value" size="35"></td></tr>
	<tr><td width="50"></td><td><input name="option" value="1" type="hidden">
		<input name="service" value="56" type="hidden">
		<input name="attrid" value="0" type="hidden">
		<input name="attrfmt" value="0" type="hidden">
		<input name="auth" value="0" type="hidden">
		<input name="svc_name" type="hidden" value="">
	        <input type="submit" name="_submit" value="Add" onClick="return _check_serviceform(this.form)"> <input type="reset" onClick="return confirm('Are you sure you want to reset the data?')"></td></tr>
	</table>
	</fieldset>
	</div>
</td></tr>
<tr><td>
	<table border=1 width="100%" class="_table2">
	<tr><th width=10>Sequence</th><th width=15>Access</th><th width=25>Vendor</th><th width=150>Command</th><th width=200>Argument</th><th colspan=2>&nbsp;</th></tr>
<?php
        for ($i = 0; $i < count($svc_array); $i++) {
                echo "<tr>";
		echo "<td width=10>".$svc_array[$i]["seq"]."</td>";
                echo "<td width=15>".$node_type[$svc_array[$i]["type"]]."</td>";
                echo "<td width=25>".$vnd_array[$svc_array[$i]["vid"]]."</td>";
                echo "<td width=150>".$svc_array[$i]["value1"]."</td>";
                echo "<td width=200>".$svc_array[$i]["value"]."</td>";
//                echo "<td width=25><a href=\"Javascript:_modify(svc_info[$i])\" title=\"Modify Command\"><img src=\"images/modify.gif\" width=25 border=0></a></td>";
                echo "<td width=25><input type=\"image\" width=25 border=0 src=\"images/trash.gif\" onclick=\"return _delete(this.form,svc_info[$i]);\" title=\"Delete Command\">\n";
        }
?>
	</table>
</td></tr>
</table>
</fieldset>
</form>

<script>
$(document).ready(function() {
	$('#seq').change(function() {
		var re = /^[0-9]+$/;
		if (!re.test($(this).val())) {
			alert("Sequence can only be numeric");
			$(this).val("");
			$(this).focus();
		}
	});
	$('#command').change(function() {
                var re = /[<>@#^%]/;
                if (re.test($(this).val())) {
                        alert("Not a valid command");
                        $(this).val("");
                        $(this).focus();
                }
        });
	$('#value').change(function() {
		if (/[<>@#^%]/.test($(this).val())) {
			alert("Characters are not allowed <>");
			$(this).val("");
			$(this).focus();
		}
	});
});
</script>
