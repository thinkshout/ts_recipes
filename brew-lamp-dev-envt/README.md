
# OSX 10.10

- Homebrew
- Git
- MySQL
- Apache
- PHP
- Dnsmasq

## Consider using the environment script

https://github.com/thinkshout/ts_recipes/blob/master/environment_setup.sh

## Install required packages via HomeBrew

Parts of this documentation yanked from
https://echo.co/blog/os-x-1010-yosemite-local-development-environment-apache-php-and-mysql-homebrew 
and
https://echo.co/blog/os-x-109-local-development-environment-apache-php-and-mysql-homebrew

#### First, ensure you have Xcode cli tools installed
```bash
xcode-select -p
```

If it returns a path, move to the next step. If not, install Xcode from the App Store, or try:
```bash
xcode-select --install
``` 

#### Homebrew
```bash
ruby -e "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/master/install)"
```

Check your Homebrew installation with ```brew doctor``` before continuing.

#### Git
```bash
brew install git
```

### Set up SSH keys
https://help.github.com/articles/generating-ssh-keys/

#### Set up homebrew taps

```bash
brew tap homebrew/dupes
brew tap homebrew/homebrew-php
```

#### MySQL

Install MySQL - paste these commands one at a time (the multi-line blocks are like one big line):

```bash
brew install mysql
```

MySQL config:
```bash
cp -v $(brew --prefix mysql)/support-files/my-default.cnf $(brew --prefix)/etc/my.cnf
```

```bash
cat >> $(brew --prefix)/etc/my.cnf <<'EOF'

# Echo & Co. changes
max_allowed_packet = 1073741824
innodb_file_per_table = 1
EOF
```

```bash
sed -i '' 's/^#[[:space:]]*\(innodb_buffer_pool_size\)/\1/' $(brew --prefix)/etc/my.cnf
```

Add launch agent:
```bash
[[ ! -d ~/Library/LaunchAgents ]] && mkdir -v ~/Library/LaunchAgents
```

```bash
ln -sfv $(brew --prefix mysql)/homebrew.mxcl.mysql.plist ~/Library/LaunchAgents/
```

And start it:
```bash
launchctl load -Fw ~/Library/LaunchAgents/homebrew.mxcl.mysql.plist
```

To secure your MySQL installation:
```bash
$(brew --prefix mysql)/bin/mysql_secure_installation
```

#### Apache

Stop the existing apache, if it's running:
```bash
sudo launchctl unload /System/Library/LaunchDaemons/org.apache.httpd.plist 2>/dev/null
```

Install apache - paste these commands one at a time (the giant blocks are one big line):

```bash
brew install -v httpd22 --with-brewed-openssl
```

This is where you'll put your sites:
```bash
[ ! -d ~/Sites ] && mkdir -pv ~/Sites
mkdir -pv ~/Sites/{logs,ssl}
```

Configure VirtualHosts:
```bash
touch ~/Sites/httpd-vhosts.conf
```

```bash
USERHOME=$(dscl . -read /Users/`whoami` NFSHomeDirectory | awk -F": " '{print $2}') cat >> $(brew --prefix)/etc/apache2/2.2/httpd.conf <<EOF
# Include our VirtualHosts
Include ${USERHOME}/Sites/httpd-vhosts.conf
EOF
```

```bash
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
```

Set up SSL:

```bash
(export USERHOME=$(dscl . -read /Users/`whoami` NFSHomeDirectory | awk -F"\: " '{print $2}') ; cat > ~/Sites/ssl/ssl-shared-cert.inc <<EOF
SSLEngine On
SSLProtocol all -SSLv2 -SSLv3
SSLCipherSuite ALL:!ADH:!EXPORT:!SSLv2:RC4+RSA:+HIGH:+MEDIUM:+LOW
SSLCertificateFile "${USERHOME}/Sites/ssl/selfsigned.crt"
SSLCertificateKeyFile "${USERHOME}/Sites/ssl/private.key"
EOF
)
```

```bash
openssl req \
  -new \
  -newkey rsa:2048 \
  -days 3650 \
  -nodes \
  -x509 \
  -subj "/C=US/ST=State/L=City/O=Organization/OU=$(whoami)/CN=*.dev" \
  -keyout ~/Sites/ssl/private.key \
  -out ~/Sites/ssl/selfsigned.crt
```

Start apache:
```bash
ln -sfv $(brew --prefix httpd22)/homebrew.mxcl.httpd22.plist ~/Library/LaunchAgents
```

```bash
launchctl load -Fw ~/Library/LaunchAgents/homebrew.mxcl.httpd22.plist
```

Forward traffic from port 80:

```bash
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
```

```bash
sudo launchctl load -Fw /Library/LaunchDaemons/co.echo.httpdfwd.plist
```

#### PHP
```bash
brew install php55 --homebrew-apxs --with-apache
```

