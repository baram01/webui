<?php
/*
    Copyright (C) 2003-2020  3 Youngs, Inc
                                                                                                                                                                 
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

if (!$_ret) {
        echo "<script language=\"JavaScript\"> top.location.href=\"?module=main\"; </script>";
}
?>
<script language="JavaScript" src="js/calendar3.js"></script>
<script language="JavaScript" src="js/reports.js"></script>
<script language="JavaScript">
<!--
function Search() {
        var src = "report.php?_ret=1&table=accounting2";
        src += "&sdate=" + document.getElementById("sdate").value;
        src += "&edate=" + document.getElementById("edate").value;
        src += "&user=" + document.getElementById("user").value;
        src += "&client_ip=" + document.getElementById("client_ip").value;
        src += "&nas=" + document.getElementById("nas").value;
        src += "&cmd=" + document.getElementById("cmd").value;
        src += "&auth=" + document.getElementById("auth").value;
        src += "&offset=0";
        src += "&vrows=" + document.getElementById("vrows").value;
        src += "&_index=";

<?php
  foreach ($db_config->{'hosts'} as $key=>$value) {
        echo "
        \$.get(src+\"".$key."\", function(data, status) {
                document.getElementById(\"_results".$key."\").innerHTML = data;
        });\n";
  }
?>
}

$( function() {
    $( "#user" ).autocomplete({
      source: function( request, response ) {
        $.ajax( {
          url: "queryjson.php?table=user",
          dataType: "jsonp",
          data: {
            term: request.term,
            user: 1
          },
          success: function( data ) {
            response( data );
          }
        } );
      },
      minLength: 2,
      select: function( event, ui ) {
        $( "#user" ).val( ui.item.uid );
        $( "#id" ).val( ui.item.id );
        return false;
      }
    })
    .autocomplete( "instance" )._renderItem = function (ul, item ) {
        return $( "<li>")
                .append( "<div>" + item.uid + "</div>" )
                .appendTo( ul );
    };

    $( "#cmd" ).autocomplete({
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
    $( "#sdate" ).datetimepicker({dateFormat:'yy-mm-dd', timeInput: true, showHour:false, showMinute:false, showSecond:false, timeFormat: 'HH:mm:ss'});
    $( "#edate" ).datetimepicker({dateFormat:'yy-mm-dd', timeInput: true, showHour:false, showMinute:false, showSecond:false, timeFormat: 'HH:mm:ss'});
} );

//-->
</script>
<form name="account" method="post" action="index.php?menu=report&module=account">
<fieldset class="_collapsible"><legend>Accounting Report</legend>
<table border=0 width="100%">
<tr><td>
	<table class="_table">
	<tr><td>Start Date:</td>
	    <td><input type="text" id="sdate" name="sdate" size=20 value="<?php if (isset($sdate)) { echo $sdate; } ?>"><!-- &nbsp;<a href="javascript:open_tcalendar(document.forms['account'].elements['sdate']);"><img src="images/cal.gif"></img></a> --></td>
	    <td width="30">&nbsp;</td>
	    <td>End Date:</td>
	    <td><input type="text" id="edate" name="edate" size=20 value-"<?php if (isset($edate)) { echo $edate; } ?>"><!-- &nbsp;<a href="javascript:open_tcalendar(document.forms['account'].elements['edate']);"><img src="images/cal.gif"></img></a> --></td>
	</tr>
	<tr><td>User ID:</td>
	    <td><div class="ui-widget"><input type="text" id="user" name="user" size=20></div></td>
	    <td width="30">&nbsp;</td>
	    <td>User IP:</td>
	    <td><input type="text" id="client_ip" name="client_ip" size=20 value="<?php if (isset($client_ip)) {  echo $client_ip; } ?>"></td>
	</tr>
	<tr><td>NAS:</td>
	    <td><input type="text" id="nas" name="nas" size=20 value="<?php if (isset($nas)) { echo $nas; } ?>"></td>
	    <td width="30">&nbsp;</td>
	    <td>Command:</td>
	    <td><div class="ui-widget"><input type="text" id="cmd" name="cmd" size=20 <?php if (isset($cmd)) { echo "value=\"$cmd\";"; } ?>></div></td>
        <tr><td>Auth:</td>
	    <td><select id="auth" name="auth" onchange="javascript:Search()">
		<option value="0">both</option>
		<option value="1">tacacs</option>
		<option value="2">radius</option>
		</select></td>
            <td width="30">&nbsp;<input type="hidden" name="offset" value="<?php if (isset($offset)) { echo $offset; } ?>"></td>
	    <td>Rows to view:</td>
            <td><select id="vrows" name="vrows" onchange="javascript:Search()">
<?php foreach($_vrows as $_item) {
        if (!$_item) {
echo "                                  <option value=\"$_item\">all</option>";
        } else {
echo "                                  <option value=\"$_item\">$_item</option>";
        }
      } ?>
                </select><script language="JavaScript">document.forms["account"].vrows.value=admin_vrows;</script></td>
            <td><a href="javascript:Search()"><img src="images/search.gif" height=15></img></a></td>
            <td></td>
        </tr>
	</table>
</td></tr>
<?php
foreach ($db_config->{'hosts'} as $key=>$value) {
        echo "<tr><td> <div id=\"_results".$key."\"> </div></td></tr>\n";
}
?>
</table>
</fieldset>
</form>

<script>
$(document).ready(function() {
        Search();
});
</script>

