<?php
$banner = "WebUI";
//$version = "5.0";
//$release = "b5";
$copyrights_dates = "2002-2021";
$pagetitle = "WebUI $version$release";

$target_dir = "cust/";
$json_auth_meth_file =  $target_dir."auth_meth.json";
$json_db_file =  $target_dir."db_config.json";
$json_site_file =  $target_dir."site_config.json";
$json_pass_file =  $target_dir."pass_complex.json";
$json_prov_file =  $target_dir."prov_config.json";

$site_config = json_decode(file_get_contents($json_site_file));
$copyrights = "All logos and trademarks in this site are property of their respective owner,<br> rest are (c)$copyrights_dates ".$site_config->{"company_name"};
//$logo_gif = $site_config->{'logo'};

$pass_complex = json_decode(file_get_contents($json_pass_file));
$prov_config = json_decode(file_get_contents($json_prov_file));
$auth_method = json_decode(file_get_contents($json_auth_meth_file));
$db_config = json_decode(file_get_contents($json_db_file));

$dbtype = $db_config->{'type'};
$_uname = "";
if (isset($_COOKIE["uname"])) {
	$_uname = $_COOKIE["uname"];
}
$_remote = $_SERVER['REMOTE_ADDR'];

$netmask = array(
   "0.0.0.0","128.0.0.0","192.0.0.0","224.0.0.0","240.0.0.0",
   "248.0.0.0","252.0.0.0","254.0.0.0","255.0.0.0","255.128.0.0",
   "255.192.0.0","255.224.0.0","255.240.0.0","255.248.0.0","255.252.0.0",
   "255.254.0.0","255.255.0.0","255.255.128.0","255.255.192.0","255.255.224.0",
   "255.255.240.0","255.255.248.0","255.255.252.0","255.255.254.0","255.255.255.0",
   "255.255.255.128","255.255.255.192","255.255.255.224","255.255.255.240",
   "255.255.255.248","255.255.255.252","255.255.255.254","255.255.255.255" );

ob_start("ob_gzhandler");

if (!$debug) {
	error_reporting(E_ERROR);
}

function OpenTable()
{
	global $glob_width;
	echo "<TABLE BORDER=1 CELLPADDING=0 CELLSPACING=0 WIDTH=\"100%\">";
}

function CloseTable()
{
	echo "</TABLE>";
}

//function OpenDatabase($host, $user, $password, $db)
function OpenDatabase($db_cfg)
{
	global $_ERROR; // $dbtype, $redundancy;

	$host = $db_cfg->{'hosts'};
	$user = $db_cfg->{'uid'};
	$password = $db_cfg->{'pass'};
	$db = $db_cfg->{'name'};
	$redundancy = $db_cfg->{'redundancy'};
	$id = 0;

	switch ($db_cfg->{'type'}) {
	case "mysqli":
		$id = new mysqli($host[0], $user, $password, $db);
		if ($id->connect_errno) {
			$_ERROR = "Could not connect to MySQL DB at ".$host[0];

			if ($redundancy) {
				$id = new mysqli($host[1], $user, $password, $db);
				if ($id->connect_errno) {
					$_ERROR = "Could not connect to backup MySQL DB at ".$host[1];
				}
			}
		}
		break;
	case "mysql":
		if (!($id=@mysql_pconnect($host[0], $user, $password))) {
			$_ERROR = "Could not connect to MySQL database at ".$host[0];
			if ($redundancy) {
				if (!($id=@mysql_pconnect($host[1], $user, $password))) {
					$_ERROR = "Could not connect to the backup MySQL database at ".$host[1];
				}
			}
		}
		if ($id) {
			if (!mysql_select_db($db, $id))
				$_ERROR = "Could not select the database";
		}
		break;
	case "odbc":
		if (!($id=@odbc_connect("Server=".$host[0].";Database=$db", $user, $password, SQL_CURR_USE_ODBC)))
			$_ERROR = "Could not connect to ODBC database";
		break;
	}

	return $id;
}

