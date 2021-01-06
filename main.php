<?php
/*
    Copyright (C) 2003-2020 Young Consulting, Inc

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
?>

<div id="main">
<fieldset class="_collapsible"><legend>Welcome to <?php echo $site_config->{"company_name"}; ?> WebUI</legend>
<p class="p_body"><?php echo "LEGAL MESSAGE<p>".$site_config->{"message"}; ?></p>
</fieldset>
</div>

<div id="login" class="_table">
       <form name="frmLogon" method="post" action="">
	    <fieldset class=" collapsible"><legend>Admin Login</legend>
            <table align="left" border=0>
            <tr><td>Username:</td>
                <td><input type="text" name="username" size=20></td>
	        <td style="color:red"><?php if ($_lmsg) echo "Bad Login"; ?></td>
            </tr>
            <tr><td>Password:</td>
                <td><input type="password" name="password" size=20></td>
		<td></td>
            </tr>
            <tr><td><input type="hidden" name="_login" value="<?php echo ($_ret)?'0':'1'; ?>"></td>
                <td><input type="submit" value="Logon" name="Logon"></td>
		<td></td>
            </tr>
            </table>
	    </fieldset>
        </form>
</div>

<div id="dashboard"></div>

<?php

if ($_MESSAGE) {
	echo "<div id=\"message_center\">\n";
	echo "<fieldset class=\"_collapsible\"><legend>Message Center </legend>\n";
	echo "<p class=\"p_body\">MESSAGE<p>".$_MESSAGE."</p>\n";
	echo "</fieldset>\n";
	echo "</div>\n";
}

if ($_ret) {
	echo "<script language=\"javascript\"> document.getElementById(\"login\").style.display=\"none\"; </script>\n";
}

?>
