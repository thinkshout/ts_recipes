## Installing SOLR

- Replace CORENAME with the name of your solr core.
- Replace PROJECT with the name of your project.
- Solr core will live at http://localhost:8983/solr/#/CORENAME

```
brew install solr
brew tap smithmilner/homebrew-solr-legacy
brew install solr@5.5
sudo ln -sfn /usr/local/opt/openjdk/libexec/openjdk.jdk /Library/Java/JavaVirtualMachines/openjdk.jdk
brew services start solr@5.5

mkdir -p /usr/local/opt/solr\@5.5/server/solr/CORENAME/conf
cp ~/Sites/PROJECT/web/modules/contrib/search_api_solr/solr-conf/5.x/* /usr/local/opt/solr\@5.5/server/solr/CORENAME/conf
```

- Launch [http://localhost:8983/solr/](http://localhost:8983/solr/)
- Click Core Admin
- Click Add Core
- name: CORENAME
- instanceDir: CORENAME
