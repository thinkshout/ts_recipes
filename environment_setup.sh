#!/bin/bash

#set -e

confirmupdate () {
  read -r -p "$1 [y/n]" response
  case $response in
    [yY])
      true
      ;;
    *)
      false
      ;;
  esac
}

xcode_path=`xcode-select -p`
echo ""
echo "Sets up the standard ThinkShout development environment."
echo "Work-in-progress."
echo ""
echo "There's no UNDO for this script, so please double check the prereqs now:"
echo "- Required: OSX 10.10 Yosemite"
echo "- Required: Xcode with Command Line Tools (xcode-select --install)"
echo ""

if confirmupdate "Would you like to proceed?"; then
  echo "Starting setup..."
else
  exit
fi

echo $'\n'
echo "Installing Homebrew."
echo $'\n'

# Check Homebrew is installed.
brew_installed=`which brew`
if [ "$brew_installed" == "" ] ; then
  ruby -e "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/master/install)"
else
  echo "Updating Homebrew"
  echo $'\n'

  brew update
fi

brew_result=`brew doctor`

if [ "$brew_result" != "Your system is ready to brew." ]; then
  echo "Homebrew was not successfully installed. See message:"
  echo "$brew_result"
  exit 1;
fi

brew tap homebrew/dupes
brew tap homebrew/homebrew-php

echo $'\n'
echo "Installing git"
echo $'\n'

brew install git

echo $'\n'
echo "Installing wget"
echo $'\n'

brew install wget

echo $'\n'
echo "Installing MySQL"
echo $'\n'

brew install mysql

cp -v $(brew --prefix mysql)/support-files/my-default.cnf $(brew --prefix)/etc/my.cnf

cat >> $(brew --prefix)/etc/my.cnf <<'EOF'

# Echo & Co. changes
max_allowed_packet = 1073741824
innodb_file_per_table = 1
EOF

sed -i '' 's/^#[[:space:]]*\(innodb_buffer_pool_size\)/\1/' $(brew --prefix)/etc/my.cnf

[[ ! -d ~/Library/LaunchAgents ]] && mkdir -v ~/Library/LaunchAgents

ln -sfv $(brew --prefix mysql)/homebrew.mxcl.mysql.plist ~/Library/LaunchAgents/

echo $'\n'
echo "Installing Apache"
echo $'\n'

sudo launchctl unload /System/Library/LaunchDaemons/org.apache.httpd.plist 2>/dev/null

brew install homebrew/apache/httpd22 --with-brewed-openssl

[ ! -d ~/Sites ] && mkdir -pv ~/Sites

mkdir -pv ~/Sites/{logs,ssl}

touch ~/Sites/httpd-vhosts.conf

USERHOME=$(dscl . -read /Users/`whoami` NFSHomeDirectory | awk -F": " '{print $2}') cat >> $(brew --prefix)/etc/apache2/2.2/httpd.conf <<EOF
# Include our VirtualHosts
Include ${USERHOME}/Sites/httpd-vhosts.conf
EOF

