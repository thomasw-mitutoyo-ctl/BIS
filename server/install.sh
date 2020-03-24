#!/bin/bash

succ() {
	echo -e "\e[92m$1\e[39m"
}

err() {
	echo -e "\e[91m$1\e[39m"
}

doing() {
	echo -e "\e[96m$1\e[39m"
}

inst() {
	if [ "`apt list --installed 2>/dev/null | grep $1 | wc -l`" -gt "0" ] ;then
		succ "$1 is already installed"
	else
		err "$1 is not installed yet"
		doing "Installing ..."
		apt install -y $1
	fi
}

if [ "$2" = "" ] ;then
        err "Wrong parameters"
        echo "Usage: $0 <database password> <api token> <fqdn> [<tools>]"
        echo "   <database password>: Password to use for the MySQL bisdb datab$"
	echo "   <api token>:         API token of OpenWeatherMap.org"
	echo "   <fqdn>:              Host name (fully qualified)"
	echo "                        Maybe `hostname -f`"
	echo "   <tools>:             Install additional tools for diagnosis"
	echo "                        like dnsutils and lynx"
	echo "                        Use 'yes' to install. Default: no"
        exit 3
fi

if [ "$4" = "yes" ] ;then
    inst dnsutils
    inst lynx
}

# Install LAMP packages
inst apache2
inst mariadb-client
inst mariadb-server
inst php7.0
inst php7.0-mysql
inst php7.0-mbstring
inst php7.0-gd
inst libapache2-mod-php7.0

# MySQL Service
if [ "`service mysql status | grep running | wc -l`" -eq "1" ] ;then
	succ "MySQL service is started"
else
	err "MySQL service not running"
	doing "Starting ..."
	service mysql start
fi

if [ -f /var/www/bis/server/config/db_settings.ini ] ;then
	succ "Database settings seem configured"
else
	err "Database settings are not configured"
	doing "Configuring ..."
	cat <<-EOF > /var/www/bis/server/config/db_settings.ini
Server = "localhost"
Username = "bis"
Password = "$1"
DatabaseName = "bisdb"
EOF
	chown www-data:www-data /var/www/bis/server/config/db_settings.ini
fi

# PHP
if [ "`php -v | grep PHP | grep 7.0 | wc -l`" -eq "1" ] ;then
	succ "PHP 7.0 can execute"
else
	err "PHP 7.0 cannot execute"
	err "Don't know how this could be fixed. Is a newer version installed?"
	exit 3
fi

# Git
inst git

# Python
inst python-pip

# Configure Apache website

if [ "`service apache2 status | grep running | wc -l`" -eq "1" ] ;then
	succ "Apache is running"
else
	err "Apache is not running"
	doing "Starting ..."
	service apache2 start
fi

if [ -d /var/www/bis ] ;then
        succ "Webserver directory exists"
else
        err "Webserver directory does not exist yet"
        doing "Creating ..."
        mkdir /var/www/bis
        sudo chown -R www-data:www-data /var/www/bis
fi

if [ -f /etc/apache2/sites-available/bis.conf ] ;then
	succ "Apache Config file found"
else
	err "Apache config file not found"
	doing "Creating ..."
	cat <<EOF > /etc/apache2/sites-available/bis.conf
Listen 80
<VirtualHost *:80>
	ServerName $3
	DocumentRoot "/var/www/bis/html"
	<Directory /var/www/bis/html>
		Options FollowSymLinks
		AllowOverride All
		Require all granted
	</Directory>
        <FilesMatch \.php$>
                SetHandler application/x-httpd-php
        </FilesMatch>
</VirtualHost>
EOF
	a2ensite bis
	service apache2 reload
fi

if [ -f /etc/apache2/sites-enabled/bis.conf ] ;then
	succ "Site is enabled"
else
	err "Site is disabled"
	doing "Enabling ..."
	a2ensite bis
	service apache2 reload
fi

if [ "`ls -l /etc/apache2/mods-enabled | grep php7 | wc -l`" -gt "1" ] ;then
	succ "PHP module for Apache is enabled"
else
	err "PHP module for Apache disabled"
	doing "Enabling ..."
	a2enmod php7.0
fi

# Create MySQL database

echo -e "\e[94mWhen prompted for a password, that's the MySQL password.\e[39m"

if [ "`echo SHOW DATABASES | mysql -u root -p | grep bisdb | wc -l`" -eq "1" ] ;then
	succ "BIS database already available"
else
	err "BIS database not available yet"
	doing "Creating ..."
	mysql -u root -p -e "CREATE DATABASE bisdb;"
fi

if [ "`mysql -u root -p -e \"select user from mysql.user\"| grep bis | wc -l`" -eq "1" ] ;then
	succ "Database user already exists"
else
	err "Database user does not exist"
	doing "Creating ..."
	mysql -u root -p -e "create user 'bis'@'localhost' IDENTIFIED BY '$1';"
	mysql -u root -p -e "GRANT ALL ON bisdb.* TO bis@localhost; FLUSH PRIVILEGES;"
fi

# Clone repository

if [ -d /var/www/bis/.git ] ;then
	succ "Git repository already cloned"
else
	err "Git repository not cloned yet"
	doing "Cloning ..."
	git clone https://github.com/thomasw-mitutoyo-ctl/BIS /var/www/bis/
fi

pushd . >/dev/null
cd /var/www/bis
if [ "`git rev-parse @`" = "`git rev-parse @{u}`" ] ;then
	succ "Repository is up-to-date"
else
	err "Repository needs updates"
	doing "Pulling ..."
	git pull
