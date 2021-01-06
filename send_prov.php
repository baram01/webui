<?php
/*
    Copyright (C) 2019  3 Youngs, Inc
                                                                                                                                                                 
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
require("PHPMailer/src/PHPMailer.php");
require("PHPMailer/src/SMTP.php");
require("PHPMailer/src/Exception.php");

if ($_ret < 5) {
	exit("Not authorized");
}

use PHPMailer\PHPMailer\PHPMailer;

//$dbi=OpenDatabase($dbhost, $dbuname, $dbpass, $dbname);
$dbi=OpenDatabase($db_config);

/***
if (checkLoginXML($_COOKIE["login"],$dbi) < 5) {
	CloseDatabase($dbi);
	return;
}
***/

$result = @SQLQuery("SELECT fname, email FROM contact_info WHERE uid='$_uid'", $dbi);

if (SQLNumRows($result) > 0) {
	$row = SQLFetchArray($result);
	$_fname = $row[0];
	$_to = $row[1];
	SQLFreeResult($result);

	$_file = file_get_contents('http://localhost/cust/prov_letter.doc');
	$replace_array = array(
                '##LOGO##' => $site_config->{'logo'},
                '##FIRSTNAME##' => $_fname,
                '##TACACS_WEB##' => $site_config->{'webui'},
                '##COMPANY_NAME##' => $site_config->{'company_name'},
                '##USERNAME##' => $_uid,
                '##TEMP_PASSWORD##' => $pass_complex->{'temp_pass'},
                '##PASS_EXPIRE##' => $pass_complex->{'expiretime'},
                '##PASS_SIZE##' => $pass_complex->{'pass_size'},
                '##PASS_UPPER##' => $pass_complex->{'upper'},
                '##PASS_LOWER##' => $pass_complex->{'lower'},
                '##PASS_NUM##' => $pass_complex->{'number'},
                '##PASS_SPECIAL##' => $pass_complex->{'special'}
                );

	$file1 = str_replace(array_keys($replace_array), array_values($replace_array), $_file);

	$mail = new PHPMailer;

	$mail->isSMTP();
	$mail->Host = $prov_config->{'mail_relay'};
	$mail->setFrom($prov_config->{'from'});
	$mail->addAddress($_to);
	$mail->isHTML();
	$mail->Subject = '[Confidential] Your TACACS+ Credentials';
	$mail->Body = $file1;
	$mail->addAttachment('cust/'.$site_config->{'logo'});

	if ($mail->send()) {
		echo "<P><font color=\"green\">Provision email sent.</font></P>";
	} else {
		echo "<P><font color=\"red\">Provision email not sent.</font></P>";
	}
} else {
	echo "<P><font color=\"red\">No records found</font>";
}

CloseDatabase($dbi);
?>
