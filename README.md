Copyright (C) 2003-2021 Young Consulting, Inc

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

'''
Application:	WebUI
Author:		Andrew Young (baram01@hotmail.com)
Date:		01.03.2009
Scope:		This document describes how to setup the application.
Requirements:
	1)	Apache Web server (2.x or above is recommended)
	2)	PHP (4.2 or above is recommended)
	3)	MariaDB (10 or above is recommended)
Installation:
    First time:
	1) Install Apache Web server with PHP mnd MySQL modules.
	2) Install MariaDB.
	3) Unpack webui in main web document location (/var/www/html).
	   # tar xzf webui_v1.8bx.x.tar.gz
	4) Create database.
	   # mysql -u root < tac_plus.sql

    Upgrade:
	1) Copy config.php to /tmp/config.php.old
	2) Copy company logo from [webui directory]/images to /tmp
	3) Remove all files under current webui directory.
	4) Unpack webui in current webui directory.
	   # tar xfz webui_v1.8bx.x.tar.gz
	5) Update database.
	   # mysql -u root < tac_plus_upgrade.sql
	6) Copy company logo from /tmp to [webui directory]/cust
	7) If redundant DB, take the information from /tmp/config.php and update [webui directory]/cust/db_config.json
	8) Log into new webui.  Go to System and configure your way.
'''