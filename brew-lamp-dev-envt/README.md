
## Install required packages via HomeBrew

#### First, Homebrew
```bash
ruby -e "$(curl -fsSL https://raw.github.com/Homebrew/homebrew/go/install)"`
```

Check your Homebrew installation with ```bash brew doctor``` before continuing.

#### Git
```bash
brew install git
```

#### Set up homebrew taps

At this point, we need to tell Homebrew where to look for formulae.

```bash
brew tap homebrew/dupes
brew tap homebrew/homebrew-php
```

#### Drush
```bash
brew install drush
```
#### LAMP
```bash
brew install php55
```

```bash
brew install mysql
```

##### Connect Apache to homebrew-php

Homebrew doesn't touch your Apache config files, so we'll need to point it to our new php install manually.

```bash
nano /etc/apache2.conf
```

Find the line ```#LoadModule php5_module libexec/apache2/libphp5.so``` and make sure that it's commented out.  Three lines below, insert the following:

```
# Homebrew LAMP stack customizations
LoadModule php5_module    /usr/local/opt/php55/libexec/apache2/libphp5.so
```

##### Connect shell to homebrew-php

Find the rc file for your shell (```~/.bashrc``` for bash, or ```~/.zshrc``` for zsh), find the last `export PATH="xxxxxxxx"` line, and add the following below.

```bash
export PATH="#{HOMEBREW_PREFIX}/bin:$PATH"
```

#### Composer
```bash
brew install composer
```

#### Xdebug
```bash
brew install php55-xdebug
```

#### Drupal Code Sniffer
```bash
brew install drupal-code-sniffer
```

## Set up Apache VHosts

### Create vhosts file

Create a vhosts file located at `~/Sites/httpd-vhosts.conf` using one of the two templates included in this directory.
```
touch ~/Sites/httpd-vhosts.conf
```

#### Create a symbolic link for our previously created conf file.

```bash
sudo ln -s ~/Sites/httpd-vhosts.conf /etc/apache2/other
```

#### Setup the logs directory, and set it’s permissions.

```bash
mkdir ~/Sites/_logs && chmod 0777 ~/Sites/_logs
```

#### Optionally Setup DNSMasq
See http://fourkitchens.com/blog/2014/05/16/local-development-apache-vhosts-and-dnsmasq.

#### Restart Apache for changes to take effect

```bash
sudo apachectl restart
```

### Add entries to hosts file if _not_ using dynamic vhosts

```bash
sudo nano /etc/hosts
```
Add the following line:

```bash
127.0.0.1 site.local
```

## Gotchas to keep in mind

### MySql
May complain about concurrent connections. Add a .cnf file at `/usr/local/etc/my.cnf` containing at least:

```
[mysqld]
max_connections=10
```

## Additional reading
* http://thinkshout.com/blog/2013/01/gabe/drupal-development-la-thinkshout-setting-your-environment/
* http://fourkitchens.com/blog/2014/05/16/local-development-apache-vhosts-and-dnsmasq