// function OpenDatabase2($host, $user, $password, $db)
function OpenDatabase2($host, $db_cfg)
{
	global $_ERROR; //, $dbtype;
	$id = 0;

	switch ($db_cfg->{'type'}) {
	case "mysqli":
		$id=new mysqli($host, $db_cfg->{'uid'}, $db_cfg->{'pass'}, $db_cfg->{'name'});
		if ($id->connect_errno) {
			$_ERROR = "Could not connect to MySQL database at ".$host;
		}
		break;
	case "mysql":
		if (!($id=@mysql_pconnect($host, $db_cfg->{'uid'}, $db_cfg->{'pass'}))) {
			$_ERROR = "Could not connect to MySQL database at ".$host;
		}
		if ($id) {
			if (!mysql_select_db($db_cfg->{'name'}, $id))
				$_ERROR = "Could not select the database";
		}

		break;
	case "odbc":
		if (!($id=@odbc_connect("DSN=".$db_cfg->{'name'}, $db_cfg->{'uid'}, $db_cfg->{'pass'}, SQL_CURR_USE_ODBC)))
			$_ERROR = "Could not connect to ODBC database";
		break;
	}

	return $id;
}

function OpenDatabase3($db_cfg, $index)
{
	global $_ERROR;
	$id = 0;
	$hosts = $db_cfg->{'hosts'};

	switch ($db_cfg->{'type'}) {
	case "mysqli":
		$id=new mysqli($hosts[$index], $db_cfg->{'uid'}, $db_cfg->{'pass'}, $db_cfg->{'name'});
		if ($id->connect_errno) {
			$_ERROR = "Could not connect to MySQL database at ".$host;
		}
		break;
	case "mysql":
		if (!($id=@mysql_pconnect($hosts[$index], $db_cfg->{'uid'}, $db_cfg->{'pass'}))) {
			$_ERROR = "Could not connect to MySQL database at ".$host;
		}
		if ($id) {
			if (!mysql_select_db($db_cfg->{'name'}, $id))
				$_ERROR = "Could not select the database";
		}

		break;
	case "odbc":
		if (!($id=@odbc_connect("Server=".$hosts[$index].";Database=".$db_cfg->{'name'}, $db_cfg->{'uid'}, $db_cfg->{'pass'}, SQL_CURR_USE_ODBC)))
			$_ERROR = "Could not connect to ODBC database";
		break;
	}

	return $id;
}

function CloseDatabase($id)
{
	GLOBAL $dbtype, $_ERROR;
	$ret = 0;

	switch ($dbtype) {
	case "mysqli":
		$ret = $id->close();
		break;
	case "mysql":
		$ret = @mysql_close($id);
		break;
	case "odbc":
		@odbc_close($id);
		if (@odbc_error($id))
			$_ERROR = $odbc_errormsg($id);
		else
			$ret = 1;
		break;
	}

	return $ret;
}

function SQLQuery($_query, $id)
{
	GLOBAL $dbtype;
	$ret = 0;

	switch ($dbtype) {
	case "mysqli":
		$ret = $id->query($_query);
		break;
	case "mysql":
		$ret = @mysql_query($_query, $id);
		break;
	case "odbc":
		$ret = @odbc_exec($id, $_query);
		break;
	}

	return $ret;
}

function SQLFetchAssoc($result)
{
	GLOBAL $dbtype;
	$ret = 0;

	switch ($dbtype) {
	case "mysqli":
		$ret = $result->fetch_assoc();
		break;
	}

	return $ret;
}

function SQLFetchRow($result)
{
	GLOBAL $dbtype;
	$ret = 0;

	switch ($dbtype) {
	case "mysqli":
		$ret = $result->fetch_array(MYSQLI_NUM);
		break;
	case "mysql":
		$ret = @mysql_fetch_row($result);
		break;
	case "odbc":
		$ret = @odbc_fetch_row($result);
		break;
	}

	return $ret;
}

