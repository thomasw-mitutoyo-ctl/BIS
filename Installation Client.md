# Installation des Clients

## Internetzugriff

Wichtig zu wissen, bei der Überlegung, ob die Informationssysteme überhaupt in Betrieb gehen dürfen: die Clients benötigen Internetzugriff, um die Kartendaten von OpenStreetMap abzurufen.

## Anleitung

[1]: https://www.raspberrypi.org/downloads/raspbian/         "Raspbian"
[2]: https://etcher.io/          "Etcher"
[3]: https://www.heise.de/download/product/win32-disk-imager-92033          "Win32DiskImager"
[4]: https://www.nagios.org/	"Nagios"

Diese Anleitung berücksichtigt nicht eventuelle Vorgaben für die Inbetriebnahme und Überwachung von Clients in Ihrem Unternehmen. Informieren Sie sich, ob Sie [Nagios][4] o.ä. zusätzlich installieren müssen.

Nach den hier beschriebenen Schritten kann der Informationsbildschirm in Betrieb gehen und „funktioniert“. Zusätzliche Schritte können danach für Mitarbeiter unsichtbar per SSH erledigt werden.

Nachahmer könnten auch überlegen, den [Raspberry von USB Stick zu booten (Heise)](https://www.heise.de/newsticker/meldung/Raspberry-Pi-3-bootet-von-USB-Stick-und-SSD-3288619.html), da die SD-Karten doch recht fehleranfällig sind.

1. SD Karte auf einem Windows-System mit [Raspbian][1] flashen, wie für jede Raspbian Installation üblich. Es genügt die kleine Vaiante von Raspbian mit Desktop, also ohne "recommended software". Die Schritte hängen vom Tool ab, sind aber nahezu selbsterklärend. Bekannt sind [Etcher][2] oder [Win32DiskImager][3]. 
2. Die Datei `install.sh` auf die SD-Karte kopieren
2. SD Karte in den Raspberry einlegen.
3. Monitor, Tastatur und Maus an den Raspberry anschließen.
4. Raspberry einschalten. Reboot abwarten, bei dem die Partitionsgröße auf die Größe der SD Karte angepasst wird.
5. Den Anweisungen am Bildschirm folgen:
 * Country: Germany, Zeitzone: Berlin
 * Passwort ändern
 * WiFi einrichten: falls der Client über WLAN betrieben werden soll
 * Aktualisierung durchführen
 * Reboot durchführen
6. Das Installationsskript ausführen `sudo /boot/install.sh <client-name> <server-name> [true]`, wobei `<client-name>` durch den Hostnamen des Clients und `<server-name>` durch den Namen des Servers zu ersetzen ist. Optional können mit `true` noch ein paar Diagnosetools mitinstalliert werden.

Eine Anbindung per LDAP/Active Directory ist nicht erforderlich, da der Browser anonym auf die Anzeige des Servers zugreift.

## Problembehebung

Schwarze Ränder (häufig) oder abgeschnittene Bilder (eher selten) können behoben werden:
`sudo nano /boot/config.txt`
Eintrag aktivieren:
`disable_overscan=1`

## Backup

Ein Backup der Clients ist unserer Meinung nach nicht erforderlich. Die Clients laufen im Wesentlichen mit einem Standard-Betriebssystem und können schnell wieder aufgesetzt werden.

Wir haben die Erfahrung gemacht, dass wir mit den Backups mehr Probleme hatten, als im heutigen Zustand ohne Backups. Ggfs. kann man sich eine geklonte SD-Karte in den Schrank legen.