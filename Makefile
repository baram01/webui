VERSION = 5.0
RELEASE = b2.5g
HTMLFILES = *.html
CUSTFILES = cust
JAVAFILES = js
PHPFILES = *.php
SQLFILES = *.sql
OTHERFILES = PHPMailer css images Makefile COPYRIGHTS Credits README ReleaseNotes tmp robots.txt *.cnf *.ico
package: $(HTMLFILES) $(CUSTFILES) $(PHPFILES) $(JAVAFILES) $(OTHERFILES)
	rm -fr tmp/*
	tar czf webui_v$(VERSION)$(RELEASE).tar.gz $(HTMLFILES) $(CUSTFILES) $(PHPFILES) $(JAVAFILES) $(OTHERFILES)