fi
popd >/dev/null

# Install weatherdaemon
pythonlib() {
	if [ "`pip list 2>/dev/null | grep $1 | wc -l`" -gt "0" ] ;then
		succ "Python package $1 is installed"
	else
		err "Python package $1 not installed yet"
		doing "Installing ..."
		pip install $1
	fi
}

pythonlib requests

if [ -x /var/www/bis/weather/WeatherServer.py ] ;then
	succ "Weatherdaemon script is executable"
else
	err "Weatherdaemon script is not executable"
	doing "Changing permissions ..."
	chmod +x /var/www/bis/weather/WeatherServer.py
fi

if  id -u weatherdaemon > /dev/null ;then
	succ "User for waether daemon exists"
else
	err "User for weatherdaemon does not exist"
	doing "Creating user ..."
	useradd -r -s /bin/false weatherdaemon
fi

if [ "`stat --format %U /var/www/bis/weather/`" = "weatherdaemon" ] ;then
	succ "Weather script is owned by weatherdaemon"
else
	err "Weather script does not belong Weatherdaemon"
	doing "Changing owner ..."
	chown -R weatherdaemon:weatherdaemon /var/www/bis/weather/
fi

if [ -f /var/www/bis/weather/config.json ] ;then
	succ "Weatherdaemon config file exists"
else
	err "Weatherdaemon config file not present"
	doing "Creating ..."
	cp /var/www/bis/weather/config.json.example /var/www/bis/weather/config.json
fi

if [ "`cat /var/www/bis/weather/config.json | grep MapToken | grep your | wc -l`" -eq "0" ] ;then
	succ "Weatherdaemon seems to have a token"
else
	err "Weatherdaemon does not have a token"
	doing "Inserting token ..."
	sed -i -E "s/.your token here./$2/" /var/www/bis/weather/config.json
fi

if [ -f /etc/systemd/system/weatherdaemon.service ] ;then
	succ "Weatherdaemon service file found"
else
	err "Weatherdaemon service file does not exist"
	doing "Creating ..."
	cp /var/www/bis/weather/weatherdaemon.service /etc/systemd/system/
fi

if [ "`cat /etc/systemd/system/weatherdaemon.service | grep WorkingDir | grep /etc | wc -l`" -eq "0" ] ;then
	succ "Weatherdaemon is not running in /etc as working directory"
else
	err "Weatherdaemon is running in /etc as working directory"
	doing "Configuring ..."
	sed -i -E "s_WorkingDirectory=.*_WorkingDirectory=/var/www/bis/weather_" /etc/systemd/system/weatherdaemon.service
fi

if [ "`cat /etc/systemd/system/weatherdaemon.service | grep ExecStart | grep /etc | wc -l`" -eq "0" ] ;then
	succ "Weatherdaemon is not running from /etc"
else
	err "Weatherdaemon is running from /etc"
	doing "Configuring ..."
	sed -i -E "s_ExecStart=.*_ExecStart=/var/www/bis/weather/WeatherServer.py_" /etc/systemd/system/weatherdaemon.service
fi

if [ -f /var/log/weatherdaemon.log ] ;then
	succ "Weatherdaemon log file found"
else
	err "Weatherdaemon log file not found"
	doing "Creating ..."
	touch /var/log/weatherdaemon.log
	chown weatherdaemon:weatherdaemon /var/log/weatherdaemon.log
fi


if [ -f /etc/rsyslog.d/50-default.conf ] ;then
        succ "Config file for log exists"
else
        err "Config file for log does not exist"
        doing "Creating ..."
        touch /etc/rsyslog.d/50-default.conf
fi

if [ "`cat /etc/rsyslog.d/50-default.conf | grep Weatherdaemon | wc -l`" -eq "1" ] ;then
	succ "Config file for log contains Weatherdaemon"
else
	err "Config file does not contain info for Weatherdaemon"
	doing "Configuring ..."
	echo -e ":programname,isequal,\"Weatherdaemon\"\t/var/log/weatherdaemon.log" >> /etc/rsyslog.d/50-default.conf
	service rsyslog restart
fi

if systemctl is-enabled weatherdaemon --quiet ;then
	succ "Weatherdaemon is enabled"
else
	err "Weatherdaemon is disabled"
	doing "Enabling ..."
	systemctl enable weatherdaemon
fi

if systemctl is-active weatherdaemon --quiet ;then
	succ "Weatherdaemon is running"
else
	err "Weatherdaemon is not running"
	doing "Starting ..."
	service weatherdaemon start
fi

if nc -z localhost 9999 > /dev/null ;then
	succ "Weatherdaemon port is open"
else
	err "Weatherdaemon port 9999 is not reachable"
	err "I don't know how this could be fixed"
	exit 3
fi

if [ -f /etc/logrotate.d/weatherdaemon ] ;then
	succ "Logrotate is configured"
else
	err "Logrotate is not configured yet"
	doing "Configuring ..."
	# Do not copy, because this might contain Windows CRLF line breaks
	tr -d '\r' < /var/www/bis/weather/logrotate > /etc/logrotate.d/weatherdaemon
fi

if [ -f /var/www/bis/server/config/weather_service_settings.ini ] ;then
	succ "Apache knows where to find weather data"
else
	err "Apache does not find weather data yet"
	doing "Configuring ..."
	cat <<-EOF > /var/www/bis/server/config/weather_service_settings.ini
Server = "$3"
Port = 9999
EOF
	chown www-data:www-data /var/www/bis/server/config/weather_service_settings.ini
fi