(export USERHOME=$(dscl . -read /Users/`whoami` NFSHomeDirectory | awk -F"\: " '{print $2}') ; cat > ~/Sites/httpd-vhosts.conf <<EOF
#
# Listening ports.
#
#Listen 8080  # defined in main httpd.conf
Listen 8443

#
# Use name-based virtual hosting.
#
NameVirtualHost *:8080
NameVirtualHost *:8443

#
# Set up permissions for VirtualHosts in ~/Sites
#
<Directory "${USERHOME}/Sites">
    Options Indexes FollowSymLinks MultiViews
    AllowOverride All
    <IfModule mod_authz_core.c>
        Require all granted
    </IfModule>
    <IfModule !mod_authz_core.c>
        Order allow,deny
        Allow from all
    </IfModule>
</Directory>

# For http://localhost in the users' Sites folder
<VirtualHost _default_:8080>
    ServerName localhost
    DocumentRoot "${USERHOME}/Sites"
</VirtualHost>
<VirtualHost _default_:8443>
    ServerName localhost
    Include "${USERHOME}/Sites/ssl/ssl-shared-cert.inc"
    DocumentRoot "${USERHOME}/Sites"
</VirtualHost>

#
# VirtualHosts
#

## Manual VirtualHost template for HTTP and HTTPS
#<VirtualHost *:8080>
#  ServerName project.dev
#  CustomLog "${USERHOME}/Sites/logs/project.dev-access_log" combined
#  ErrorLog "${USERHOME}/Sites/logs/project.dev-error_log"
#  DocumentRoot "${USERHOME}/Sites/project.dev"
#</VirtualHost>
#<VirtualHost *:8443>
#  ServerName project.dev
#  Include "${USERHOME}/Sites/ssl/ssl-shared-cert.inc"
#  CustomLog "${USERHOME}/Sites/logs/project.dev-access_log" combined
#  ErrorLog "${USERHOME}/Sites/logs/project.dev-error_log"
#  DocumentRoot "${USERHOME}/Sites/project.dev"
#</VirtualHost>

#
# Automatic VirtualHosts
#
# A directory at ${USERHOME}/Sites/webroot can be accessed at http://webroot.dev
# In Drupal, uncomment the line with: RewriteBase /
#

# This log format will display the per-virtual-host as the first field followed by a typical log line
LogFormat "%V %h %l %u %t \"%r\" %>s %b \"%{Referer}i\" \"%{User-Agent}i\"" combinedmassvhost

# Auto-VirtualHosts with .dev
<VirtualHost *:8080>
  ServerName dev
  ServerAlias *.dev

  CustomLog "${USERHOME}/Sites/logs/dev-access_log" combinedmassvhost
  ErrorLog "${USERHOME}/Sites/logs/dev-error_log"

  VirtualDocumentRoot ${USERHOME}/Sites/%-2+
</VirtualHost>
<VirtualHost *:8443>
  ServerName dev
  ServerAlias *.dev
  Include "${USERHOME}/Sites/ssl/ssl-shared-cert.inc"

  CustomLog "${USERHOME}/Sites/logs/dev-access_log" combinedmassvhost
  ErrorLog "${USERHOME}/Sites/logs/dev-error_log"

  VirtualDocumentRoot ${USERHOME}/Sites/%-2+
</VirtualHost>
EOF
)

(export USERHOME=$(dscl . -read /Users/`whoami` NFSHomeDirectory | awk -F"\: " '{print $2}') ; cat > ~/Sites/ssl/ssl-shared-cert.inc <<EOF
SSLEngine On
SSLProtocol all -SSLv2 -SSLv3
SSLCipherSuite ALL:!ADH:!EXPORT:!SSLv2:RC4+RSA:+HIGH:+MEDIUM:+LOW
SSLCertificateFile "${USERHOME}/Sites/ssl/selfsigned.crt"
SSLCertificateKeyFile "${USERHOME}/Sites/ssl/private.key"
EOF
)

openssl req \
  -new \
  -newkey rsa:2048 \
  -days 3650 \
  -nodes \
  -x509 \
  -subj "/C=US/ST=State/L=City/O=Organization/OU=$(whoami)/CN=*.dev" \
  -keyout ~/Sites/ssl/private.key \
  -out ~/Sites/ssl/selfsigned.crt

ln -sfv $(brew --prefix httpd22)/homebrew.mxcl.httpd22.plist ~/Library/LaunchAgents

sudo bash -c 'export TAB=$'"'"'\t'"'"'
cat > /Library/LaunchDaemons/co.echo.httpdfwd.plist <<EOF
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
${TAB}<key>Label</key>
${TAB}<string>co.echo.httpdfwd</string>
${TAB}<key>ProgramArguments</key>
${TAB}<array>
${TAB}${TAB}<string>sh</string>
${TAB}${TAB}<string>-c</string>
${TAB}${TAB}<string>echo "rdr pass proto tcp from any to any port {80,8080} -> 127.0.0.1 port 8080" | pfctl -a "com.apple/260.HttpFwdFirewall" -Ef - &amp;&amp; echo "rdr pass proto tcp from any to any port {443,8443} -> 127.0.0.1 port 8443" | pfctl -a "com.apple/261.HttpFwdFirewall" -Ef - &amp;&amp; sysctl -w net.inet.ip.forwarding=1</string>
${TAB}</array>
${TAB}<key>RunAtLoad</key>
${TAB}<true/>
${TAB}<key>UserName</key>
${TAB}<string>root</string>
</dict>
</plist>
EOF'