function SQLFetchArray($result)
{
	GLOBAL $dbtype;
	$ret = 0;

	switch ($dbtype) {
	case "mysqli":
		$ret = $result->fetch_array(MYSQLI_BOTH);
		break;
	case "mysql":
		$ret = @mysql_fetch_array($result);
		break;
	case "odbc":
		if (@odbc_fetch_row($result)) {
			for ($j=1; $j <= @odbc_num_fields($result); $j++) {
				$field_name = @odbc_field_name($result, $j);
				$ret[$field_name] = @odbc_result($result, $field_name);
			}
		}
		break;
	}

	return $ret;
}

function SQLNumRows($result)
{
	GLOBAL $dbtype;
	$ret = 0;

	switch ($dbtype) {
	case "mysqli":
		$ret = $result->num_rows;
		break;
	case "mysql":
		$ret = @mysql_num_rows($result);
		break;
	case "odbc":
		$ret = @odbc_num_rows($result);
		break;
	}

	return $ret;
}

function SQLFreeResult($result)
{
	GLOBAL $dbtype;
	$ret = 0;

	switch ($dbtype) {
	case "mysqli":
		$ret = 0;
		$result->free();
		break;
	case "mysql":
		$ret = @mysql_free_result($result);
		break;
	case "odbc":
		$ret = @odbc_free_result($result);
		break;
	}

	return $ret;
}

function SQLError($id)
{
	GLOBAL $dbtype;
	$ret = 0;

	switch ($dbtype) {
	case "mysqli":
		$ret = $id->error;
		break;
	case "mysql":
		$ret = @mysql_error($id);
		break;
	case "odbc":
		$ret = @odbc_errormsg($id);
		break;
	}

	return $ret;
}

function SQLAffectedRows($id)
{
	GLOBAL $dbtype;
	$ret = 0;

	switch ($dbtype) {
	case "mysqli":
		$ret = $id->affected_rows;
		break;
	case "mysql":
		$ret = @mysql_affected_rows($id);
		break;
	case "odbc":
		$ret = 0;
		break;
	}

	return $ret;
}

function Login($name, $pass, $id)
{
	global $_crypt_uname, $_privlvl, $vrows, $_lmsg;
	$ret = 0;
	$result = SQLQuery("SELECT ENCRYPT(uid), password, priv_lvl, link, vrows, disable, expire FROM admin WHERE uid='$name'", $id);
	if (SQLNumRows($result)>0) {
		$row = SQLFetchRow($result);
		$_crypt_uname = $row[0];
		$_privlvl = $row[2];
		$vrows = $row[4];
		$_expires = $row[6];
		if (!$row[5]) {
		   if ($row[3]) {
			SQLFreeResult($result);
			$result = SQLQuery("SELECT uid, password, expires from user WHERE uid='$name'", $id);
			$row = SQLFetchRow($result);
			$_expires = $row[2];
		   }
		   if (strcmp($_expires,"0000-00-00 00:00:00")) {
			$now_ = strtotime("now");
			$expires_ = strtotime($_expires);
			if ($now_ <= $expires_) {
			    if (crypt($pass, $row[1]) == $row[1]) {
				$ret = $_privlvl;
		     	    } else {
				$_lmsg = "bad login";
			    }
			} else {
				$_lmsg = "expired";
			}
		   } else {
			if (crypt($pass, $row[1]) == $row[1]) {
				$ret = $_privlvl;
			} else {
				$_lmsg = "bad login";
			}
		   }
		} else {
			$_lmsg = "disable";
		}
		SQLFreeResult($result);
	}
	return $ret;
}

