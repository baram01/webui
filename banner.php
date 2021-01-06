<?php

function Banner()
{
	global $banner, $banner_gif, $version, $release, $ads, $site_config;
//	echo "<table bgcolor=\"#9999CC\" border=0 cellpadding=0 cellspacing=5 width=\"100%\">\n"
	echo "<table border=0 cellpadding=0 cellspacing=5 width=\"100%\">\n"
	    ."<tr><td align=\"center\" width=\"20%\">";
//	if (file_exists("cust/$logo_gif"))
	if (file_exists("cust/".$site_config->{'logo'}))
		echo "<img src=\"cust/".$site_config->{'logo'}."\"></img></td>\n";
	else
		echo "&nbsp;</td>\n";
	echo "    <td width=\"80%\" style=\"border-left: 2px solid #cdd0d4\">";
	if (file_exists("images/$banner_gif"))
		echo "<img src=\"images/$banner_gif\"></img></td>\n";
	else
		echo "<font color=\"dark blue\" size=7>&nbsp; $banner $version</font></td><td>$ads</td>\n";
	echo "</tr>\n</table>\n";
}

?>
