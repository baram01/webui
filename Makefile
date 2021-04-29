VERSION = 5.0
RELEASE = b5
HTMLFILES = *.html
CUSTFILES = cust
JAVAFILES = js
PHPFILES = *.php
SQLFILES = *.sql
OTHERFILES = PHPMailer css images Makefile COPYRIGHTS Credits README.md ReleaseNotes tmp robots.txt *.cnf *.ico

package: $(CUSTFILES) $(PHPFILES) $(JAVAFILES) $(OTHERFILES)
	rm -fr tmp/*
	tar czf ~/webui_v$(VERSION)$(RELEASE).tar.gz $(CUSTFILES) $(PHPFILES) $(JAVAFILES) $(OTHERFILES)

version: version.php
	echo "<?php" > version.php
	echo "\$$version = \""$(VERSION)"\";" >>  version.php
	echo "\$$release = \""$(RELEASE)"\";" >>  version.php
	echo "?>" >> version.php
