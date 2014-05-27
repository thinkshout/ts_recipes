
## Install required packages via HomeBrew

#### First, Homebrew
```bash
ruby -e "$(curl -fsSL https://raw.github.com/Homebrew/homebrew/go/install)"`
```
#### Git
```bash
brew install git
```
#### Drush
```bash
brew install drush
```
#### Composer
```bash
brew install composer
```
#### LAMP
```bash
brew install php55
```

```bash
brew install mysql
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
mkdir ~/Sites/logs && chmod 0777 ~/Sites/logs
```

#### Optionally Setup DNSMasq
See http://fourkitchens.com/blog/2014/05/16/local-development-apache-vhosts-and-dnsmasq.

#### Restart Apache for changes to take effect

```bash
sudo apachectl restart
```

### Add entries to hosts file if _not_ using dynamic vhosts

```bash
sudo nano /private/etc/hosts
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
