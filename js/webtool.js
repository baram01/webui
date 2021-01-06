/*
    Copyright (C) 2017  3 Young, Inc



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

function _checkpass(obj, obj1) {
        if (obj.value != obj1.value) {
                alert("Passwords do not match");
                obj.value = "";
                obj1.value = "";
                obj1.focus();
        }
}

function _check_complexity(element, element2)
{
        var res;
        var ret = 0, msg;

	var value = element.val();
	var value1 = element2.val();

        if (value.length < cfg_pass_complex.size) {
	    if (value.length != 0) {
                msg = "Password is too small.  Minimum size is "+ cfg_pass_complex.size+" characters.";
                ret = 1;
	    }
        } else if (value == value1) {
                msg = "New password cannot be the same as old password";
                ret = 1;
        } else {
           if (cfg_pass_complex.complexity) {
                msg = "Password must have at least ";
                res = value.match(/[A-Z]/g);
                if (cfg_pass_complex.upper && (res != null) && (res.length < cfg_pass_complex.upper)) {
                        msg = msg + " " + cfg_pass_complex.upper + " uppercase";
                        ret = 1;
                } else if (cfg_pass_complex.upper && (res == null)) {
                        msg = msg + " " + cfg_pass_complex.upper + " uppercase";
                        ret = 1;
                }

                res = value.match(/[a-z]/g);
                if (cfg_pass_complex.lower && (res != null) && (res.length < cfg_pass_complex.lower)) {
                        if (ret) msg += ",";
                        msg = msg + " " + cfg_pass_complex.lower + " lowercase";
                        ret = 1;
                } if (cfg_pass_complex.lower && (res == null)) {
                        if (ret) msg += ",";
                        msg = msg + " " + cfg_pass_complex.lower + " lowercase";
                        ret = 1;
                }

                res = value.match(/[0-9]/g);
                if (cfg_pass_complex.number && (res != null) && (res.length < cfg_pass_complex.number)) {
                        if (ret) msg += ",";
                        msg = msg + " " + cfg_pass_complex.number + " number";
                        ret = 1;
                } else if (cfg_pass_complex.number && (res == null)) {
                        if (ret) msg += ",";
                        msg = msg + " " + cfg_pass_complex.number + " number";
                        ret = 1;
                }

                res = value.match(/[!"#$%&'()*+,\-./:;<=>?@[\\\]^_`{|}~]/g);
                if (cfg_pass_complex.special && (res != null)&&(res.length < cfg_pass_complex.special)) {
                        if (ret) msg += ",";
                        msg = msg + " " + cfg_pass_complex.special + " special";
                        ret = 1;
                } else if (cfg_pass_complex.special && (res == null)) {
                        if (ret) msg += ",";
                        msg = msg + " " + cfg_pass_complex.special + " special";
                        ret = 1;
                } else if (cfg_pass_complex.special && (res == null)) {
                        if (ret) msg += ",";
                        msg = msg + " " + pass_special + " special";
                        ret = 1;
                }

                res = value.match(/(.)\1{1}/g);
                if (cfg_pass_complex.multi && (res != null) && (res.length < cfg_pass_complex.multi)) {
                        if (ret) msg += ",";
                        msg = msg + " " + cfg_pass_complex.multi + " consequent";
                        ret = 1;
                }
           }
        }

        if (ret) {
                alert(msg);
		return false;
        }

	return true;
}

