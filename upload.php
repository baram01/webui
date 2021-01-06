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

require_once("config.php");
require_once("mainfile.php");

$dbi = OpenDatabase3($db_config, $_index);

if (checkLoginXML($_COOKIE["login"], $dbi) < 15) {
	echo "<script language=\"JavaScript\"> top.location.href=\"index.php?module=main\"; </script>";
} else {

$filename = $_FILES['file']['name'];
$location = "tmp/".$filename;

switch ($_option) {
case 1: //Process vendor file
	if (move_uploaded_file($_FILES['file']['tmp_name'], $location)) {
		$json_attr_format_file = $target_dir."attr_format.json";
		$attr_format = json_decode(file_get_contents($json_attr_format_file), true);
		$vendor = 0;
		$nv = 1;
		$attr = array();

		$handle = fopen($location,"r");
		if ($handle) {
		    while (!feof($handle) && $nv) {
		        $sqlcmd = "";
		        $buffer = fgets($handle);
		        if (strpos($buffer, 'VENDOR') !== false) {
		                $keywords = preg_split("/[\s]+/", $buffer);
		                if ($keywords[0] == 'VENDOR') {
		                        $vendor = $keywords[2];
		                        $sqlcmd = "INSERT INTO vendor (id, name) VALUES (".$keywords[2].",'".$keywords[1]."')";
		                }
		        }
		        if (strpos($buffer, 'ATTRIBUTE') !== false) {
				if (!$vendor) { $nv = 0; }
		                $keywords = preg_split("/[\s]+/", $buffer);
		                $attr[$keywords[1]] = $keywords[2];
		                $sqlcmd = "INSERT INTO attribute (id, name, descr, type, auth, vid) VALUES (".$keywords[2].",'".$keywords[1]."','".$keywords[1]."',".array_search($keywords[3],$attr_format).",2,$vendor)";
		        }
		        if (strpos($buffer, 'VALUE') !== false) {
		                $keywords = preg_split("/[\s]+/", $buffer);
				SQLQuery("UPDATE attribute SET has_value=1 where id = ".$attr[$keywords[1]], $dbi);
				$sqlcmd = "INSERT INTO attr_value (attrid, vid, option, value) VALUES (".$attr[$keywords[1]].",".$vendor.",'".$keywords[2]."',".$keywords[3].")";
		        }
			if ($sqlcmd) {
				SQLQuery($sqlcmd, $dbi);
				if ($debug) {
					$_ERROR = SQLError($dbi).$sqlcmd;
				}
			}
		    }
		    fclose($handle);
		    unlink($location);

		    if ($nv) {
			echo "{\"status\":0, \"message\":\"File processed\"}";
		    } else {
			echo "{\"status\":3, \"message\":\"Not dictionary file\"}";
		    }
		} else {
		    echo "{\"status\":1, \"message\":\"Issue with file\"}";
		}
	} else {
		echo "{\"status\":2, \"message\":\"File not processed\"}";
	}
	break;

case 2: //Restore configuration
	if (move_uploaded_file($_FILES['file']['tmp_name'], $location)) {
		exec('tar tzf '.$location, $output);
		echo "<font color=";
		if (strpos($output[0], 'cust') !== false) {
			exec('tar czf tmp/old_cust_bck.tgz cust');
			exec('tar xzf '.$location);
			echo "\"green\">Restored";
		} else {
			echo "\"red\">Not backup file";
		}
		echo "</font>";
		unlink($location);
	} else {
		echo 0;
	}
	break;

default:
	echo 0;
}

}
?>
