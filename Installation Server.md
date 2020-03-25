# Installation des Servers

## Internetzugriff

Wichtig zu wissen, bei der Überlegung, ob der Server überhaupt in Betrieb gehen darf: der Server benötigt Internetzugriff, um die Wetterdaten abzurufen und zu cachen. Ansonsten würde (je nach Anzahl der Clients) die Anzahl der erlaubten Zugriff pro Zeiteinheit überschritten.

Während der Installation wird ebenfalls Internetzugriff benötigt, um die erforderlichen Pakete herunterzuladen.

## Betriebssystem

Wir verwenden Debian 9 Stretch. Das ist ähnlich zum Betriebssystem Raspbian des Raspberry und man muss sich nicht besonders umgewöhnen. Debian gibt es [kostenlos zum Download](https://www.debian.org/distrib/).

## Installation mittels Shell-Skript

Zur Installation der benötigten Pakete und Konfiguration der Einstellungen gibt es das Installationsskript [`install.sh`](https://github.com/thomasw-mitutoyo-ctl/BIS/tree/master/server/install.sh). Außer diesem Script wird nichts weiter benötigt, da das Skript dieses Git Repository klont.

```bash
wget https://raw.githubusercontent.com/thomasw-mitutoyo-ctl/BIS/master/server/install.sh
chmod +x install.sh
sudo ./install.sh <password> <api token> <fqdn> [yes]
```

Dem Skript müssen drei Parameter mitgegeben werden:

* `<password>`: Passwort für die Datenbank
* `<api token>`: das API Token von [OpenWeatherMap](https://openweathermap.org/). Ein Account wird benötigt, um ein API Token zu bekommen.
* `<fqdn>`: der qualifizierte Hostname des Servers, wie er später für den Aufruf im Browser verwendet wird
* optional: `<tools>`: falls `yes` angegeben wird, werden weitere Pakete installiert, die bei der Diagnose hilfreich sein können

Zur Erzeugung von Passwörtern eignet sich ein [Password-Generator](https://passwordsgenerator.net/).

### Pakete

LAMP ist ein Akronym für den kombinierten Einsatz von Programmen auf Basis von Linux, um dynamische Webseiten zur Verfügung zu stellen. Dabei stehen die einzelnen Buchstaben des Akronyms für die verwendeten Komponenten:

* Betriebssystem **L**inux
* Webserver **A**pache
* Datenbank **M**ariaDB (früher mySQL)
* Programmiersprache **P**HP

Weiterhin werden benötigt:

 * Git, um das Repository zu klonen
 * Python für den Weatherdaemon

Die Installation von Paketen erfolgt durch die Funktion `inst` im Skript. Es prüft mittels `apt list`, ob das Paket bereits installiert ist und installiert es andernfalls mit `apt install`.

### Datenbank

Das Installationsscript bereitet eine Konfigurationsdatei in `/var/www/bis/server/config/db_settings.ini` vor und legt eine Datenbank namens `bisdb` an. Der Benutzer `'bis'@'localhost'` erhält Zugriff auf die Datenbank.

### Konfiguration von Apache

Die Apache-Konfiguration wird in `/etc/apache2/sites-available/` unter dem Namen `bis.conf` erstellt und mit `a2ensite` aktiviert. Die Konfiguration verweist auf das Verzeichnis `/var/www/bis`, in welches dieses Git Repository geklont wird. Der Benutzer `www-data` erhält Zugriff.

### Wetterdienst

Der Wetterdienst sammelt Wetterdaten von verschiedenen Quellen und speichert diese zwischen. Anwendungen können dann diese Wetterinformationen gesammelt abrufen. Quelle ist der Dienst [openweathermap.org](https://openweathermap.org/).

Der Wetterdienst liegt in `/var/www/bis/server/weather/`. Ein eigener Benutzer namens `weatherdeaemon` erhält Zugriff auf dieses Verzeichnis.

Die Konfiguration des Wetterdienstes (z.B. das API Token) wird unter `/var/www/bis/server/weather/config.json` abgelegt. Das Logfile wird unter ` /var/log/weatherdaemon.log` angelegt.

Um die Daten des Wetterdienstes zu empfangen, kann eine HTTP-GET Anfrage an den Port 9999 gesendet werden. Die Daten werden im JSON-Format zurückgegeben.

## Installation über die Webseite

Öffne `http://<servername>/setup/` im Browser.

Für die Datenbankeinstellungen werden der Datenbankname, Benutzername und Password bereits übernommen (siehe Datenbank). Hier werden noch die Datenbank-Tabellen angelegt.

Für den Weatherdeamon muss die IP-Adresse oder der Name des Servers eingetragen werden, auf dem der Weatherdeamon läuft. Die Adresse wird im Javascript Code der Clients verwendet, um die Wetterdaten abzurufen.

Nach der Installation kann das Verzeichnis `/var/www/bis/server/html/setup/` gelöscht werden, damit nicht ungewollt die Konfiguration überschrieben wird.
