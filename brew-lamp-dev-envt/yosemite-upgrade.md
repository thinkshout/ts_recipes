I ran into 3 gotchas after upgrading.

## XCode

First, after upgrading to Yosemite, do the following:

1. Upgrade XCode
2. Upgrade XCodeCLT
3. xcode-select --install

## Homebrew

If you upgrade Homebrew before installing, you might not have this issue. I had to rebuild the brew git tree. Simply `cd /usr/local`, delete the .git directory and run `brew update`. Bam, done.

## Reinstall PHP

`brew update && brew upgrade && brew reinstall php55` // or 53/54/56

## Apache

Yosemite ships with Apache 2.4, so it's a substantial update from 2.2. Your previous httpd.conf file will be backed up, named `httpd.conf~previous`.

Enable the following modules on uncommenting them"

* LoadModule ssl_module libexec/apache2/mod_ssl.so
* LoadModule vhost_alias_module libexec/apache2/mod_vhost_alias.so
* LoadModule dir_module libexec/apache2/mod_dir.so
* LoadModule alias_module libexec/apache2/mod_alias.so
* LoadModule rewrite_module libexec/apache2/mod_rewrite.so

And, of course, add your homebrew PHP module:
`LoadModule php5_module    /usr/local/opt/php55/libexec/apache2/libphp5.so`

In your Apache vhosts config file, you should have something like the following. Apache 2.4 [handles access control differently](http://httpd.apache.org/docs/2.4/upgrading.html) to 2.2.

```
<Directory "/Users/user/Sites/">
    Options Indexes MultiViews
    AllowOverride All
    Order allow,deny
    Allow from all
    Require all granted
</Directory>
```

Replace these lines:

```
Order allow,deny
Allow from all
```

With this:

```
Require all granted
```

That should do it. Restart apache, `sudo apachectl -k restart` and you're good to go.
