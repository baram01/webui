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

Changes:
02/02/2019	Andrew Young
	- Added support for temporary password
02/02/2010	Andrew Young
	- Added flags checked or not checked in getUserResults
*/

function _checked(obj, obj1)
{
	if (obj.checked) obj1.value = "1";
	else obj1.value = "0";
}

function _checked2(obj, obj1, obj2)
{
	if (obj.checked) obj1.value = parseInt(obj1.value) + obj2;
	else obj1.value = parseInt(obj1.value) - obj2;
}

function _check_user_form(user)
{
	var ret = true;
	var form = document.userform;
	var msg = "";

	if (!form.uid.value) {
		if (user == "1") msg = "User ID ";
		else msg = "Group Name ";
		msg += "cannot be blank";
		form.uid.focus();
		ret = false;
	}

	if (user=="2") {
		if ((form.auth.value=="3")&&(!form.gid.value)) {
			msg += "LDAP Group cannot be blank";
			form.gid.focus();
			ret = false;
		}
	}

	if (!form.password.value) {
		if ((form.auth.value == "1")&&(user == "1")&&(form.option.value!="2")) {
			msg = msg + "\nPassword cannot be blank";
			if (ret) form.password.focus();
			ret = false;
		}
	} else {
		if (form.password.value.length < form.min_passwd.value) {
			msg = msg + "\nPassword cannot be less than " + form.min_passwd.value + " charaters";
			if (ret) form.password.focus();
			ret = false;
		}
	}

	if (!form.comment.value)
		msg += "\nComment is not required but recommended.";

	if (form.maxsess.value) {
		if (!IsValid(form.maxsess.value,"0123456789")) {
			msg += "\nMaxsession must be numeric.";
			if (ret) form.maxsess.focus();
			ret = false;
		}
	} else
		form.maxsess.value = "0";

	if (!form.expires.value) {
		form.expires.value = "0000-00-00 00:00:00";
	}

/*	if (process_prov) {
		if (form.option.value == 1) {
			alert("Provision processing turn on");
		}
	} */

	if (msg) alert(msg);
	if (ret) {
		if (form.uid.disabled) form.uid.disabled = false;
		if (form.auth.disabled) form.auth.disabled = false;
	}

	return ret;
}

function _check_method(obj) {
	if (obj.value == "1") {
		_show_class('tr','_passwords');
	} else {
		_hide_class('tr','_passwords');
	}
}

function _delete(obj) {
        var msg = "Do you want really want to delete User("+obj+")?";

        if (confirm(msg)) {
                document.userform.uid.value = obj;
                document.userform.option.value = "3";
                document.userform.submit();
        }
}

function getUserResults() {
        if (getRequest.readyState == 4) {
                var xmldoc = getRequest.responseXML.documentElement;

                if (xmldoc.getElementsByTagName('disable')[0].firstChild.nodeValue == "1") {
                	resultForm.disable.value = "1";
                	resultForm.check_disable.checked = true;
		} else {
                	resultForm.disable.value = "0";
                	resultForm.check_disable.checked = false;
		}
                resultForm.id.value = xmldoc.getElementsByTagName('id')[0].firstChild.nodeValue;
                resultForm.uid.value = xmldoc.getElementsByTagName('uid')[0].firstChild.nodeValue;
                if (xmldoc.getElementsByTagName('gid')[0].firstChild != null)
                	resultForm.gid.value = xmldoc.getElementsByTagName('gid')[0].firstChild.nodeValue;
		else
                	resultForm.gid.value =  '';
                if (xmldoc.getElementsByTagName('comment')[0].firstChild != null)
                        resultForm.comment.value = xmldoc.getElementsByTagName('comment')[0].firstChild.nodeValue;
		else
                        resultForm.comment.value = '';
                resultForm.auth.value = xmldoc.getElementsByTagName('auth')[0].firstChild.nodeValue;
                _check_method(resultForm.auth);
		if (xmldoc.getElementsByTagName('user')[0].firstChild.nodeValue == 1) {
                	resultForm.flags.value = xmldoc.getElementsByTagName('flags')[0].firstChild.nodeValue;
			if (resultForm.flags.value & 2)
				resultForm.check_flags.checked = true;
			else
				resultForm.check_flags.checked = false;
			if (resultForm.flags.value & 8)
				resultForm.check_lock.checked = true;
			else
				resultForm.check_lock.checked = false;
		}
                if (xmldoc.getElementsByTagName('expires')[0].firstChild.nodeValue != "0000-00-00 00:00:00")
                	resultForm.expires.value = xmldoc.getElementsByTagName('expires')[0].firstChild.nodeValue;
		else
                	resultForm.expires.value = '';
                resultForm.disable.value = xmldoc.getElementsByTagName('disable')[0].firstChild.nodeValue;
                if (xmldoc.getElementsByTagName('b_author')[0].firstChild != null)
                        resultForm.b_author.value = xmldoc.getElementsByTagName('b_author')[0].firstChild.nodeValue;
		else
                        resultForm.b_author.value = '';
                if (xmldoc.getElementsByTagName('a_author')[0].firstChild != null)
                        resultForm.a_author.value = xmldoc.getElementsByTagName('a_author')[0].firstChild.nodeValue;
		else
                        resultForm.a_author.value = '';
                if (xmldoc.getElementsByTagName('svc_dflt')[0].firstChild.nodeValue == "1") {
                        resultForm.svc_dflt.value = "1";
                        resultForm.check_svc_dflt.checked = true;
        	} else {
                        resultForm.svc_dflt.value = "0";
                        resultForm.check_svc_dflt.checked = false;
		}
                if (xmldoc.getElementsByTagName('cmd_dflt')[0].firstChild.nodeValue == "1") {
                        resultForm.cmd_dflt.value = "1";
                        resultForm.check_cmd_dflt.checked = true;
                } else {
                        resultForm.cmd_dflt.value = "0";
                        resultForm.check_cmd_dflt.checked = false;
		}
                resultForm.maxsess.value = xmldoc.getElementsByTagName('maxsess')[0].firstChild.nodeValue;
                resultForm.acl_id.value = xmldoc.getElementsByTagName('acl_id')[0].firstChild.nodeValue;
                if (xmldoc.getElementsByTagName('shell')[0].firstChild != null)
			resultForm.shell = xmldoc.getElementsByTagName('shell')[0].firstChild.nodeValue;
		else
			resultForm.shell = '';
                if (xmldoc.getElementsByTagName('homedir')[0].firstChild != null)
			resultForm.homedir = xmldoc.getElementsByTagName('homedir')[0].firstChild.nodeValue;
		else
			resultForm.homedir = '';
        }
}

