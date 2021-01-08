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

$json_attr_fmt_file = $target_dir."attr_format.json";
?>
<script language="Javascript">
<!--

var attr_format = <?php echo file_get_contents($json_attr_fmt_file); ?>;

function _check_svc(obj) {
	if (obj.value == 59) {
		document.getElementById("_svcname").style.display="";
	} else {
		document.getElementById("_svcname").style.display="none";
	}
}

$( function() {
    $( '#attrib' ).autocomplete({
      source: function( request, response ) {
        $.ajax( {
          url: "queryjson.php?table=attribute",
          dataType: "jsonp",
          data: {
            term: request.term,
	    vid: $( "#vid" ).val(),
	    auth: $( "#auth" ).val()
          },
          success: function( data ) {
            response( data );
          }
        } );
      },
      minLength: 2,
      select: function( event, ui ) {
        $( "#attrib" ).val( ui.item.name );
        $( "#attrid" ).val( ui.item.id );
        $( "#attrfmt" ).val( ui.item.type );
        $( "#attrfmt" ).prop('disabled', true);
	$( "#auth").val ( ui.item.auth );
        return false;
      }
    })
    .autocomplete( "instance" )._renderItem = function (ul, item ) {
        return $( "<li>")
                .append( "<div>" + item.name + "</div>" )
                .appendTo( ul );
    };
    $( '#value' ).autocomplete({
      source: function( request, response ) {
        $.ajax( {
          url: "queryjson.php?table=attr_value",
          dataType: "jsonp",
          data: {
            term: request.term,
	    vid: $( "#vid" ).val(),
	    attrid: $( "#attrid" ).val()
          },
          success: function( data ) {
            response( data );
          }
        } );
      },
      minLength: 2,
      select: function( event, ui ) {
        $( "#value" ).val( ui.item.value );
        return false;
      }
    })
    .autocomplete( "instance" )._renderItem = function (ul, item ) {
        return $( "<li>")
                .append( "<div>" + item.value +" "+ item.option + "</div>" )
                .appendTo( ul );
    };

} );

-->
</script>

<form name="serviceform" method="post" action="node.php?<?php echo "_ret=$_ret&pid=$pid&uid=$uid&_service=1";?>">
<fieldset class="_collapsible"><legend id="_serviceset">Services for <?php echo $uid; echo "<a href=\"javascript:_add('_node_svc_add')\"><img src=\"images/plus-new.gif\" border=\"0\" /></a>"; ?></legend>
<table border=0 width="100%">
<tr><td>
	<div id="_node_svc_add" style="display:none">
	<fieldset class="_collapsible">
	<table class="_table">
	<tr><td><font color="red">*</font>Authenticate:</td>
	    <td colspan="2"><select id="auth" name="auth"><?php
		foreach ($attr_auth as $i=>$value) {
			echo "<option value=\"$i\">$value";
		}
		?></select></td></tr>
	<tr class="_service"><td>Service:</td>
	    <td><input type="hidden" name="seq" value="0"> <select name="service" onchange="_check_svc(this)">
<?php
	foreach ($svc_type as $j=>$value) {
		echo "<option value=\"$j\">".$value;
	}
?>
		</select></td>
	    <td><div id="_svcname" style="display:none">&nbsp;&nbsp;Name: <input type="text" name="svc_name" value="" size="20"> </div></td></tr>
	<tr><td>Type:</td>
	    <td colspan="2"><select name="type">
		<option value="50">arg
		<option value="51">optarg
		</select></td></tr>
	<tr><td>Vendor:</td>
	    <td colspan="2"><select id="vid" name="vid"><?php
		foreach ($vnd_array as $i=>$value) {
			echo "<option value=\"$i\">$value";
		}
		?></select></td></tr>
	<tr><td>Attribute:</td>
	    <td colspan="2"><div class="ui-widget"><input type="text" id="attrib" name="value1" size="20"></div></td></tr>
	<tr><td>Value:</td>
	    <td colspan="2"><input type="text" id="value" name="value" size="35">&nbsp;<select id="attrfmt" name="attrfmt"></select><script>
		var $select = $('#attrfmt');
		$.each(attr_format, function(i, val){
			$select.append($('<option />', { value: (i), text: val }));
		});
		</script><br><div id="message"></div></td></tr>
	<tr><td><input type="hidden" name="option" value="1">
	        <input type="hidden" id="attrid" name="attrid" value="0"></td>
	    <td colspan="2"><input type="submit" name="_ssubmit" value="Add" onClick="return _check_serviceform(this.form)"> <input type="reset" value="Reset" onClick="return confirm('Are you sure you want to reset the data')"></td></tr>
	</table>
	</fieldset>
	</div>
