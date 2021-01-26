<?php
/*
    Copyright (C) 2020  Young Consulting, Inc
                                                                                                                                                                 
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

if (isset($_index)) {
	$dbi=OpenDatabase3($db_config, $_index);
} else {
	$_index = 0;
	$dbi=OpenDatabase3($db_config, 0);
}


$_ret = checkLoginXML($_COOKIE["login"],$dbi);
if (!$_ret) {
        echo "<script language=\"JavaScript\"> top.location.href=\"index.php?module=main\"; </script>";
}

$top = 200;
$_lastID = 0;

$select = "";
$where = "";
$where2 = "";
$table = $_table;

switch ($_table) {
case "user":
	$_lastID = $start_uid;
	$select = "disable, id, uid, gid, comment, expires, acl_id, flags, auth";
	$where = "WHERE user = 1";
	if (isset($_uid) && $_uid) {
		$where .= " AND uid = ".$_uid;
	}
	$where2 = "ORDER BY id";
	if (isset($group) && $group) $where .= " AND gid='$group'";
	break;

case "usergroup":
	$_lastID = $start_gid;
	$select = "disable, id, uid, comment, expires, acl_id";
	$table = "user";
	$where = "WHERE user = 2";
	$where2 = "ORDER BY id";
	break;

case "host":
	$select = "ip, name, hostgroup, vendor, prompt, loginacl, enableacl";
	$where = "WHERE host = 1";
	$where2 = "ORDER BY ip ASC";
	if (isset($hostgroup) && $hostgroup) $where .= " AND hostgroup='$hostgroup'";
	break;

case "hostgroup":
	$select = "ip, vendor, prompt, loginacl, enableacl";
	$table = "host";
	$where = "WHERE host = 2";
	break;

case "profile":
	$_lastID = $start_pid;
	$select ="id, uid";
//	$table = "user";
//	$where = "WHERE user = 3";
	$where2 = "ORDER BY id";
	break;

case "nas_acl":
	$select = "id, seq, permission, value, value1";
	$table = "acl";
	$where = "WHERE type = 2";
	$where2 = "ORDER BY id, seq";
	break;

case "user_acl":
	$select = "id, seq, permission, value";
	$table = "acl";
	$where = "WHERE type = 1 AND seq!=9999";
	$where2 = "ORDER BY id, seq";
	break;

case "attribute":
	$select ="id, name, descr, type, auth, vid, has_value";
	$where2 = "ORDER BY vid";
	break;

case "command":
	$select ="id, name, descr, auth, vid";
	$where2 = "ORDER BY id";
	break;

case "vendor":
	$select = "id, name, contract, tsphone";
	$where2 = "ORDER BY id";
	break;

case "attr_value":
	$select = "option, value";
	$where = "WHERE attrid=".$attrid." AND vid =".$vid;
	$where2 = "ORDER BY value";
	break;

case "config":
	$select = "*";
	$where = "";
	$where2 = "";
	break;

case "admin":
	$select = "uid, comment, priv_lvl, link, vrows, disable, expire";
	$where = "";
	$where2 = "";
	break;
}

if ($vrows) {
	$where2 .= " LIMIT ".$vrows;
	if (isset($offset) && $offset) {
		$where2 .= " OFFSET ".$offset;
	} else {
		$offset = 0;
	}
}

if ($select) {
	$result = @SQLQuery("SELECT SQL_CALC_FOUND_ROWS $select FROM $table $where $where2", $dbi);
} else {
	$result = @SQLQuery("SELECT SQL_CALC_FOUND_ROWS * FROM $table $where $where2", $dbi);
}
$_ERROR = @SQLError($dbi);

if (@SQLNumRows($result) > 0) {
	$result2 = @SQLQuery("SELECT FOUND_ROWS()", $dbi);
	$_r = SQLFetchRow($result2);
	echo $_r[0]." rows found. ";
	if (@SQLNumRows($result)==$vrows) { echo $vrows; }
	else { echo @SQLNumRows($result); }
	echo " shown.\n";

	navi_buttons("_Result",$_table,$_r[0],$offset,$vrows,$_index);
	SQLFreeResult($result2);

//	echo "<table border=1 cellspacing=1 cellpadding=2 class=\"_table2\">\n";
	echo "<table border=1 cellspacing=1 cellpadding=2 class=\"reports\">\n";
	switch ($_table) {
	   case "user":
		echo "<tr><th>ID</th><th>User</th><th>Group</th><th>Comment</th><th>Expires</th><th>ACL</th></tr>\n";

//	   $result3 = @SQLQuery("SELECT MAX(id) FROM user WHERE user = 1", $dbi);
	   $result3 = @SQLQuery("SELECT MIN(id) AS nextid FROM user a WHERE NOT EXISTS (SELECT id FROM user b WHERE b.id=a.id+1 AND user=1) AND user=1", $dbi);
	   if (SQLNumRows($result3)) {
	   	$row3 = @SQLFetchArray($result3);
		$_lastID = $row3[0];
	   }
	   SQLFreeResult($result3);

	   while ($row=SQLFetchAssoc($result)) {
        	$style = "";
        	$icon = array("");
        	$acl = $row["acl_id"]?$row["acl_id"]:"&nbsp;";

		if ($row["auth"] == 3) {
			$icon = array("ldap.png", "LDAP authentication");
		}
        	if ($row["disable"]) $style="style=\"color:red\"";
        	else if ($row["flags"] & 2) {
                	$style="style=\"color:red\"";
                	$icon = array("change_pass.png","Change Password");
        	} else if ($row["flags"] & 8) {
                	$style="style=\"color:black\"";
                	$icon=array("lock_pass.png","Locked Password");
        	} else {
                	if (strcmp($row["expires"],"0000-00-00 00:00:00")) {
                        	$_now = strtotime("now");
                        	$_expires = strtotime($row["expires"]);

                        	if ($_now > $_expires) {
                                	$style="style=\"color:red\"";
                                	$icon = array("expired_pass.png","Expired Password");
                        	} else if ((($_expires - $_now) <= $pass_complex->{'changetime'}*24*60*60)) {
                                	$style="style=\"color:orange\"";
                        	}
                	}
        	}
        	if ($_ret > 9) {
                	echo "<tr><td $style><a href=\"javascript:_modify('".$row["id"]."','".$row["uid"]."','1')\" title=\"Modify User\">".$row["id"]."</a></td>";
        	} else {
                	echo "<tr><td $style>".$row["id"]."</td>";
        	}
        	echo "<td width=90 $style>";
        	if ($icon[0]) echo "<image src=\"images/".$icon[0]."\" style=\"width:15px;height:15px;\" title=\"".$icon[1]."\"></image> ";
        	echo substr($row["uid"],0,20)."</td>"
        	    ."<td width=90 $style>".$row["gid"]."</td>"
        	    ."<td width=190 $style>".$row["comment"]."</td>"
        	    ."<td $style>".$row["expires"]."</td>"
        	    ."<td $style>".$acl."</td>";
        	if ($_ret > 9) {
                    echo "<td><a href=\"javascript:_openCommand('".$row["id"]."','".$row["uid"]."','".$top."px')\" title=\"Add/Modify Commands\"><img src=\"images/command.gif\" width=25 border=0></a>"."</td>"
                        ."<td><a href=\"javascript:_openService('".$row["id"]."','".$row["uid"]."','".$top."px')\" title=\"Add/Modify Services\"><img src=\"images/service.gif\" width=25 border=0></a>"."</td>"
                        ."<td><a href=\"Javascript:_open_contact1('".$row["uid"]."')\"><img width=25 src=\"images/identity.gif\" border=0></img></a></td>"
                        ."<td><a href=\"javascript:_delete('".$row["uid"]."');\" title=\"Delete\"><img src=\"images/trash.gif\" width=25 border=0></img></a></td></tr>\n";
        	}
        //	$top+=20;
	   }
	   break;

	   case "usergroup":
		echo "<tr><th>ID</th><th>Group</th><th>Comment</th><th>Expires</th><th>ACL</th></tr>\n";

//	   $result3 = @SQLQuery("SELECT MAX(id) FROM user WHERE user = 2", $dbi);
	   $result3 = @SQLQuery("SELECT MIN(id) AS nextid FROM user a WHERE NOT EXISTS (SELECT id FROM user b WHERE b.id=a.id+1 AND user=2) AND user=2", $dbi);

	   if (SQLNumRows($result3)) {
	   	$row3 = @SQLFetchArray($result3);
		$_lastID = $row3[0];
	   }
	   SQLFreeResult($result3);

	   while ($row = @SQLFetchAssoc($result)) {
           $style = "";
           $acl = $row["acl_id"]?$row["acl_id"]:"&nbsp;";

           if ($row["disable"]) $style="style=\"color:red\"";
           else {
                if (strcmp($row["expires"],"0000-00-00 00:00:00")) {
                        $_now = strtotime("now");
                        $_expires = strtotime($row["expires"]);

                        if ($_now > $_expires) {
                                $style="style=\"color:red\"";
                        } else if ((($_expires - $_now) <= $pass_complex->{'changetime'}*24*60*60)) {
                                $style="style=\"color:orange\"";
                        }
                }
           }
           if ($_ret > 9) {
                echo "<tr><td $style><a href=\"javascript:_modify('".$row["id"]."','".$row["uid"]."','2')\" title=\"Modify user\">".$row["id"]."</a></td>";
           } else {
                echo "<tr><td $style>".$row["id"]."</td>";
           }
           echo "<td width=90 $style>".substr($row["uid"],0,20)."</td>"
            ."<td width=190 $style>".$row["comment"]."</td>"
            ."<td $style>".$row["expires"]."</td>"
            ."<td $style>".$acl."</td>";
           if ($_ret > 9) {
                echo "<td><a href=\"javascript:_openCommand('".$row["id"]."','".$row["uid"]."','".$top."px')\" title=\"Add/Modify Commands\"><img src=\"images/command.gif\" width=25 border=0></a>"."</td>"
                    ."<td><a href=\"javascript:_openService('".$row["id"]."','".$row["uid"]."','".$top."px')\" title=\"Add/Modify Services\"><img src=\"images/service.gif\" width=25 border=0></a>"."</td>"
                    ."<td><a href=\"javascript:_group('".$row["id"]."','".$row["uid"]."')\" title=\"Users in group\"><img src=\"images/users.gif\" width=30 border=0></a>"."</td>"
                    ."<td><a href=\"javascript:_delete('".$row["uid"]."');\" title=\"Delete user\"><img src=\"images/trash.gif\" width=25 border=0></img></a></td></tr>\n";
        }
        // $top+=20;
	   }
	   break;

	   case "host":
		$vnd_array = array();
		$result3 = @SQLQuery("SELECT id, name FROM vendor ORDER BY name", $dbi);
		while ($row3 = @SQLFetchRow($result3)) {
		        $vnd_array[$row3[0]]=$row3[1];
		}
		@SQLFreeResult($result3);

		echo "<tr><th>IP</th><th>Name</th><th>Group</th><th>Vendor</th><th>Prompt</th><th>Login<br>Policy</th><th>Enable<br>Policy</th></tr>\n";

	   while ($row=SQLFetchArray($result)) {
        	$lacl = $row["loginacl"]?$row["loginacl"]:"&nbsp;";
        	$eacl = $row["enableacl"]?$row["enableacl"]:"&nbsp;";
        	$prmt = $row["prompt"]?$row["prompt"]:"&nbsp;";
        	if ($_ret > 9 ) {
          	echo "<tr><td width=80><a href=\"javascript:_modify('".$row["ip"]."')\" title=\"Modify NAS\">".$row["ip"]."</a></td>"
            ."<td>".$row["name"]."</td>"
            ."<td width=80>".$row["hostgroup"]."</td>"
            ."<td width=80>".$vnd_array[$row["vendor"]]."</td>"
            ."<td width=230>".$prmt."</td>"
            ."<td width=45><center>".$lacl."</center></td>"
            ."<td width=45><center>".$eacl."</center></td>"
            ."<td><a href=\"javascript:_delete('".$row["ip"]."')\" title=\"Delete NAS\"><img src=\"images/trash.gif\" width=25 border=0></img></a></td></tr>\n";
        	} else {
          	echo "<tr><td width=80>".$row["ip"]."</td>"
            ."<td width=80>".$row["hostgroup"]."</td>"
            ."<td width=80>".$vnd_array[$row["vendor"]]."</td>"
            ."<td width=190>".$prmt."</td>"
            ."<td width=45><center>".$lacl."</center></td>"
            ."<td width=45><center>".$eacl."</center></td>";
        	}
	   }
	   break;

	   case "hostgroup":
                $vnd_array = array();
                $result3 = @SQLQuery("SELECT id, name FROM vendor ORDER BY name", $dbi);
                while ($row3 = @SQLFetchRow($result3)) {
                        $vnd_array[$row3[0]]=$row3[1];
                }
                @SQLFreeResult($result3);

		echo "<tr><th>Group</th><th>Vendor</th><th>Prompt</th><th>Login<br>Policy</th><th>Enable<br>Policy</th></tr>\n";

	   
	  	while ($row=SQLFetchArray($result)) {
        	$lacl = $row["loginacl"]?$row["loginacl"]:"&nbsp;";
        	$eacl = $row["enableacl"]?$row["enableacl"]:"&nbsp;";
        	if ($_ret > 9) {
       		   echo "<tr><td width=80><a href=\"javascript:_modify('".$row["ip"]."')\" title=\"Modify group\">".$row["ip"]."</a></td>"
       		     ."<td width=80>".$vnd_array[$row["vendor"]]."</td>"
       		     ."<td width=300>".$row["prompt"]."</td>"
       		     ."<td width=45><center>".$lacl."</center></td>"
       		     ."<td width=45><center>".$eacl."</center></td>"
       		     ."<td><a href=\"javascript:_group('".$row["ip"]."')\" title=\"Nas part of group\"><img src=\"images/nasgroup.gif\" width=25 border=0></img></a></td>"
       		     ."<td><a href=\"javascript:_delete('".$row["ip"]."')\" title=\"Delete group\"><img src=\"images/trash.gif\" width=25 border=0></img></a></td></tr>\n";
       		 } else {
       		   echo "<tr><td width=80>".$row["ip"]."</td>"
       		     ."<td width=80>".$vnd_array[$row["vendor"]]."</td>"
       		     ."<td width=190>".$row["prompt"]."</td>"
       		     ."<td width=45><center>".$lacl."</center></td>"
       		     ."<td width=45><center>".$eacl."</center></td>";
       		 }
	   }
	   break;

	   case "profile":
		echo "<tr><th width=50>ID</th><th width=100>Name</th></tr>\n";

//	   $result3 = @SQLQuery("SELECT MAX(id) FROM profile", $dbi);
	   $result3 = @SQLQuery("SELECT MIN(id) AS nextid FROM profile a WHERE NOT EXISTS (SELECT id FROM profile b WHERE b.id=a.id+1)", $dbi);

	   if (SQLNumRows($result3)) {
	   	$row3 = @SQLFetchArray($result3);
		$_lastID = $row3[0];
	   }
	   SQLFreeResult($result3);

		while ($row = @SQLFetchArray($result)) {
		if ($_ret > 9) {
		//        if ($_lastID < intval($row["id"])) $_lastID = intval($row["id"]);
	        echo "<tr><td width=50>".$row["id"]
	            ."<td width=300>".$row["uid"]
		//    ."<td><img src=\"images/clone.png\" width=23 border=0>"
	            ."<td width=15><a href=\"javascript:_openCommand('".$row["id"]."','".$row["uid"]."','".$top."px')\" title=\"Add/Modify Commands\"><img src=\"images/command.gif\" width=25 border=0></a>"
	            ."<td width=15><a href=\"javascript:_openService('".$row["id"]."','".$row["uid"]."','".$top."px')\" title=\"Add/Modify Services\"><img src=\"images/service.gif\" width=25 border=0></a>"
	            ."<td width=15><a href=\"javascript:_delete('".$row["id"]."','".$row["uid"]."');\" title=\"Delete Profiles\"><img src=\"images/trash.gif\" width=25 border=0></img></a>\n";
	    //    $top+=20;
	    } else {
	        echo "<tr><td width=50>".$row["id"]
	            ."<td width=100>".$row["uid"];
	    }
	   }
	   break;

	   case "nas_acl":
		$curid = 0;
		$perm_type = array(57=>"permit", "deny");
		$profile = array(0=>'&nbsp;');

		$result3 = @SQLQuery("SELECT id, uid from profile", $dbi);
		while ($row3 = @SQLFetchArray($result3)) {
		        $profile[$row3[0]] = $row3[1];
		}
		@SQLFreeResult($result3);

		echo "<tr><th>ID</th><th>Sequence</th><th>Permission</th><th>User/Group</th><th>Profile</th></tr>\n";

		while ($row = @SQLFetchArray($result)) {
		   if ($row["id"]) {
			if ($row["id"] != $curid) {
				$result4 = @SQLQuery("SELECT id FROM acl WHERE type=2 AND id=".$row["id"], $dbi);
				$_num = @SQLNumRows($result4);
				@SQLFreeResult($result4);

				echo "<tr><td rowspan='$_num'>".$row["id"];
				if ($_ret > 9) echo "<a href=\"javascript:_add_acl('_acladd',".$row["id"].")\"><img src=\"images\plus-new.gif\" border=\"0\" /></a>";
				echo "</td>";
				$curid = $row["id"];
			} else {
				echo "<tr>";
			}
			if ($_ret > 9) {
				echo "<td><a href=\"javascript:_modify('".$row["id"]."','".$row["seq"]."')\" title=\"Modify ACL and sequence\">".$row["seq"]."</a></td>"
	                           ."<td>".$perm_type[$row["permission"]]."</td>"
	                           ."<td>".$row["value"]."</td>"
	                           ."<td>".$profile[$row["value1"]]."</td>"
	                           ."<td><a href=\"javascript:_delete('".$row["id"]."','".$row["seq"]."')\" title=\"Delete\"><img src=\"images/trash.gif\" width=25 border=0></img></a></td>\n";
	                } else {
		                echo "<td>".$row["seq"]."</td>"
		                    ."<td>".$perm_type[$row["permission"]]."</td>"
		                    ."<td>".$row["value"]."</td>"
		                    ."<td>".$profile[$row["value1"]]."</td>\n";
		        }
	        }
	      }
	   break;

	   case "user_acl":
		$curid = 0;
		$perm_type = array(57=>"permit", "deny");

		echo "<tr><th>ID</th><th>Sequence</th><th>Permission</th><th>IP Address/mask</th></tr>\n";

		while ($row = @SQLFetchArray($result)) {
			if ($row["id"]) {
				if ($row["id"] != $curid) {
					$result4 = @SQLQuery("SELECT id FROM acl WHERE type=1 AND id=".$row["id"], $dbi);
					$_num = @SQLNumRows($result4)-1;
					@SQLFreeResult($result4);

					echo "<tr><td rowspan='$_num'>".$row["id"];
					if ($_ret > 9) echo "<a href=\"javascript:_add_acl('_acladd',".$row["id"].")\"><img src=\"images\plus-new.gif\" border=\"0\" /></a>";
					echo "</td>";
				//	echo "<tr><td rowspan='$_num'>".$row["id"]."</td>";
					$curid = $row["id"];
				} else {
					echo "<tr>";
				}
				if ($_ret > 9) {
					echo "<td><a href=\"javascript:_modify('".$row["id"]."','".$row["seq"]."')\" title=\"Modify ACL\">".$row["seq"]."</a></td>"
					    ."<td>".$perm_type[$row["permission"]]."</td>"
					    ."<td>".$row["value"]."</td>"
					    ."<td><a href=\"javascript:_delete('".$row["id"]."','".$row["seq"]."')\" title=\"Delete\"><img src=\"images/trash.gif\" width=25 border=0></img></a></td></tr>\n";
				} else {
					echo "<td>".$row["seq"]."</td>"
					    ."<td>".$perm_type[$row["permission"]]."</td>"
					    ."<td>".$row["value"]."</td></tr>\n";
				}
			}
		}
	   break;

	   case "attribute":
		echo "<tr><th>ID</th><th>Attribute</th><th>Description</th><th>Format</th><th>Authen</th><th>Vendor</th></tr>\n";

	   $json_attr_format_file =  $target_dir."attr_format.json";
	   $attr_format = json_decode(file_get_contents($json_attr_format_file));
	   $attr_auth = array("all","tacacs","radius");

	   $result3 = @SQLQuery("SELECT id, name FROM vendor ORDER BY name", $dbi);
	   while ($row3 = @SQLFetchArray($result3)) {
	       $vendor[$row3[0]]=$row3[0]?$row3[1]:"All";
	   }
	   SQLFreeResult($result3);

	   $result3 = @SQLQuery("SELECT MAX(id) FROM attribute", $dbi);
	   if (SQLNumRows($result3)) {
	   	$row3 = @SQLFetchArray($result3);
		$_lastID = $row3[0];
	   }
	   SQLFreeResult($result3);

	   $_i = 0;
	   while ($row = @SQLFetchAssoc($result)) {
           if ($row["id"]) {
            $_attr_t = $row["type"];
            if ($_ret > 9) {
	      if ($row["has_value"]) {
              	echo "<tr id=\"_showrow_color$_i\">"; } else {
              	echo "<tr>";
	      }
	      echo "<td><a href=\"javascript:_modify('".$row["id"]."','".$row["vid"]."','".$row["auth"]."')\">".$row["id"]."</a></td>";
	      if ($row["has_value"]) {
                echo "<td><a href=\"javascript:_SearchValue(".$row["id"].",".$row["vid"].",$vrows,$_i);\">".$row["name"]."</a></td>";
	      } else {
                echo "<td>".$row["name"]."</td>";
	      }
              echo  "<td>".$row["descr"]."</td>"
                ."<td>".$attr_format->{"$_attr_t"}."</td>"
                ."<td>".$attr_auth[$row["auth"]]."</td>"
                ."<td>".$vendor[$row["vid"]]."</td>";
              echo "<td><a href=\"javascript:_delete('".$row["id"]."','".$row["name"]."','".$row["vid"]."','".$row["auth"]."','".$vendor[$row["vid"]]."')\"><img src=\"images/trash.gif\" width=25 border=0></img></a></td></tr>\n";
	      if ($row["has_value"]) {
	      	echo "<tr id=\"_showrow".$_i."\" style=\"display:none;\"><td colspan=\"7\"><div id=\"_showrow_data".$_i++."\"></div></td></tr>\n";
	      }
            } else {
              echo "<tr><td>".$row["id"]."</td>"
                ."<td width=\"100\">".$row["name"]."</td>"
                ."<td width=\"180\">".$row["descr"]."</td>"
                ."<td>".$attr_format->{"$_attr_t"}."</td>"
                ."<td>".$attr_auth[$row["auth"]]."</td>"
                ."<td>".$vendor[$row["vid"]]."</td></tr>\n";
            }
           }
	   }

	   break;

	   case "command":
		echo "<tr><th>ID</th><th width=\"100\">Command</th><th width=\"180\">Description</th><th>Authen</th><th>Vendor</th></tr>\n";

	   $json_attr_format_file =  $target_dir."attr_format.json";
	   $attr_format = json_decode(file_get_contents($json_attr_format_file));
	   $attr_auth = array("all","tacacs","radius");

	   $result3 = @SQLQuery("SELECT id, name FROM vendor ORDER BY name", $dbi);
	   while ($row3 = @SQLFetchArray($result3)) {
	       $vendor[$row3[0]]=$row3[0]?$row3[1]:"All";
	   }
	   SQLFreeResult($result3);

	   $result3 = @SQLQuery("SELECT MAX(id) FROM command", $dbi);
	   if (SQLNumRows($result3)) {
	   	$row3 = @SQLFetchArray($result3);
		$_lastID = $row3[0];
	   }
	   SQLFreeResult($result3);

	   while ($row = @SQLFetchAssoc($result)) {
            if ($row["id"]) {

            if ($_ret > 9) {
             echo "<tr><td><a href=\"javascript:_modify('".$row["id"]."','".$row["vid"]."')\">".$row["id"]."</a></td>"
                ."<td width=\"100\">".$row["name"]."</td>"
                ."<td width=\"180\">".$row["descr"]."</td>"
                ."<td align=\"center\">".$attr_auth[$row["auth"]]."</td>"
                ."<td>".$vendor[$row["vid"]]."</td>"
                ."<td><a href=\"javascript:_delete('".$row["id"]."','".$row["name"]."','".$row["vid"]."','".$vendor[$row["vid"]]."')\"><img src=\"images/trash.gif\" width=25 border=0></img></a></td></tr>\n";
          } else {
             echo "<tr><td>".$row["id"]."</td>"
                ."<td width=\"100\">".$row["name"]."</td>"
                ."<td width=\"180\">".$row["descr"]."</td>"
                ."<td align=\"center\">".$attr_auth[$row["auth"]]."</td>"
                ."<td>".$vendor[$row["vid"]]."</td></tr>\n";
          }
        }
	   }
	   break;

           case "vendor":
                echo "<tr><th>ID</th><th>Name</th><th>Contract#</th><th>Tech Support</th></tr>\n";

	   while ($row=SQLFetchAssoc($result)) {
           if ($row["id"]) {
            if ($_ret > 9) {
              echo "<tr><td><a href=\"javascript:_modify('".$row["id"]."')\" title=\"Modify Vendor\">".$row["id"]."</a></td>"
                ."<td>".$row["name"]."</td>"
                ."<td>".$row["contract"]."</td>"
                ."<td>".$row["tsphone"]."</td>"
                ."<td><a href=\"javascript:_delete('".$row["id"]."')\"><img src=\"images/trash.gif\" width=25 border=0></img></a></td></tr>\n";
            } else {
              echo "<tr><td>".$row["id"]."</td>"
                ."<td>".$row["name"]."</td>"
                ."<td>".$row["contract"]."</td>"
                ."<td>".$row["tsphone"]."</td>"
                ."<td><center>".$row["start"]."</center></td>"
                ."<td><center>".$row["end"]."</center></td></tr>\n";
            }
          }
	 }
         break;

           case "attr_value":
                echo "<tr><th width=200>Option</th><th width=100>Value</th></tr>\n";
	   	while ($row=SQLFetchAssoc($result)) {
		    echo "<tr><td width=200>".$row["option"]."</td>"
			."<td width=100>".$row["value"]."</td></tr>";
		}
		break;

           case "config":
                echo "<tr><th width=100>Engine</th><th width=100>IPv4</th><th>IPv6</th><th>Version</th><th>Status</th></tr>\n";
	   	while ($row=SQLFetchAssoc($result)) {
		    $_status = $row["status"]?"ON":"OFF";
		    echo "<tr><td width=100>".$row["engine"]."</td>"
			."<td width=100>".long2ip($row["ip"])."</td>"
			."<td width=100>".$row["ipv6"]."</td>"
			."<td>".$row["version"]."</td>"
			."<td>".$_status." ".$row["start"]."</td>"
                	."<td><a href=\"javascript:_delete('".$row["engine"]."')\"><img src=\"images/trash.gif\" width=25 border=0></img></a></td></tr>\n";
		}
		break;

	   case "admin":
		echo "<tr><th>User</th><th>Comment</th><th>Privilege</th><th>Linked</th><th>View</th><th>Expire</th></tr>\n";
		$result = SQLQuery("SELECT uid,comment,priv_lvl,link,vrows,disable,expire FROM admin", $dbi);
	   while ($row=SQLFetchArray($result)) {
                $_style="";
                $_img="";
                echo "<tr>";
                if ($row["disable"]) {
			$_style=" style=\"border:ridge 2px red;\"";
                } else {
                  if (strcmp($row["expire"],"0000-00-00 00:00:00")) {
                        $_now = strtotime("now");
                        $_expires = strtotime($row["expire"]);
                        if ($_now > $_expires) {
                                $_style=" style=\"border:solid 2px red;\"";
                                $_img="<image src=\"images/expired_pass.png\" style=\"width:15px;height:15px;\" title=\"Expired\"></image>";
                        } else if ((($_expires - $_now) <= $pass_complex->{'changetime'}*24*60*60)) {
                                $_style=" style=\"border:solid 2px orange;\"";
                                $_img="<image src=\"images/expired_pass.png\" style=\"width:15px;height:15px;\" title=\"Expired\"></image>";
                        }
                  }
                }
                if (!$demo) {
                        $_linked = $row[3]?"Yes":"No";
                        echo "<td $_style>$_img <a href=\"javascript:_modify('".$row[0]."','".$row[1]."','".$row[2]."','".$row[3]."','".$row[4]."',".$row[5].",'".$row[6]."')\">".substr($row[0],0,20)."</a></td>";
                        echo "<td $_style>".$row[1]."</td>";
                        echo "<td $_style>".$row[2]."</td>";
                        echo "<td $_style>".$_linked."</td>";
                        echo "<td $_style>".$row[4]."</td>";
                        echo "<td $_style>".$row[6]."</td>";
                        echo "<td $_style><a href=\"javascript:_delete('".$row[0]."')\"><img src='images/trash.gif' width=25></a></td>";
                } else {
                        if ($row[0] == "admin") {
                                echo "<td $_style>".$row[0]."</td>";
                        } else {
                        	echo "<td $_style>$_img<a href=\"javascript:_modify('".$row[0]."','".$row[1]."','".$row[2]."','".$row[3]."','".$row[4]."',".$row[5].",'".$row[6]."')\">".$row[0]."</a></td>";
                        }
                        echo "<td $_style>".$row[1]."</td>"
                            ."<td $_style>";
                        echo $row[2]?"Yes":"No"."</td>";
                        echo "<td $_style>".$row[3]."</td>";
                        echo "<td $_style>".$row[4]."</td>";
                        echo "<td $_style>".$row[6]."</td>";
                        if ($row[0] != "admin") {
                                echo "<td><a href=\"javascript:_delete('".$row[0]."');\"><img src='images/trash.gif' width=25></a></td>";
                        }
                }
                echo "</tr>\n";
	   }
		break;
	}
	echo "</table>\n";
	SQLFreeResult($result);
} else {
//	echo "<legend>".$db_config->{'hosts'}[$_index]."</legend>\n";
	echo "<table border=1 cellspacing=1 cellpadding=2 class=\"_table2\">\n";
	echo "<tr><td>No records found</td></tr>\n";
	echo "</table>\n";
}

CloseDatabase($dbi);

if ($debug && $_ERROR) {
	echo "SELECT SQL_CALC_FOUND_ROWS $select FROM $table WHERE $where $where2\n";
	echo $_ERROR;
}

echo "<input type=\"hidden\" id=\"_lastID\" value=\"".($_lastID + 1)."\">\n";
?>