echo $'\n'
echo "Installing PHP"
echo $'\n'

# TODO: Make PHP version optional (PHP 5.3 / PHP 5.4 at least)

brew install php55 --with-homebrew-apxs --with-apache

cat >> $(brew --prefix)/etc/apache2/2.2/httpd.conf <<EOF
# Send PHP extensions to mod_php
AddHandler php5-script .php
AddType text/html .php
DirectoryIndex index.php index.html
EOF

sed -i '-default' "s|^;\(date\.timezone[[:space:]]*=\).*|\1 \"$(sudo systemsetup -gettimezone|awk -F": " '{print $2}')\"|; s|^\(memory_limit[[:space:]]*=\).*|\1 256M|; s|^\(post_max_size[[:space:]]*=\).*|\1 200M|; s|^\(upload_max_filesize[[:space:]]*=\).*|\1 100M|; s|^\(default_socket_timeout[[:space:]]*=\).*|\1 600|; s|^\(max_execution_time[[:space:]]*=\).*|\1 300|; s|^\(max_input_time[[:space:]]*=\).*|\1 600|;" $(brew --prefix)/etc/php/5.5/php.ini

USERHOME=$(dscl . -read /Users/`whoami` NFSHomeDirectory | awk -F": " '{print $2}') cat >> $(brew --prefix)/etc/php/5.5/php.ini <<EOF
; PHP Error log
error_log = ${USERHOME}/Sites/logs/php-error_log
EOF

touch $(brew --prefix php55)/lib/php/.lock && chmod 0644 $(brew --prefix php55)/lib/php/.lock

/usr/bin/sed -i '' "s|^\(\;\)\{0,1\}[[:space:]]*\(opcache\.enable[[:space:]]*=[[:space:]]*\)0|\21|; s|^;\(opcache\.memory_consumption[[:space:]]*=[[:space:]]*\)[0-9]*|\1256|;" $(brew --prefix)/etc/php/5.5/php.ini

echo $'\n'
echo "Installing Services"
echo $'\n'

brew tap homebrew/services

echo $'\n'
echo "Installing Dnsmasq"
echo $'\n'

brew install dnsmasq

echo 'address=/.dev/127.0.0.1' > $(brew --prefix)/etc/dnsmasq.conf
echo 'listen-address=127.0.0.1' >> $(brew --prefix)/etc/dnsmasq.conf
echo 'port=35353' >> $(brew --prefix)/etc/dnsmasq.conf

ln -sfv $(brew --prefix dnsmasq)/homebrew.mxcl.dnsmasq.plist ~/Library/LaunchAgents

sudo mkdir -v /etc/resolver
sudo bash -c 'echo "nameserver 127.0.0.1" > /etc/resolver/dev'
sudo bash -c 'echo "port 35353" >> /etc/resolver/dev'

echo $'\n'
echo "Installing Drush"
echo $'\n'

brew install drush

echo $'\n'
echo "Installing Composer"
echo $'\n'

brew install composer

echo $'\n'
echo "Installing Xdebug"
echo $'\n'

brew install php55-xdebug

echo $'\n'
echo "Adding Xdebug configuration to php.ini (php 5.5)"
echo $'\n'

cat >> $(brew --prefix)/etc/php/5.5/php.ini <<EOF
[xdebug]
xdebug.default_enable=1
xdebug.remote_enable=1
xdebug.remote_handler=dbgp
xdebug.remote_host=localhost
xdebug.remote_port=9000
xdebug.remote_autostart=1
; Needed for Drupal 8
xdebug.max_nesting_level = 256
EOF

echo $'\n'
echo "Installing Drupal Code Sniffer"
echo $'\n'

brew install drupal-code-sniffer

echo $'\n'
echo "Starting services"
echo $'\n'

launchctl load -Fw ~/Library/LaunchAgents/homebrew.mxcl.mysql.plist
launchctl load -Fw ~/Library/LaunchAgents/homebrew.mxcl.httpd22.plist
sudo launchctl load -Fw /Library/LaunchDaemons/co.echo.httpdfwd.plist
launchctl load -Fw ~/Library/LaunchAgents/homebrew.mxcl.dnsmasq.plist

echo $'\n'
echo "Dev environment setup complete"
echo $'\n'

exit
