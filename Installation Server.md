# Installation des Servers

## Internetzugriff

Wichtig zu wissen, bei der Überlegung, ob der Server überhaupt in Betrieb gehen darf: der Server benötigt Internetzugriff, um die Wetterdaten abzurufen und zu cachen.

## Betriebssystem

Wir verwenden Debian 9 Stretch. Das ist ähnlich zum Betriebssystem Raspbian des Raspberry und man muss sich nicht besonders umgewöhnen. Debian gibt es [kostenlos zum Download](https://www.debian.org/distrib/).

## Installieren von benötigten Paketen

LAMP ist ein Akronym für den kombinierten Einsatz von Programmen auf Basis von Linux, um dynamische Webseiten zur Verfügung zu stellen. Dabei stehen die einzelnen Buchstaben des Akronyms für die verwendeten Komponenten:

* Betriebssystem **L**inux
* Webserver **A**pache
* Datenbank **M**ariaDB (früher mySQL)
* Programmiersprache **P**HP

Weiterhin werden benötigt:

 * Git, um das Repository zu klonen
 * Python für den Weatherdaemon

```bash
apt install mariadb-client mariadb-server php7.0 php7.0-mysql php7.0-mbstring php7.0-gd apache2 libapache2-mod-php7.0 git python3 python-pip
```

## Konfiguration von Apache

Die .htaccess Datei im html/ Ordner muss von Apache erlaubt sein. Dazu muss man in der Datei `/etc/apache2/apache2.conf` folgendes ändern:

```apache
<Directory /var/www/bis>
        Options Indexes FollowSymLinks
        AllowOverride None
        Require all granted
</Directory>
```

hier das `AllowOverride` von `None` auf `All` ändern...

```apache
<Directory /var/www/bis>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
</Directory>
```

## Datenbank und Benutzer anlegen

Mit `mysql -u root -p` und der Eingabe des bei der Installation vergebenen root-Passworts beim Datenbank-Server anmelden.

```mysql
CREATE DATABASE bisdb;
CREATE USER 'bis'@'localhost' IDENTIFIED BY '<somePassword>';
GRANT ALL PRIVILEGES ON bisdb.* TO bis@localhost;
FLUSH PRIVILEGES;
quit
```

Dabei *&lt;somePassword&gt;* durch ein sicheres Password ersetzen. Dazu eignet sich ein [Password-Generator](https://passwordsgenerator.net/).

## Klonen dieses Repositories

Um diesen Service zu installieren klont man einfach dieses Repository in das Apache Verzeichnis:

```bash
sudo rm /var/www/bis -R
sudo git clone http://<github-URL>/BIS.git /var/www/bis
```

Um dem Apache-Process, der als User `www-data` läuft, die Rechte einzuräumen in die Config-Dateien zu schreiben müssen wir die Rechte anpassen:

```bash
sudo chown www-data:www-data /var/www/bis/* -R
```

## Installieren des Weatherdaemons

Der Weatherdaemon sammelt Wetterdaten von verschiedenen Quellen und speichert diese zwischen. Anwendungen können dann diese Wetterinformationen gesammelt abrufen. Quellen können unter anderem der openweathermap.org service sein, oder die eigene Wetterstation.

Um die Daten zu Empfangen muss eine HTTP-GET Anfrage an den Port 9999 gesendet werden. Die Daten welden im JSON-Format zurückgegeben.

Es gibt ein Installationsskript in Python, um den Weatherdeamon zu installieren. Um es als Service laufen zu lassen, wird systemd benutzt.

1. Installiere requests `pip install requests`
1. Klone dieses Repository in ein locales Verzechnis auf dem Server
   `git clone http://<TODO>/Weatherdaemon.git /etc/Weatherdaemon`
1. Die Konfigurationsdatei erstellen `cp /etc/Weatherdaemon/config.json.example /etc/Weatherdaemon/config.json`
1. Bei [OpenWeatherMap](https://home.openweathermap.org/users/sign_up) anmelden und einen API Token beantragen.
1. Konfigurationsdatei anpassen und eigenen API Token eintragen 
   `sudo nano /etc/Weatherdaemon/config.json` 
1. Den Server konfigurieren: bei Hostname die IP Adresse oder den Namen des Servers eintragen
1. Mache `WeatherServer.py` ausführbar mit
   `chmod +x /etc/Weatherdaemon/WeatherServer.py`
1. Füge einen neuen Benutzer hinzu
   `useradd -r -s /bin/false weatherdaemon`
1. Ändere den Eigentümer des Verzeichnisses
   `chown -R weatherdaemon:weatherdaemon /etc/Weatherdaemon/`
1. Kopiere die Datei `weatherdaemon.service` zu systemd
   `cp /etc/Weatherdaemon/weatherdaemon.service /etc/systemd/system/weatherdaemon.service`
1. Aktivere den Service
   `systemctl enable weatherdaemon`
   `systemctl start weatherdaemon`
1. Prüfe den Status
   `systemctl status weatherdaemon`
1. Lege für das Looging eine Datei an `touch /var/log/weatherdaemon.log`
1. Um das Logging zu konfigurieren füge diese Zeile
   `:programname,isequal,"Weatherdaemon"         /var/log/weatherdaemon.log`
   hier `/etc/rsyslog.d/50-default.conf` hinzu. (Wenn die Datei nicht existiert erzeuge sie)
   Starte des Syslog neu:
   `systemctl restart rsyslog`
1. Configure log rolling by copying the weatherdaemon file:
   `cp /etc/Weatherdaemon/weatherdaemon /etc/logrotate.d/weatherdaemon`
   Test it using:
   `logrotate -d /etc/logrotate.d/weatherdaemon`


### Update des Waetherdaemons

Um den Weatherdaemon zu aktualisieren muss man das Git Repository auf den neuen Stand bringen:

1. `cd /etc/Weatherdaemon`
1. `git reset --hard`
2. `git pull --force`
3. `chmod +x /etc/Weatherdaemon/WeatherServer.py`
4. `systemctl restart weatherdaemon.service`

## Erste Konfiguration

Öffne `http://<servername>/setup/` im Browser.

Für die Datenbankeinstellungen müssen der Datenbankname, Benutzername und Password von oben übernommen werden (siehe Datenbank anlegen...).

Für den Weatherdeamon muss die IP-Adresse oder der Namen des Servers eingetragen werden, auf dem der Weatherdeamon läuft. Die Adresse wird im Javascript Code der Clients verwendet, um die Wetterdaten abzurufen.

Nach der Installation kann das Verzeichnis `/var/www/bis/html/setup/` gelöscht werden, damit nicht ungewollt die Konfiguration überschrieben wird.
