## Installing SOLR

```
brew install solr
```

If you need solr 5.5 follow these instructions:
```
brew tap smithmilner/homebrew-solr-legacy
brew install solr@5.5
sudo ln -sfn /usr/local/opt/openjdk/libexec/openjdk.jdk /Library/Java/JavaVirtualMachines/openjdk.jdk
brew services start solr@5.5
```
