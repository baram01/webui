<?php
/*
    Copyright (C) 2019  Young Consulting, Inc
                                                                                                                                                                 
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

$id = 0;
$vid = 0;
$fields = "*";
require_once("config.php");
require_once("mainfile.php");
include("XMLWriter.class.php");



//$dbi=OpenDatabase($dbhost, $dbuname, $dbpass, $dbname);
$dbi=OpenDatabase($db_config);

if (checkLoginXML($_COOKIE["login"],$dbi) < 5) {
	CloseDatabase($dbi);
	return;
}

$sqlcmd = "";
$where = "";
$oXML = new _XmlWriter();

switch ($table) {
case "user":
	if ($id)
		$where = "WHERE uid RLIKE '$uid.*' and user=$user";
	else {
		if ($uid)
			$where = "WHERE uid='$uid' and user=$user";
		else if ($user)
			$where = "WHERE user=$user";
	}
	break;

case "vendor":
	$where = "WHERE id='$vid'";
	break;
case "host":
	$where = "WHERE ip='$ip' and host=$host";
	break;
case "acl":
	$where = "WHERE type=$type AND id=$id AND seq=$seq";
	break;
case "attribute":
	if (isset($name))
		$where = "WHERE name RLIKE '$name.*'";
	else if ($id)
		$where = "WHERE id=$id AND vid=$vid AND auth=$auth";
	else {
		$fields = "name";
		$where = "WHERE vid=$vid AND auth=$auth";
	}
	break;
case "command":
	if (isset($name))
		$where = "WHERE name RLIKE '$name.*'";
	else if ($id)
		$where = "WHERE id=$id AND vid=$vid";
	else {
		$fields = "name";
		if ($vid)
			$where = "WHERE vid=$vid or vid=0";
		else
			$where = "WHERE vid=0";
	}
	break;
case "vcomponent":
	if ($id) {
		$where = "WHERE id=$id";
	}

	if ($vid) {
		$where = "WHERE vid=$vid AND component=$component";
	}
	break;
case "node":
	break;
}


$result = @SQLQuery("SELECT $fields FROM $table $where", $dbi);
$numrows = @SQLNumRows($result);
if ($numrows > 0) {
	$oXML->push($table);
	while ($row=SQLFetchArray($result)) {
		$oXML->push("row");
		foreach ($row as $key => $value) {
			if (!is_numeric($key))
				$oXML->element("$key",$value);
		}
		$oXML->pop();
	}
	SQLFreeResult($result);
	$oXML->pop();
}

CloseDatabase($dbi);

if ($numrows > 0) {
	header("Content-Type: text/xml");
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Mon, 01 Jan 1990 00:00:00 GMT");
	echo $oXML->getXml();
}
?>
