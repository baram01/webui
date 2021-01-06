/*
    Copyright (C) 2019  3 Young, Inc

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

function _Report(_table, _offset, _vrows, _index) {
        var src = "report.php?_ret=1&table="+_table;
        src += "&sdate=" + document.getElementById("sdate").value;
        src += "&edate=" + document.getElementById("edate").value;
        src += "&user=" + document.getElementById("user").value;
        src += "&client_ip=" + document.getElementById("client_ip").value;

	switch (_table) {
	case "access2":
        	src += "&nas=" + document.getElementById("nas").value;
	case "audit":
        	src += "&status=" + document.getElementById("status").value;
		break;
	case "accounting2":
        	src += "&nas=" + document.getElementById("nas").value;
        	src += "&cmd=" + document.getElementById("cmd").value;
		break;
	}
        src += "&offset=" + _offset;
        src += "&vrows=" + _vrows;
        src += "&_index=" + _index;

        $.get(src, function(data,status) {
                document.getElementById("_results"+_index).innerHTML = data;
        });
}