function _add(obj, user, password) {
        resultForm = document.userform;

        if (resultForm.option.value == "2") {
                resultForm.uid.disabled = false;
		if (user == 1) {
			resultForm.check_lock.disabled = true;
		}
                resultForm.option.value = "1";
                resultForm._submit.value = "Add";
               	resultForm.id.value = resultForm._lastID.value;
               	resultForm.uid.value = "";
               	resultForm.disable.value = "0";
               	resultForm.check_disable.checked = false;
               	resultForm.gid.value =  '';
		resultForm.comment.value = '';
		resultForm.flags.value = "0";
               	resultForm.expires.value = '';
               	resultForm.password.value = password;
               	resultForm.re_password.value = password;
                resultForm.enable.value = password;
                resultForm.re_enable.value = password;
		resultForm.b_author.value = '';
		resultForm.a_author.value = '';
		resultForm.svc_dflt.value = "0";
		resultForm.check_svc_dflt.checked = false;
		resultForm.cmd_dflt.value = "0";
		resultForm.check_cmd_dflt.checked = false;
		resultForm.maxsess.value = "0";
		resultForm.acl_id.value = "0";
		resultForm.shell = '';
		resultForm.homedir = '';
        } else {
               	resultForm.id.value = resultForm._lastID.value;
		if (user == 1) {
			document.getElementById("check_flags").checked = true;
		}
                resultForm.password.value = password;
                resultForm.re_password.value = password;
                resultForm.enable.value = password;
                resultForm.re_enable.value = password;
                addMe(obj);
        }
}

function _modify(pid, uid, user) {
	resultForm = document.userform;
	getQueryXML(getUserResults,"user","uid="+uid+"&user="+user);
	resultForm.uid.disabled = true;
	if (user == 1) resultForm.check_lock.disabled = false;
	resultForm.option.value = "2";
	resultForm.password.value = "";
	resultForm.re_password.value = "";
	resultForm.enable.value = "";
	resultForm.re_enable.value = "";
	resultForm._submit.value = "Modify";
	document.getElementById("_useradd").style.display = "";
	resultForm.id.focus();
}

function _updateid(id) {
	alert(id);
	document.getElementById("id").value = id;
}

function _group(pid, uid) {
	document.userform.uid.disabled = true;
	document.userform.group.value = uid;
	document.userform.option.value = "4";
	document.userform.action = "?menu=admin&module=user";
	document.userform.submit();
}

function _hide_class(e, spv) {

	var nodes=document.getElementsByTagName(e);
	for (var i = 0; i < nodes.length; i++) {
		var nodeObj = nodes.item(i);
		for (var j = 0; j < nodeObj.attributes.length; j++) {
			if (nodeObj.attributes.item(j).nodeName == 'class') {
				if (nodeObj.attributes.item(j).nodeValue == spv)
					nodeObj.style.visibility = "collapse";
				//	nodeObj.style.display = "none";
			}
		}
	}
}

function _show_class(e, spv) {

	var nodes=document.getElementsByTagName(e);
	for (var i = 0; i < nodes.length; i++) {
		var nodeObj = nodes.item(i);
		for (var j = 0; j < nodeObj.attributes.length; j++) {
			if (nodeObj.attributes.item(j).nodeName == 'class') {
				if (nodeObj.attributes.item(j).nodeValue == spv)
					nodeObj.style.visibility = "";
				//	nodeObj.style.display = "";
			}
		}
	}
}

function _open_contact(uid, obj) {
	if (uid.value) {
		document.getElementById("theLayerTable").style.width = "600";
		document.getElementById("titleName").innerHTML = "Contact Information";
		showMe();
		document.getElementById("_nodeframe").src = "contact.php?_ret=5&id="+this.id+"&uid="+uid.value+"&update=1";
	} else {
		document.getElementById("uid").focus();
	}
}

function _open_contact1(uid) {
	document.getElementById("theLayerTable").style.width = "600px";
	document.getElementById("theLayerTable").style.top = document.body.scrollTop+"px";
	document.getElementById("titleName").innerHTML = "Contact Information";
	showMe();
	document.getElementById("_nodeframe").src = "contact.php?_ret=5&id="+this.id+"&uid="+uid+"&update=0";
}
