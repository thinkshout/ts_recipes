# Add this to your .bash_profile or other shell file, and then you can call 'tbg [sitename]' to download
# the latest db backup from pantheon in one step
tbg() {
  dburl=$(terminus site backups get --site="$1" --env="live" --latest --element="db"); 
  wget -Odatabase.sql.gz "$dburl" ; 
  echo "Unzipping file..."; 
  gunzip -f database.sql.gz ;
}
