<?php
/*
    Copyright (C) 2021 3Youngs, Inc
                                                                                                                                                                 
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

$dbi=OpenDatabase3($db_config, $_index);

if (!checkLoginXML($_COOKIE["login"],$dbi)) {
        echo "<script language=\"JavaScript\"> top.location.href=\"index.php?module=main\"; </script>";
}


$select = "*";
$join = "";
$where = "";
$where2 = "";


if ($table == "audit") {
       $select = "date, uid, INET_NTOA(client_ip), service, status, what";
}

if ($table == "access2") {
        $select = "date, INET_NTOA(nas_ip), name, terminal, uid, INET_NTOA(client_ip), service, status";
	$join = "left join host on access2.nas_ip = host.network";
}

if ($table == "accounting2") {
        $select = "date, INET_NTOA(nas_ip), name, uid, terminal, INET_NTOA(client_ip), type, service, priv_lvl, cmd, elapsed_time, bytes_in, bytes_out";
	$join = "left join host on accounting2.nas_ip = host.network";
}

if (isset($user) && $user) {
	$where = "WHERE uid='$user'";
}

if (isset($nas) && $nas) {
	if ($where) {
		$where .= " AND ";
	} else {
		$where = "WHERE ";
	}

	$network = preg_split('/\//', $nas);
	if (count($network)==1) $maskbits = 32;
	else $maskbits = $network[1];
	$where .= "INET_ATON('".$network[0]."') = (nas_ip & INET_ATON('".$netmask[$maskbits]."'))";
}

if (isset($client_ip) && $client_ip) {
	if ($where) {
		$where .= " AND ";
	} else {
		$where = "WHERE ";
	}

	$network = preg_split('/\//', $client_ip);
	if (count($network)==1) $maskbits = 32;
	else $maskbits = $network[1];
	$where .= "INET_ATON('".$network[0]."') = (client_ip & INET_ATON('".$netmask[$maskbits]."'))";
}

if ((isset($sdate)&&$sdate) || (isset($edate)&&$edate)) {
	if ($where) {
		$where .= " AND ";
	} else {
		$where = "WHERE ";
	}

	if ($sdate && $edate) {
		$where .= "date BETWEEN '$sdate' AND '$edate'";
	} else {
		if ($sdate) { $where .= "date >= '$sdate'"; }
	}
}

if (isset($status)&&$status) {
	if ($where) {
		$where .= " AND ";
	} else {
		$where = "WHERE ";
	}

	$where .= "status LIKE '$status'";
}

if (isset($cmd)&&$cmd) {
	if ($where) {
		$where .= " AND ";
	} else {
		$where = "WHERE ";
	}

	$where .= "cmd LIKE '$cmd'";
}

if (isset($auth)&&$auth) {
	if ($where) {
		$where .= " AND ";
	} else {
		$where = "WHERE ";
	}

	$where .= "auth = $auth";
}

$where .= " ORDER BY date DESC ";

if ($vrows) {
	$where2 .= " LIMIT ".$vrows;
	if (isset($offset)&&$offset) {
		$where2 .= " OFFSET ".$offset;
	}
}

if ($select) {
	$result = @SQLQuery("SELECT SQL_CALC_FOUND_ROWS $select FROM $table $join $where $where2", $dbi);
} else {
	$result = @SQLQuery("SELECT SQL_CALC_FOUND_ROWS * FROM $table $join $where $where2", $dbi);
}
$_ERROR = @SQLError($dbi);

if (@SQLNumRows($result) > 0) {
	$result2 = @SQLQuery("SELECT FOUND_ROWS()", $dbi);
	$_r = SQLFetchRow($result2);
	echo "<legend>".$db_config->{'hosts'}[$_index]."</legend>".$_r[0]." rows found\n";
	navi_buttons("_Report",$table,$_r[0],$offset,$vrows,$_index,'');
	SQLFreeResult($result2);

//	echo "<table border=1 cellspacing=1 cellpadding=2 class=\"_table2\">\n";
	echo "<table class=\"reports\">\n";
	switch ($table) {
	   case "access2":
		echo "<tr><th>Date</th><th>NAS</th><th>NAS Name</th><th>Terminal</th><th>User ID</th><th>Client IP</th><th>Service</th><th>Status</th></tr>\n";
		break;

	   case "accounting2":
		if ($auth == 1) {
			echo "<tr><th>Date</th><th>NAS</th><th>NAS Name</a><th>User</th><th>Terminal</th><th>Client IP</th><th>Type</th><th>Service</th><th>Priv Lvl</th><th>Command</th><th>Elapsed Time</th><th>Bytes In</th><th>Bytes Out</th></tr>";
		} else if ($auth == 2) {
			echo "<tr><th>Date</th><th>NAS</th><th>NAS Name</th><th>User</th><th>Session ID</th><th>Client IP</th><th>Type</th><th>Service</th><th>Acct Link Count</th><th>Multi Session ID</th><th>Session Time</th><th>Bytes In</th><th>Bytes Out</th></tr>";
		} else {
			echo "<tr><th>Date</th><th>NAS</th><th>NAS Name</th><th>User</th><th>Terminal/Session ID</th><th>Client IP</th><th>Type</th><th>Service</th><th>Priv Lvl/Acct LInk Count</th><th>Command/Multi Session ID</th><th>Elapsed/Session Time</th><th>Bytes In</th><th>Bytes Out</th></tr>";
		}
		break;

           case "audit":
                echo "<tr><th>Date</th><th>User</th><th>User IP</th><th>Service</th><th>Status</th><th>Changed</th></tr>\n";
                break;

	}
	while ($row=SQLFetchRow($result)) {
		echo "<tr>";
		foreach ($row as $item) {
			echo "<td>";
			if ($item) echo "$item";
			else echo "&nbsp;";
			echo "</td>";
		}
		echo "</tr>\n";
	}
	echo "</table>\n";
	SQLFreeResult($result);
} else {
	echo "<legend>".$db_config->{'hosts'}[$_index]."</legend>\n";
	echo "<table border=1 cellspacing=1 cellpadding=2 class=\"_table2\">\n";
	echo "<tr><td>No records found</td></tr>\n";
	echo "</table>\n";
}

CloseDatabase($dbi);
?>
