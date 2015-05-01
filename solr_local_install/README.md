This will install the latest solr release, but Pantheon only supports solr 3.5.0. Luckily there's a brew ```solr36``` formula that works although you'll have to tinker a bit with the following instructions. Start by using ```brew install solr36``` to install the 3.6 version of Solr.

## Install required packages via Homebrew

```
brew install solr
```

If you get an error like...
> curl: (22) The requested URL returned error: 404
Error: Failed to download resource "solr"
Download failed: http://mirrors.koehn.com/apache/lucene/solr/4.9.0/solr-4.9.0.tgz

...you will have to edit your solr formula.

#### Editing solr formula
Use ```brew edit solr``` to open the solr formula, and change the ```url``` and ```sha1``` lines to reflect the url and sha1 of a differnt download mirror. I visited the [Apache Solr mirrors page](http://www.apache.org/dyn/closer.cgi/lucene/solr/), and came up with the following:
```
url 'http://www.bizdirusa.com/mirrors/apache/lucene/solr/4.10.0/solr-4.10.0.tgz'
sha1 'ae47a89f35b5e2a6a4e55732cccc64fb10ed9779'
```

## Once you've got solr installed
For the solr configuration we need to copy some Drupal settings. These settings configure solr to work with fieldtypes from Drupal among other stuff.

#### Get the latest version of the ```search_api_solr``` module.

Get the module from the [search_api_solr Drupal.org page](https://www.drupal.org/project/search_api_solr)

```wget http://ftp.drupal.org/files/projects/search_api_solr-7.x-1.6.tar.gz```

Extract the file

```tar xf search_api_solr-7.x-1.6.tar.gz```

This module contains ```schema.xml``` and ```solrconfig.xml``` files we'll be copying to our solr installatinon.

#### Modify the solr installation with Drupal config

Go to where homebrew has installed Solr, change ```<VER.SI.ON>``` to the actual version number, in this example the version is 4.10.0.
```cd $(brew --prefix)/Cellar/solr/<VER.SI.ON>/libexec```

Make a copy of the provided example dir
```
cp -r example drupal
cd drupal
```
Copy the ```conf``` directory from the example collection into the ```solr``` dir and cd there as this is where the configuration files we want to modify are. Skip this step if you're using solr36.
```
cp -r solr/collection1/conf ./solr
cd solr/conf
```
Make copies of the configuration files that will be overwritten
```
cp schema.xml schema.xml.bak
cp solrconfig.xml solrconfig.xml.bak
```
Pull in new copies of configuration files from the ```search_api_solr``` module. If using Solr 4.x use the 4.x conf files, otherwise use the 3.x conf files.
```
cp search_api_solr/solr-conf/4.x/*.xml .
```
Head back to the ```drupal``` dir that is a copy of ```example```
```
cd ../../
```
#### Enable multicore server (for multiple local sites)
You'll need a solr core for each site you wish to index locally. To create these cores, edit the solr.xml file...
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

## Running the server
Everything is configured and ready to launch. Head to  the ```drupal``` dir we created.
```
cd $(brew --prefix)/Cellar/solr/<VER.SI.ON>/libexec/drupal
```
Next, start the solr server with the following command. Note that if you create new cores, or alter the configuration in any other way, you will have to kill the process and fire the command again.
```
java -jar start.jar
```
If you see the [solr admin page](http://localhost:8983/solr) everything is working!

#### Troubleshooting server issues
If you do not see the [solr admin page](http://localhost:8983/solr) it's possible your java version is incompatible with the version of solr you're running.

To fix this you can either...

1. Change the version of solr you're running to one that is compatible with your current java version. To check your java version run ```java -version```.
2. Change your java version to be compatible with the version of solr you're running. I ran into this issue as OSX 10.8 comes with java 6 and [solr 4.10.0 is only compatible with java 7 or greater](http://lucene.apache.org/core/4_10_0/SYSTEM_REQUIREMENTS.html). To resolve this issue I installed the latest version of java from [Oracle's java download page](http://www.oracle.com/technetwork/java/javase/downloads/index.html).

## Drupal config
Once your server is up and running do the following to configure a search_api server for your site.

1. Ensure ```search_api``` and ```search_api_solr``` modules are installed
2. Head to ```/admin/config/search/search_api```
3. Click ```+ Add server```
4. Use the following credentials for the solr service:
	* solr host: localhost
	* solr port: 8983
	* solr path: /solr/\<name\_of\_site\> (e.g. /solr/site1)
5. Leave the Basic HTTP Authentication and Advanced sections blank, and save your settings.

## Conflicts with Pantheon's pantheon_apachesolr module
Once you enable the pantheon_apachesolr module it overrides any connection info for any solr server used. You will need to disable pantheon_apachesolr to have a local solr server work.


## Empty facets or search results showing up

You may need to force clear your index. the Clear all indexed data button on the Search API index page only clears the items that the site knows about. This means we often end up with many items the site doesn't know about in the index. These curl commands will clear your index for a particular core. Replace ```<CORE>``` with your actual core name.

```
curl http://localhost:8983/solr/<CORE>/update --data '<delete><query>*:*</query></delete>' -H 'Content-type:text/xml; charset=utf-8'
curl http://localhost:8983/solr/<CORE>/update --data '<commit/>' -H 'Content-type:text/xml; charset=utf-8'
```
