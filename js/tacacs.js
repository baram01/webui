// Script Source: CodeLifter.com
// Copyright 2003
// Do not remove this header

isIE=document.all;
isNN=!document.all&&document.getElementById;
isN4=document.layers;
isHot=false;

function ddInit(e){
  topDog=isIE ? "BODY" : "HTML";
  whichDog=isIE ? document.all.theLayer : document.getElementById("theLayer");
  hotDog=isIE ? event.srcElement : e.target;
  while (hotDog.id!="titleBar"&&hotDog.tagName!=topDog){
    hotDog=isIE ? hotDog.parentElement : hotDog.parentNode;
  }

  if (hotDog.id=="titleBar"){
    offsetx=isIE ? event.clientX : e.clientX;
    offsety=isIE ? event.clientY : e.clientY;
    nowX=parseInt(whichDog.style.left);
    nowY=parseInt(whichDog.style.top);
    ddEnabled=true;
    document.onmousemove=dd;
  }
}

function dd(e){
  if (!ddEnabled) return;
  whichDog.style.left=isIE ? nowX+event.clientX-offsetx : nowX+e.clientX-offsetx;
  whichDog.style.top=isIE ? nowY+event.clientY-offsety : nowY+e.clientY-offsety;
  return false;
}

function ddN4(whatDog){
  if (!isN4) return;
  N4=eval(whatDog);
  N4.captureEvents(Event.MOUSEDOWN|Event.MOUSEUP);
  N4.onmousedown=function(e){
    N4.captureEvents(Event.MOUSEMOVE);
    N4x=e.x;
    N4y=e.y;
  }
  N4.onmousemove=function(e){
    if (isHot){
      N4.moveBy(e.x-N4x,e.y-N4y);
      return false;
    }
  }
  N4.onmouseup=function(){
    N4.releaseEvents(Event.MOUSEMOVE);
  }
}

function hideMe(){
  if (isIE||isNN) whichDog.style.visibility="hidden";
  else if (isN4) document.theLayer.visibility="hide";
}

function showMe(){
  if (isIE||isNN) whichDog.style.visibility="visible";
  else if (isN4) document.theLayer.visibility="show";
}

function addMe(obj) {
	if (document.getElementById(obj).style.display == "none") {
		document.getElementById(obj).style.display = "";
	} else {
		document.getElementById(obj).style.display = "none";
	}
}

document.onmousedown=ddInit;
document.onmouseup=Function("ddEnabled=false");

//CodeLifter ends here

function addhandler(f) {
        for (var i=0; i<f.elements.length; i++) {
            if (f.elements[i].type == "text")
                f.elements[i].onfocus = new Function("this.select()");
        }
}

function IsValid(str, validChars) {
        var ret = true;
                                                                                
        if (str.length == 0) return false;
        for (i=0; i < str.length && ret == true; i++) {
                if (validChars.indexOf(str.charAt(i)) == -1) {
                        ret = false;
                }
        }
                                                                                
        return ret;
}

function logon(cvalue, etime) {
	var d = new Date();
	d.setTime(d.getTime() + (etime*3600));
	var expires = "expires="+ d.toUTCString();
	document.cookie = "login=" + cvalue + "; " + expires;
}

function logoff() {
	document.cookie = "login=; expires=Thu, 01 Jan 1970 00:00:00 UTC";
	document.cookie = "uname=; expires=Thu, 01 Jan 1970 00:00:00 UTC";
	top.location.href = "";
}

function _openCommand(pid, uid, position) {
//	document.getElementById("theLayerTable").style.width = "850px";
	document.getElementById("theLayerTable").style.top = document.body.scrollTop+"px";
	document.getElementById("theLayer").style.top = position;
	document.getElementById("titleName").innerHTML = "Commands";
        showMe();
        document.getElementById("_nodeframe").src = "node.php?_ret=5&pid="+pid+"&uid="+uid+"&_service=56";
}

