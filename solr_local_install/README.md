This will install solr36 because Pantheon only supports solr 3.

## Install required packages via Homebrew

```
brew unlink solr
brew unlink solr4
brew tap homebrew/versions
brew install Caskroom/cask/java
brew install solr36
```

## Once you've got solr installed
For the solr configuration we need to copy some Drupal settings. These settings configure solr to work with fieldtypes from Drupal among other stuff.

#### Get the latest version of the ```search_api_solr``` module.

Get the module from the [search_api_solr Drupal.org page](https://www.drupal.org/project/search_api_solr)

```cd ~;drush dl search_api_solr```

This module contains config files we'll be copying to our Solr installatinon.

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

#### Enable multicore server (for multiple local sites)
If you with to run multiple websites at the same time, you may want to run multiple cores. You could reuse the same core, but you will have to reindex when you switch between projects. To create these cores, edit the solr.xml file...
```
subl solr/solr.xml
```
Remove everything from the file and replace it with the codeblock below.

You will need to create a core element for every search core you wish to create. Both element attributes (name and instanceDir) should be the actual dir name.
```
<?xml version="1.0" encoding="UTF-8" ?>
  <solr persistent="false">
    <cores adminPath="/admin/cores">
      <core name="site1" instanceDir="site1" />
      <core name="site2" instanceDir="site2" />
    </cores>
  </solr>
```
#### Create a core directory for each site
The step below should be repeated for each search core. Every core instance should have it’s own dir (e.g. site1, site2, www_whatever_com) and needs a copy the complete solr/conf dir in its root.
```
mkdir solr/site1
cp -r solr/conf solr/site1/
cp -r solr/conf solr/site2/
```

## Conflicts with Pantheon's pantheon_apachesolr module
Once you enable the pantheon_apachesolr module it overrides any connection info for any solr server used. You will need to disable pantheon_apachesolr to have a local solr server work.


## Empty facets or search results showing up

You may need to force clear your index. the Clear all indexed data button on the Search API index page only clears the items that the site knows about. This means we often end up with many items the site doesn't know about in the index. These curl commands will clear your index for a particular core. Replace ```core0``` with your actual core name if you've changed it.

```
curl http://localhost:8983/solr/core0/update --data '<delete><query>*:*</query></delete>' -H 'Content-type:text/xml; charset=utf-8'
curl http://localhost:8983/solr/core0/update --data '<commit/>' -H 'Content-type:text/xml; charset=utf-8'
```
