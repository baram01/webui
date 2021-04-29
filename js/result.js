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

function _Result(_table, _offset, _vrows, _index, _search) {
        var src = "result.php?_ret=5&_table="+_table;
            src += "&offset=" + _offset;
            src += "&vrows=" + _vrows;
            src += "&_index=" + _index;
	    src += "&" + _search;

        $.get(src, function(data,status) {
                document.getElementById("_results"+_index).innerHTML = data;
        });
}

