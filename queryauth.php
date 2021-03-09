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

/*
if (checkLoginXML($_COOKIE["login"],$dbi) < 1) {
	CloseDatabase($dbi);
	return;
}
*/

$params = "*";
$sqlcmd = "";
$where = "";

switch ($fnc) {
case "auth":
	$params = "SELECT uid";
        $where = "WHERE uid='$uid' AND ENCRYPT('$password', $field)=$field";
        break;

case "change":
	$where = "WHERE id='$vid'";
	break;
}

$result = @SQLQuery("$params FROM user $where", $dbi);
$numrows = @SQLNumRows($result);
if ($numrows > 0) {
	$_i = 0;
	$_json = array();

	$_json[0]["pass"]=1;
/*
	while ($row=SQLFetchAssoc($result)) {
		foreach ($row as $key => $value) {
			$_json[$_i][$key] = $value;
		}
		$_i++;
	}
	SQLFreeResult($result);
*/
} else {
	$_json = array();
	$_json[0]["pass"]=0;

}

$output =  json_encode($_json);

if (isset($_GET["callback"])) {
	$output = htmlspecialchars($_GET["callback"])."($output);";
}
echo $output;

CloseDatabase($dbi);
?>