Hook PHP up to apache:
```bash
cat >> $(brew --prefix)/etc/apache2/2.2/httpd.conf <<EOF
# Send PHP extensions to mod_php
AddHandler php5-script .php
AddType text/html .php
DirectoryIndex index.php index.html
EOF
```

Some initial php.ini settings:
```bash
sed -i '-default' "s|^;\(date\.timezone[[:space:]]*=\).*|\1 \"$(sudo systemsetup -gettimezone|awk -F": " '{print $2}')\"|; s|^\(memory_limit[[:space:]]*=\).*|\1 256M|; s|^\(post_max_size[[:space:]]*=\).*|\1 200M|; s|^\(upload_max_filesize[[:space:]]*=\).*|\1 100M|; s|^\(default_socket_timeout[[:space:]]*=\).*|\1 600|; s|^\(max_execution_time[[:space:]]*=\).*|\1 300|; s|^\(max_input_time[[:space:]]*=\).*|\1 600|;" $(brew --prefix)/etc/php/5.5/php.ini
```

Configure error logging:
```bash
USERHOME=$(dscl . -read /Users/`whoami` NFSHomeDirectory | awk -F": " '{print $2}') cat >> $(brew --prefix)/etc/php/5.5/php.ini <<EOF
; PHP Error log
error_log = ${USERHOME}/Sites/logs/php-error_log
EOF
```

Fix a permissions problem with PEAR:
```bash
touch $(brew --prefix php55)/lib/php/.lock && chmod 0644 $(brew --prefix php55)/lib/php/.lock
```

Enable opcache:
```bash
/usr/bin/sed -i '' "s|^\(\;\)\{0,1\}[[:space:]]*\(opcache\.enable[[:space:]]*=[[:space:]]*\)0|\21|; s|^;\(opcache\.memory_consumption[[:space:]]*=[[:space:]]*\)[0-9]*|\1256|;" $(brew --prefix)/etc/php/5.5/php.ini
```

Cross your fingers and restart apache

```bash
apachectl restart
```

#### Dnsmasq

```bash
brew install dnsmasq
```

```bash
echo 'address=/.dev/127.0.0.1' > $(brew --prefix)/etc/dnsmasq.conf
echo 'listen-address=127.0.0.1' >> $(brew --prefix)/etc/dnsmasq.conf
echo 'port=35353' >> $(brew --prefix)/etc/dnsmasq.conf
```

```bash
ln -sfv $(brew --prefix dnsmasq)/homebrew.mxcl.dnsmasq.plist ~/Library/LaunchAgents
```

```bash
launchctl load -Fw ~/Library/LaunchAgents/homebrew.mxcl.dnsmasq.plist
```

```bash
sudo mkdir -v /etc/resolver 
sudo bash -c 'echo "nameserver 127.0.0.1" > /etc/resolver/dev'
sudo bash -c 'echo "port 35353" >> /etc/resolver/dev'
```

#### Test it

You shouldn't need to edit the Apache configuration or edit /etc/hosts for new local development sites. Simply create a directory in ~/Sites and then reference "http://" + that foldername + ".dev/" in your browser to access it.

For example, download Drupal 7 to the directory ~/Sites/firstproject, and it can then be accessed at http://firstproject.dev/ without any additional configuration. A caveat - you will need to uncomment the line in Drupal's .htaccess containing "RewriteBase /" to work with the auto-VirtualHosts configuration.

#### Drush
```bash
brew install drush
```

##### Or for Drush 7
```bash
brew install --HEAD drush
brew switch drush HEAD
```

#### Composer
```bash
brew install composer
```

#### Xdebug
```bash
brew install php55-xdebug
```

edit `/usr/local/etc/php/5.5/php.ini` by inserting this at the bottom:
```bash
[xdebug]
zend_extension="/usr/local/Cellar/php55-xdebug/2.3.2/xdebug.so"

xdebug.default_enable=1
xdebug.remote_enable=1
xdebug.remote_handler=dbgp
xdebug.remote_host=localhost
xdebug.remote_port=9000
xdebug.remote_autostart=1
; Needed for Drupal 8
xdebug.max_nesting_level = 250
```

#### Drupal Code Sniffer
```bash
brew install drupal-code-sniffer
```

## Gotchas to keep in mind

### MySql
May complain about concurrent connections. Add a .cnf file at `/usr/local/etc/my.cnf` containing at least:

```
[mysqld]
max_connections=151
```
See: http://dev.mysql.com/doc/refman/5.5/en/too-many-connections.html

### Switching PHP versions

See: https://github.com/Homebrew/homebrew-php#installing-multiple-versions

## Additional reading
* http://thinkshout.com/blog/2013/01/gabe/drupal-development-la-thinkshout-setting-your-environment/
* http://fourkitchens.com/blog/2014/05/16/local-development-apache-vhosts-and-dnsmasq
