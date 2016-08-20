This will install solr36 because Pantheon only supports solr 3. (But if you really want to [install solr4...](#install-solr4))

## Install required packages via Homebrew

```
brew unlink solr
brew unlink solr4
brew tap homebrew/versions
brew install Caskroom/cask/java
brew install solr36
```

Note: If homebrew cannot find a tap for solr36, you can manually download the file from a repo like: https://github.com/paulirish/homebrew-versions-1/blob/master/solr36.rb 

and put it at `/usr/local/Library/Taps/homebrew/homebrew-versions/solr36.rb` 

This change would usually be overwritten by a `brew update` or a `brew upgrade`, but if you add the file to your git excludes, it should be safe. To do this, add the following line to the bottom of `/usr/local/Library/Taps/homebrew/homebrew-versions/.git/info/exclude`

```
solr36.rb
```

You should now be able to run `brew install solr36` as recommended above.

## Once you've got solr installed
For the solr configuration we need to copy some Drupal settings. These settings configure solr to work with fieldtypes from Drupal among other stuff.

#### Get the latest version of the ```search_api_solr``` module.

Get the module from the [search_api_solr Drupal.org page](https://www.drupal.org/project/search_api_solr)

```cd ~;drush dl search_api_solr```

This module contains config files we'll be copying to our Solr installation.

#### Modify the solr installation with Drupal config

Copy the configuration files from the ```search_api_solr``` module to the example Jetty Solr application.
```
cp ~/search_api_solr/solr-conf/3.x/* /usr/local/etc/solr36/multicore/core0/conf
cp ~/search_api_solr/solr-conf/3.x/* /usr/local/etc/solr36/multicore/core1/conf
```

## Running the server
```
ln -sfv /usr/local/opt/solr36/*.plist ~/Library/LaunchAgents
launchctl load ~/Library/LaunchAgents/homebrew.mxcl.solr36.plist 
```

If you see the [solr admin page](http://localhost:8983/solr/core0/admin/) everything is working!

## Tika
If your search indexes attachments, you will also have to install tika:
```
brew install tika
```

## Drupal config
Once your server is up and running do the following to configure a search_api server for your site.

1. Ensure ```search_api``` and ```search_api_solr``` modules are installed
2. Head to ```/admin/config/search/search_api```
3. Click ```+ Add server```
4. Use the following credentials for the solr service:
	* solr host: localhost
	* solr port: 8983
	* solr path: /solr/core0
5. Leave the Basic HTTP Authentication and Advanced sections blank, and save your settings.
6. If you installed tika, edit `/admin/config/search/search_api/attachments` and update the tika directory path and tika jar file settings, e.g.: `/usr/local/Cellar/tika/1.11/libexec/` and `tika-app-1.11.jar`
 
## Enable multicore server (for multiple local sites)
If you with to run multiple websites at the same time, you may want to run multiple cores. You could reuse the same core, but you will have to reindex when you switch between projects. To create these cores, edit the solr.xml file...
```
subl /usr/local/etc/solr36/multicore/solr.xml
```
Remove everything from the file and replace it with the codeblock below.

You will need to create a core element for every search core you wish to create. Both element attributes (name and instanceDir) should be the actual dir name.
```
<?xml version="1.0" encoding="UTF-8" ?>
  <solr persistent="false">
    <cores adminPath="/admin/cores">
      <core name="core0" instanceDir="core0" />
      <core name="core1" instanceDir="core1" />
      <core name="newsite_core" instanceDir="newsite_core" />
    </cores>
  </solr>
```
#### Create a core directory for each site
Inside ```/usr/local/etc/solr36/multicore/``` you will see directories for your initial "Cores".

Otherwise, the step below should be repeated for each search core. Every core instance should have its own dir (e.g. site1, site2, www_whatever_com) and needs a copy of the complete solr/conf dir in its root. To save time, you can duplicate one of the directories that already has this configuration to create your new core.

```
cp -R /usr/local/etc/solr36/multicore/core1 /usr/local/etc/solr36/multicore/newsite_core
```

## Conflicts with Pantheon's pantheon_apachesolr module
Once you enable the pantheon_apachesolr module it overrides any connection info for any solr server used. You will need to disable pantheon_apachesolr to have a local solr server work, or you can add the following line to your local.settings.php if you want to use the existing search api server for a project:

```
$conf['pantheon_apachesolr_search_api_solr_service_class'] = 'SearchApiSolrService';
```


## Empty facets or search results showing up

You may need to force clear your index. the Clear all indexed data button on the Search API index page only clears the items that the site knows about. This means we often end up with many items the site doesn't know about in the index. These curl commands will clear your index for a particular core. Replace ```core0``` with your actual core name if you've changed it.

```
curl http://localhost:8983/solr/core0/update --data '<delete><query>*:*</query></delete>' -H 'Content-type:text/xml; charset=utf-8'
curl http://localhost:8983/solr/core0/update --data '<commit/>' -H 'Content-type:text/xml; charset=utf-8'
```

## Restart your process

```
brew services restart solr36
```

## Install solr4

Replace PROJECT with the name of your project. Solr core will live at http://localhost:8983/solr/PROJECT

```
brew tap homebrew/versions
brew install solr4
brew services start homebrew/versions/solr4
mkdir /usr/local/opt/solr4/example/solr/PROJECT
mkdir /usr/local/opt/solr4/example/solr/PROJECT/conf
cp search_api_solr/solr-conf/4.x/* /usr/local/opt/solr4/example/solr/PROJECT/conf
```

- Launch [http://localhost:8983/solr/](http://localhost:8983/solr/)
- Click Core Admin
- Click Add Core
- name: PROJECT
- instanceDir: PROJECT
