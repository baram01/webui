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

$fields = "*";
require_once("config.php");
require_once("mainfile.php");

//$dbi=OpenDatabase($dbhost, $dbuname, $dbpass, $dbname);
$dbi=OpenDatabase($db_config);

if (checkLoginXML($_COOKIE["login"],$dbi) < 5) {
	CloseDatabase($dbi);
	return;
}

$sqlcmd = "";
$where = "";

switch ($table) {
case "user":
	$fields = "id, uid";
        if (isset($term)) {
                $where = "WHERE uid RLIKE '".$term.".*' AND user=$user";
        } else {
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
	$fields = "id, name, type, auth";
	if (isset($term)) {
		$where = "WHERE name RLIKE '".$term.".*'";
		if (isset($vid)) {
			if ($vid) {
				$where .= " AND (vid=$vid OR vid=0)";
			} else {
				$where .= " AND vid=0";
			}
		}
		if (isset($auth)) {
			if ($auth) {
				$where .= " AND auth=$auth";
			}
		}
	} else {
		$where = "WHERE vid=$vid";
	}
	break;

case "command":
	$fields = "id, name";
	if (isset($term)) {
		$where = "WHERE name RLIKE '".$term.".*'";
		if (isset($vid)) {
			if ($vid) {
				$where .= " AND (vid=$vid OR vid=0)";
			} else {
				$where .= " AND vid=0";
			}
		}
	} else {
		if ($vid)
			$where = "WHERE vid=$vid or vid=0";
		else
			$where = "WHERE vid=0";
	}
	break;

case "node":
	break;

case "admin":
	$fields = "uid";
        if (isset($term)) {
                $where = "WHERE uid RLIKE '".$term.".*'";
        } else {
                $where = "WHERE user=$user";
        }
        break;

case "attr_value":
	$fields = "value, option";
	if (isset($term)) {
		if (is_numeric($term)) {
			$where = "WHERE value = ".$term;
		} else {
			$where = "WHERE option RLIKE '".$term.".*'";
		}
		if (isset($vid)) {
			if ($vid) {
				$where .= " AND (vid=$vid OR vid=0)";
			} else {
				$where .= " AND vid=0";
			}
		}
		if (isset($attrid)) {
			if ($attrid) {
				$where .= " AND attrid=$attrid";
			}
		}
	} else {
		$where = "WHERE vid=$vid AND attrid=$attrid";
	}
	$where .= " ORDER BY value ASC";
	break;

}

$result = @SQLQuery("SELECT $fields FROM $table $where", $dbi);
$numrows = @SQLNumRows($result);
if ($numrows > 0) {
	$_i = 0;
	$_json = array();
	while ($row=SQLFetchAssoc($result)) {
		foreach ($row as $key => $value) {
			$_json[$_i][$key] = $value;
		}
//		array_push($_json, array("id"=>$row[0], "label"=>$row[1], "value"=>$row[1], "type"=>$row[2]));
		$_i++;
	}
	SQLFreeResult($result);
	$output =  json_encode($_json);

	if (isset($_GET["callback"])) {
		$output = htmlspecialchars($_GET["callback"])."($output);";
	}

	echo $output;
} else {
	$output = "";

	if (isset($_GET["callback"])) {
		$output = htmlspecialchars($_GET["callback"])."($output);";
	}
	echo $output;
}
CloseDatabase($dbi);
?>