</td></tr>
<tr><td>
	<table border=1 width="100%" class="_table2">
	<tr><th>Auth</th><th>Service</th><th>Type</th><th>Vendor</th><th>Attribute</th><th>Value</th></tr>
<?php
        for ($i = 0; $i < count($svc_array); $i++) {
                echo "<tr>";
                echo "<td>".$attr_auth[$svc_array[$i]["auth"]]."</td>";
		if ($svc_array[$i]["service"] == 59) {
                	echo "<td>".$svc_array[$i]["svc_name"]."</td>";
		} else {
                	echo "<td>".$svc_type[$svc_array[$i]["service"]]."</td>";
		}
                echo "<td>".$node_type[$svc_array[$i]["type"]]."</td>";
                echo "<td>".$vnd_array[$svc_array[$i]["vid"]]."</td>";
                echo "<td>".$svc_array[$i]["value1"]."</td>";
                echo "<td>".$svc_array[$i]["value"]."</td>";
              /*  echo "<td width=25><a href=\"Javascript:_modify(svc_info[$i])\" title=\"Modify Service\"><img src=\"images/modify.gif\" width=25 border=0></a></td>"; */
                echo "<td><input type=\"image\" width=25 border=0 src=\"images/trash.gif\" onclick=\"return _delete(this.form,svc_info[$i]);\" title=\"Delete Service\"></td></tr>\n";
        }
?>
	</table>
</td></tr>
</table>
</fieldset>
</form>
<script>
$(document).ready(function() {
	$('#auth').change(function() {
		if ($('#auth').val() == 2) {
			$('._service').hide();
		} else {
			$('._service').show();
		}
	});
	$('#value').change(function() {
		switch(attr_format[$('#attrfmt').val()]) {
		    case "string":
			if (/[<>]/.test($(this).val())) {
				alert("Characters not allowed <>");
				$(this).val("");
				$(this).focus();
			}
			break;

		    case "integer":
			if (isNaN($('#value').val())) {
				$('#message').html("<font color=\"red\">Only integers are allowed.</font>");
				$('#value').val("");
				$('#value').focus();
			}
			break;

		    case "email":
			var re = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
			if (!re.test($('#value').val())) {
				$('#message').html("<font color=\"red\">Not a valid email.</font>");
				$('#value').val("");
				$('#value').focus();
			}
			break;

		    case "ipaddr":
	//		var ipPattern = /^([01]?[0-9]?[0-9]|2[0-4][0-9]|25[0-5])\\.([01]?[0-9]?[0-9]|2[0-4][0-9]|25[0-5])\\.([01]?[0-9]?[0-9]|2[0-4][0-9]|25[0-5])\\.([01]?[0-9]?[0-9]|2[0-4][0-9]|25[0-5])$/;
			var ipPattern = /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
			if (!ipPattern.test($('#value').val())) {
				$('#message').html("<font color=\"red\">Not a valid IP address.</font>");
				$('#value').val("");
				$('#value').focus();
			}
			break;

		    case "octets":
			var Pattern = /^.*$/;
			if (!Pattern.test($('#value').val())) {
				$('#message').html("<font color=\"red\">Not a valid octets.</font>");
				$('#value').val("");
				$('#value').focus();
			}
			break;

		    case "ipv6addr":
			var ipPattern = /^((?:[0-9A-Fa-f]{1,4}))((?::[0-9A-Fa-f]{1,4}))*::((?:[0-9A-Fa-f]{1,4}))((?::[0-9A-Fa-f]{1,4}))*|((?:[0-9A-Fa-f]{1,4}))((?::[0-9A-Fa-f]{1,4})){7}$/;
			if (!ipPattern.test($('#value').val())) {
				$('#message').html("<font color=\"red\">Not a valid IPv6 address.</font>");
				$('#value').val("");
				$('#value').focus();
			}
			break;

		    case "ipv6prefix":
			var ipPattern = /^((?:[0-9A-Fa-f]{1,4}))*:((?:[0-9A-Fa-f]{1,4}))*:((?:[0-9A-Fa-f]{1,4}))*:((?:[0-9A-Fa-f]{1,4}))$/;
			if (!ipPattern.test($('#value').val())) {
				$('#message').html("<font color=\"red\">Not a valid IPv6 prefix.</font>");
				$('#value').val("");
				$('#value').focus();
			}
			break;

		    case "ethernet":
			var Pattern = /^([0-9A-F]{2}[:-]){5}([0-9A-F]{2})$/;
			if (!Pattern.test($('#value').val())) {
				$('#message').html("<font color=\"red\">Not a valid MAC address.</font>");
				$('#value').val("");
				$('#value').focus();
			}
			break;
		}
	});
});
</script>
