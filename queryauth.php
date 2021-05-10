<?php
/*
    Copyright (C) 2021  Young Consulting, Inc
                                                                                                                                                                 
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

require_once("config.php");
require_once("mainfile.php");

$dbi=OpenDatabase($db_config);

if (($fnc != "auth") & (checkLoginXML($_COOKIE["login"],$dbi) < 1)) {
	CloseDatabase($dbi);
	exit(1);
}

$sql = "";
$params = "*";
$where = "";
$table = "admin";
$msg = "";

switch ($fnc) {
case "auth":
	$sql = "SELECT";
	$params = "uid,priv_lvl,vrows,link,disable,expire";
        $where = "WHERE uid='$uid' AND ENCRYPT('$password', password)=password";
        break;

case "change":
	if (isset($password) && isset($oldpass)) {
	   $result = @SQLQuery("SELECT link FROM admin WHERE uid='$uid' and ENCRYPT('$oldpass', password)=password", $dbi);
	   if (@SQLNumRows($result) > 0) {
		$row = @SQLFetchRow($result);
		if ($row[0]==0) {
			$result1 = @SQLQuery("UPDATE admin set password='".unixcrypt($password)."' WHERE uid='$uid'", $dbi);
			if (@SQLAffectedRows($dbi)>0) {
				$sql = "SELECT";
				$params = "priv_lvl";
				$where = "WHERE uid='$uid'";
			}
		} else {
			$msg = "Account linked";
		}
	   } else {
		$msg = "Old password not valid";
	   }
	}
	break;

case "verify":
	$sql = "SELECT";
	$params = "priv_lvl, disable";
	$where = "WHERE ENCRYPT(uid,'$uid')='$uid'";
	break;

case "chgvrows":
        $result = @SQLQuery("SELECT vrows FROM admin WHERE uid='$uid'", $dbi);
        if (@SQLNumRows($result) > 0) {
                $row = @SQLFetchRow($result); 
                if ($row[0]!=$_vrows) {
                        $result1 = @SQLQuery("UPDATE admin set vrows=$_vrows WHERE uid='$uid'", $dbi);
                        if (@SQLAffectedRows($dbi)>0) {
                                $sql = "SELECT";
                                $params = "vrows";
                                $where = "WHERE uid='$uid'";
                        }
                }
        }
        break;
}

$result = @SQLQuery("$sql $params FROM $table $where", $dbi);
$numrows = @SQLNumRows($result);
if ($numrows > 0) {
	$_i = 0;
	$_json = array();

	$_json[0]["pass"]=1;
	while ($row=SQLFetchAssoc($result)) {
		foreach ($row as $key => $value) {
			$_json[$_i][$key] = $value;
		}
		$_i++;
	}
	SQLFreeResult($result);
} else {
	$_json = array();
	$_json[0]["pass"]=0;
	$_json[0]["message"]=$msg;
}

$output =  json_encode($_json);

if (isset($_GET["callback"])) {
	$output = htmlspecialchars($_GET["callback"])."($output);";
}
echo $output;

CloseDatabase($dbi);
?>