function checkLogin($name, $id)
{
	global $vrows;

	$ret = 0;

	$result = SQLQuery("SELECT priv_lvl,vrows FROM admin WHERE ENCRYPT(uid,'$name')='$name'", $id);
	if (SQLNumRows($result)>0) {
		$row = SQLFetchRow($result);
		$ret = $row[0];
		$vrows = $row[1];
		echo "<script language=\"JavaScript\">"
		    ."var admin_priv_lvl = ".$row[0].";"
		    ."var admin_vrows = ".$row[1].";"
		    ."</script>\n";
		SQLFreeResult($result);
	}
	return $ret;
}

function checkLoginXML($name, $id)
{
	$ret = 0;

	$result = SQLQuery("SELECT priv_lvl FROM admin WHERE ENCRYPT(uid,'$name')='$name'", $id);
	if (SQLNumRows($result)>0) {
		$row = SQLFetchRow($result);
		$ret = $row[0];
		SQLFreeResult($result);
	}
	return $ret;
}

function unixcrypt($password)
{
	return crypt($password);
}


function checkCookie($value, $value1, $id)
{
	$ret = checkLogin($_COOKIE[$value], $id);
	if ($ret<=0) {
		echo "<script language=\"JavaScript\">"
		    ." alert('You are not currently logged in.  Please login.');"
		    ." top.location.href = \"index.php\";"
		    ." </script>";
	}
	return $ret;
}

function updateOther($field, $uid, $oldpass, $newpass, $id)
{
	global $_ERROR;
	$ret = 0;

	$c_newpass = unixcrypt($newpass);
	$sqlcmd = sprintf("UPDATE user set %s='%s' WHERE uid='%s' AND ENCRYPT('%s',password)=password", $field, $c_newpass, $uid, $oldpass);

	$res = SQLQuery($sqlcmd, $id); 
	if (SQLAffectedRows($id)) {
		$ret = 1;
	}

	return $ret;
}

function updatePassword($field, $uid, $oldpass, $newpass, $expiretime, $id)
{
	global $_ERROR, $pass_complex;
	$ret = 0;

	if ($field === "password") {
		//check for old password reuse
		$result = SQLQuery("SELECT ts FROM oldpass WHERE uid='$uid' AND ENCRYPT('$newpass',password)=password",$id);
		$rows = SQLAffectedRows($id);
		if ($rows) { SQLFreeResult($result); }
	} else {
		$rows = 0;
	}
	if ($rows==0) {
		if (!empty($newpass)) {
			$c_newpass = unixcrypt($newpass);
			if ($field  === "password") {
				$_sql_expires = ", expires=DATE_ADD(CURDATE(), INTERVAL ".$pass_complex->{'expiretime'}." DAY)";
			}
			$sqlcmd = sprintf("UPDATE user set %s='%s', expires=DATE_ADD(CURDATE(), INTERVAL %d DAY), flags=0 WHERE uid='%s' AND ENCRYPT('%s',password)=password", $field, $c_newpass, $pass_complex->{'expiretime'}, $uid, $oldpass);
			$result = SQLQuery($sqlcmd, $id); 
			$ret = SQLAffectedRows($id);
			if (($ret > 0) && ($field === "password")) {
				//Insert old password into old password table
				$result2 = SQLQuery("INSERT INTO oldpass (uid, password) VALUE ('$uid','".unixcrypt($oldpass)."')", $id);
				// Delete oldest password if greater than pass_complex_repeat
				$result2 = SQLQuery("SELECT ts FROM oldpass WHERE uid='$uid' ORDER BY ts ASC", $id);
				$rows = SQLAffectedRows($id);
				if ($rows >= $pass_complex->{'repeat'}) {
					$_row = SQLFetchArray($result2);
					$_ERROR += $_row[0];
					$result3 = SQLQuery("DELETE FROM oldpass WHERE ts = '$_row[0]'", $id);
					if (SQLAffectedRows($id)) {
						$_ERROR += " Deleted old password for $uid on timestamp '$_row[0]'";
					}
					SQLFreeResult($result2);
				}
			}
		}
	} else {
		$ret = -1;
	}

	return $ret;
}

