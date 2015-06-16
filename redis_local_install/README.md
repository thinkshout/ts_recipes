

## Install required packages via Homebrew

```
brew install redis
brew install php55-redis
```

Set redis to run automatically:

```
cp /usr/local/Cellar/redis/<VE.RS.ION>/homebrew.mxcl.redis.plist ~/Library/LaunchAgents
launchctl load -w ~/Library/LaunchAgents/homebrew.mxcl.redis.plist
```

Add the following to your settings.php (or local.settings.php if you're using one):

```php
// Redis settings:
// http://helpdesk.getpantheon.com/customer/portal/articles/401317-understanding-redis-cache
$conf['redis_client_interface'] = 'PhpRedis';
$conf['cache_backends'][] = 'profiles/<INSTALL PROFILE>/modules/contrib/redis/redis.autoload.inc';
$conf['cache_default_class'] = 'Redis_Cache';
$conf['cache_prefix'] = array('default' => 'pantheon-redis');
// Do not use Redis for cache_form (no performance difference).
$conf['cache_class_cache_form'] = 'DrupalDatabaseCache';
// Use Redis for Drupal locks (semaphore).
$conf['lock_inc'] = 'profiles/<INSTALL PROFILE>/modules/contrib/redis/redis.lock.inc';

$conf['redis_client_host'] = 'localhost';
$conf['redis_client_port'] = '6379';
$conf['redis_client_password'] = '';
```