function _openService(pid, uid, position) {
	document.getElementById("theLayerTable").style.width = "850";
	document.getElementById("theLayerTable").style.top = document.body.scrollTop+"px";
	document.getElementById("theLayer").style.top = position;
	document.getElementById("titleName").innerHTML = "Services";
        showMe();
        document.getElementById("_nodeframe").src = "node.php?_ret=5&pid="+pid+"&uid="+uid+"&_service=1";
}

function _checkpass(obj, obj1) {
        if (obj.value != obj1.value) {
                alert("Fields does not match");
                obj.value = "";
                obj1.value = "";
                obj1.focus();
        }
}

function _verify(obj, type)
{
        var ret = true;
	var msg = "";

        if (type == "integer") {
                var anum=/(^\d+$)/;
                if (!anum.test(obj.value)) {
                        alert("Must be numeric.");
			obj.value = "";
                        obj.focus();
                        ret = false;
                }
        }

        if (type == "email") {
                if (obj.value.length > 0)
                {
			var re = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/; 
//                        if ((obj.value.indexOf(' ') >= 0) || (obj.value.indexOf('@') == -1)) {
			if (!re.test(obj.value)) {
                                alert("Not a valid email");
                                obj.focus();
                                ret = false;
                        }
                }
        }

        if (type == "subnet") {
                s = obj.value.split('/');
		var ipPattern = /^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/;
		var ipArray = s[0].match(ipPattern);
		var ip6Pattern = /^((?:[0-9A-Fa-f]{1,4}))((?::[0-9A-Fa-f]{1,4}))*::((?:[0-9A-Fa-f]{1,4}))((?::[0-9A-Fa-f]{1,4}))*|((?:[0-9A-Fa-f]{1,4}))((?::[0-9A-Fa-f]{1,4})){7}$/g;
		var ip6Array = s[0].match(ip6Pattern);

                if (s[1] != null) {
			if (ipArray) {
                            if ((s[1]<0)||(s[1]>32)) {
                                alert("Not a valid IPv4 maskbits");
                                obj.focus();
                                return false;
                            }
			}

			if (ip6Array) {
                            if ((s[1]<0)||(s[1]>128)) {
                                alert("Not a valid IPv6 maskbits");
                                obj.focus();
                                return false;
                            }
			}
                }

		if (s[0] == "255.255.255.255") {
			alert("Cannot use 255.255.255.255 as IPv4 network");
			obj.focus();
			ret = false;
		} else {
			if ((ipArray == null) && (ip6Array == null)) {
				alert("Not a valid IPv4  or IPv6 address");
				obj.focus();
				ret = false;
			} else if (ip6Array == null) {
				for (var i=0; i<4; i++) {
					var thisSegment = ipArray[i];
					if (thisSegment > 255) {
						alert("Not valid IP address");
						obj.focus();
						ret = false;
						i = 4;
					}
				}

				if (i < 4) {
					alert("Not valid IP address");
					obj.focus();
					ret = false;
				}
			}
		}
        }

        return ret;
}

var _divElem = "";

var func_Results_jq = function(data, status) {
        document.getElementById(_divElem).innerHTML = data;
};

function _SearchValue(attr_id, vid, vrows, div_id) {
        var src = "result.php?_table=attr_value&debug=0";
        var _divRow = "#_showrow_color"+div_id;
        var _divShow = "#_showrow"+div_id;
        var attr = $(_divShow).attr('active');
        
        src += "&vrows="+vrows+"&attrid="+attr_id+"&vid="+vid;
        _divElem = "_showrow_data"+div_id;

        if (typeof attr !== typeof undefined && attr !== false ) {
                $(_divRow).css('background-color','#FFFFFF');
                $(_divShow).removeAttr('active');
                $(_divShow).hide();
        } else {
                $(_divRow).css('background-color','lightblue');
                $(_divShow).attr('active','');
                $(_divShow).show();

                $.get(src, func_Results_jq);
        }
}

