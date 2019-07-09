#!/bin/bash

err() {
	echo -e "\e[91m$1\e[39m"
}
succ() {
	echo -e "\e[92m$1\e[39m"
}
doing() {
	echo -e "\e[96m$1\e[39m"
}

if [ "$2" = "" ]; then
	err "Wrong parameters"
	echo "Usage: $0 <hostname> <server> [<diagnosis>]"
	echo "   hostname: Desired host name of this BIS client, e.g. bis-client-1"
	echo "   server: Name or IP address plus path of the BIS server delivering the web pages. e.g. bis-server/subdir"
	echo "   diagnosis: 'true' if diagnosis tools shall be installed. Default: false"
	exit 3
fi

# SSH aktivieren
if [ "`raspi-config nonint get_ssh`" -eq "1" ]; then
	err "SSH is disabled."
	doing "Enabling SSH..."
	raspi-config nonint do_ssh 0
else
	succ "SSH is enabled."
fi

# Hostname
if [ "`hostname`" = "$1" ]; then
	succ "Hostname is already $1"
else
	err "Hostname is not set yet."
	doing "Setting hostname to $1..."
	hostname $1
fi

# NTP
if [ "`cat /etc/systemd/timesyncd.conf | grep ^NTP | wc -l`" -eq "1" ]; then
	succ "Time servers are set."
else
	err "Time servers not set."
	doing "Setting German time servers..."
	sed -i -E "s/^.?NTP=.*/NTP=0.de.pool.ntp.org/" /etc/systemd/timesyncd.conf
	sed -i -E "s/^.?FallbackNTP=.*/FallbackNTP=pool.ntp.org/" /etc/systemd/timesyncd.conf
fi

if [ "`timedatectl status | grep \"Network time on\" | grep yes | wc -l`" -eq "1" ]; then
	succ "Network time is on"
else
	err "Network time is off."
	doing "Enabling NTP..."
	timedatectl set-ntp 1
fi

if [ "`service systemd-timesyncd status | grep running | wc -l`" -eq "1" ] ;then
	succ "Timesync service is running"
else
	err "Timesync service not running"
	doing "Enabling..."
	service systemd-timesyncd start
fi

# Chromium
if [ "`apt list --installed 2>/dev/null | grep chromium-browser | wc -l`" -gt "0" ]; then
	succ "Chromium is installed"
else
	err "Chromium is not installed"
	doing "Installing..."
	apt install chromium-browser
fi


# Hide Mouse Cursor
if [ "`apt list --installed 2>/dev/null | grep unclutter | wc -l`" -eq "1" ] ;then
	succ "Unclutter is installed"
else
	err "Unclutter is not installed"
	doing "Installing ..."
	apt install unclutter
fi


# Chromium Autostart, disable screensaver etc.
if [ ! -d /home/pi/.config/lxsession ] ;then
	mkdir /home/pi/.config/lxsession
fi

if [ ! -d /home/pi/.config/lxsession/LXDE-pi ] ;then
	mkdir /home/pi/.config/lxsession/LXDE-pi
fi

if [ ! -f /home/pi/.config/lxsession/LXDE-pi/autostart ] ;then
	touch /home/pi/.config/lxsession/LXDE-pi/autostart
fi

if [ "`cat /home/pi/.config/lxsession/LXDE-pi/autostart | grep chromium-browser | wc -l`" -eq "1" ] ;then
	succ "Chromium is in Autostart"
else
	err "Autostart is not ready"
	doing "Preparing ..."
	cat <<-EOF > /home/pi/.config/lxsession/LXDE-pi/autostart
@xset s off
@xset -dpms
@xset s noblank
@unclutter -grab -visible
@chromium-browser --kiosk --incognito --noerrdialogs --disable-infobars --disable-session-crashed-bubble --no-first-run --fast --fast-start --disable-translate http://$2/main_view.php
EOF
fi


# Diagnosis tools

inst() {
	if [ "`apt list --installed 2>/dev/null | grep $1 | wc -l`" -gt "0" ] ;then
		succ "$1 is installed"
	else
		err "$1 is not installed yet"
		doing "Installing ..."
		apt install -y $1
	fi
}

if [ "$3" = "true" ] ;then
	inst lrzsz
	inst aptitude
	inst apt-utils
	inst net-tools
	inst dnsutils
	inst dkms
else
	succ "Not installing diagnosis tools"
fi
