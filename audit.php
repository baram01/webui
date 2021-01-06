<?php
/*
    Copyright (C) 2002-2020  3 Youngs, Inc
                                                                                                                                                                 
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
	var src = "report.php?_ret=1&table=audit";
	src += "&sdate=" + document.forms["audit"].sdate.value;
	src += "&edate=" + document.forms["audit"].edate.value;
	src += "&user=" + document.forms["audit"].user.value;
	src += "&client_ip=" + document.forms["audit"].client_ip.value;
	src += "&status=" + document.forms["audit"].status.value;
	src += "&offset=0";
	src += "&vrows=" + document.forms["audit"].vrows.value;
	src += "&_index=0";

	$.get(src, function(data, status) {
		document.getElementById("_results0").innerHTML = data;
	});
}

$( function() {
    $( "#user" ).autocomplete({
      source: function( request, response ) {
        $.ajax( {
          url: "queryjson.php?table=admin",
          dataType: "jsonp",
          data: {
            term: request.term
          },
          success: function( data ) {
            response( data );
          }
        } );
      },
      minLength: 2,
      select: function( event, ui ) {
        $( "#user" ).val( ui.item.uid );
        return false;
      }
    })
    .autocomplete( "instance" )._renderItem = function (ul, item ) {
        return $( "<li>")
                .append( "<div>" + item.uid + "</div>" )
                .appendTo( ul );
    };
    $( "#sdate" ).datetimepicker({dateFormat:'yy-mm-dd', timeInput: true, showHour:false, showMinute:false, showSecond:false, timeFormat: 'HH:mm:ss'});
    $( "#edate" ).datetimepicker({dateFormat:'yy-mm-dd', timeInput: true, showHour:false, showMinute:false, showSecond:false, timeFormat: 'HH:mm:ss'});
} );

//-->
</script>
<form name="audit" method="post" action="index.php?menu=report&module=audit">
<fieldset class="_collapsible"><legend>Audit Report</legend>
<table border=0 width="100%">
<tr><td>
	<table class="_table">
	<tr><td>Start Date:</td>
	    <td><input type="text" id="sdate" name="sdate" size=20><!-- &nbsp;<a href="javascript:open_tcalendar(document.forms['audit'].elements['sdate']);"><img src="images/cal.gif"></img></a> --></td>
	    <td width="30">&nbsp;</td>
	    <td>End Date:</td>
	    <td><input type="text" id="edate" name="edate" size=20><!-- &nbsp;<a href="javascript:open_tcalendar(document.forms['audit'].elements['edate']);"><img src="images/cal.gif"></img></a> --></td>
	</tr>
	<tr><td>User ID:</td>
	    <td><div class="ui-widget"><input type="text" id="user" name="user" size=20></div></td>
	    <td width="30">&nbsp;</td>
	    <td>User IP:</td>
	    <td><input type="text" id="client_ip" name="client_ip" size=20></td>
	</tr>
	<tr><td>&nbsp;</td>
	    <td>&nbsp;</td>
	    <td width="30">&nbsp;</td>
	    <td>Status:</td>
	    <td><input type="text" id="status" name="status" size=20></td>
	</tr>
	<tr><td>Rows to view:</td>
	    <td><select id="vrows" name="vrows" onchange="javascript:Search()">
<?php foreach($_vrows as $_item) {
        if (!$_item) {
echo "                                  <option value=\"$_item\">all</option>";
        } else {
echo "                                  <option value=\"$_item\">$_item</option>";
        }
      } ?>
		</select><script language="JavaScript">document.forms["audit"].vrows.value=admin_vrows;</script></td>
	    <td width="30">&nbsp;</td>
	    <td><a href="javascript:Search()"><img src="images/search.gif" height=15></img></a></td>
	    <td></td>
	</tr>
	</table>
</td></tr>
<tr><td> <div id="_results0"> </div>
</td></tr>
</table>
</fieldset>
</form>

<script>
$(document).ready(function() {
        Search();
});
</script>