function verifyPassword($field, $uid, $password, $id)
{
	$ret = 0;

	if (!empty($password)) {
		$result = SQLQuery("SELECT uid FROM user WHERE uid='$uid' AND ENCRYPT('$password',$field)=$field", $id);
		$ret = SQLAffectedRows($id);
	}

	return $ret;
}

function Audit($service, $status, $_what, $dbi)
{
        global $_uname, $_remote;

	$_remotev6 = "";

	if (filter_var($_remote, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
		$_remotev6 = $_remote;
	}
	
	if (isset($ipv6_enable) && $ipv6_enable) {
        	$result = @SQLQuery("INSERT INTO audit (date, uid, client_ip, client_ip6, service, status, what) VALUES (NOW(), \"$_uname\", INET_ATON(\"$_remote\"), INET6_ATON(\"$_remotev6\"), \"$service\", \"$status\", \"$_what\")", $dbi);
	} else {
        	$result = @SQLQuery("INSERT INTO audit (date, uid, client_ip, service, status, what) VALUES (NOW(), \"$_uname\", INET_ATON(\"$_remote\"), \"$service\", \"$status\", \"$_what\")", $dbi);
	}

}

function navi_buttons($_func, $_table, $_rows, $_offset, $_vrows, $_index,$_search)
{       
        if ($_rows>$_vrows) {
                echo "<div class=\"navi\" id=\"navi\">";
                if ($_offset) {
                        echo "<a href=\"javascript:$_func('$_table',0,$_vrows,$_index,$_search);\" class=\"navi-item navi-round\">&#8249&#8249</a>";
                        $ppos = ($_offset-$_vrows)<0?0:($_offset-$_vrows);
                        echo "<a href=\"javascript:$_func('$_table',$ppos,$_vrows,$_index,$_search);\" class=\"navi-item navi-round\">&#8249</a>";
                }
                if (($_offset+$_vrows)<$_rows) {
                        echo "<a href=\"javascript:$_func('$_table',".($_offset+$_vrows).",$_vrows,$_index,$_search);\" class=\"navi-item navi-round\">&#8250</a>";
                }
                echo "<a href=\"javascript:$_func('$_table',".($_rows-$_vrows).",$_vrows,$_index,$_search);\" class=\"navi-item navi-round\">&#8250&#8250</a>";
                echo "</div>\n";
        }
}

// Submask6
function Submask6($sub_bit)
{
	$int_size = PHP_INT_SIZE * 8;
	$val = -1;
	$_submask = "";

	for ($i=0; $i < (128/$int_size); $i++) {
		$places = ($int_size<$sub_bit)?0:$int_size-$sub_bit;
		$res = $val << $places;

		if ($res) {
			$chunk = str_split(dechex($res), 4);
			$_submask .= implode(':', $chunk);
		} else {
			if ($int_size == 32) {
				$_submask .= "0000:0000";
			} else {
				$_submask .= "0000:0000:0000:0000";
			}
		}

		if ($i < (128/$int_size)-1) {
			$_submask .= ":";
		}
		$sub_bit -= $int_size;
	}

	return $_submask;
}

function Netmask6($subnet) {
	$mask = "";

	if ($subnet) {
		$len = PHP_INT_SIZE * 8;
		if ($subnet > $len) $subnet = $len;

		$mask = str_repeat('f', $subnet>>2);

		switch ($subnet & 3) {
			case 3: $mask .= 'e'; break;
			case 2: $mask .= 'c'; break;
			case 1: $mask .= '8'; break;
		}
		$mask = str_pad($mask, $len>>2, '0');
		$mask = pack('H*', $mask);
	}

	return $mask;
}

foreach ($_POST as $key=>$val) {
        eval("\$$key = '$val';");
}
foreach ($_GET as $key=>$val)
        eval("\$$key = '$val';");

?>
