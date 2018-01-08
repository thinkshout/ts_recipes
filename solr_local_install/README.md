## Installing SOLR

Replace PROJECT with the name of your project. Solr core will live at http://localhost:8983/solr/PROJECT

```
brew install solr55
brew services start homebrew/versions/solr55
mkdir /usr/local/opt/solr\@5.5/server/solr/PROJECT
mkdir /usr/local/opt/solr\@5.5/server/solr/PROJECT/conf
cp search_api_solr/solr-conf/5.x/* /usr/local/opt/solr\@5.5/server/solr/PROJECT/conf
```

- Launch [http://localhost:8983/solr/](http://localhost:8983/solr/)
- Click Core Admin
- Click Add Core
- name: PROJECT
- instanceDir: PROJECT
