<?php

// Prevent connection to the Production SF instance if not on the Production Pantheon server.
if (isset($_SERVER['PANTHEON_ENVIRONMENT']) && $_SERVER['PANTHEON_ENVIRONMENT'] === 'live') {
  $conf['salesforce_instance_url'] = 'INSTANCE_URL';
  $conf['salesforce_consumer_key'] = 'CONSUMER_KEY';
  $conf['salesforce_consumer_secret'] = 'CONSUMER_SECRET';
  $conf['salesforce_endpoint'] = 'https://login.salesforce.com';
}
elseif (isset($_SERVER['PANTHEON_ENVIRONMENT']) && $_SERVER['PANTHEON_ENVIRONMENT'] === 'test') {
  $conf['salesforce_instance_url'] = 'SANDBOX_INSTANCE_URL';
  $conf['salesforce_consumer_key'] = 'CONSUMER_KEY_SANDBOX';
  $conf['salesforce_consumer_secret'] = 'CONSUMER_SECRET_SANDBOX';
  $conf['salesforce_endpoint'] = 'https://test.salesforce.com';

}
else {
  // Enforce Sandbox endpoint on all other instances.
  $conf['salesforce_endpoint'] = 'https://test.salesforce.com';
}



// Prevent XMLSiteMap generation and submission
if ((isset($_SERVER['PANTHEON_ENVIRONMENT']) && $_SERVER['PANTHEON_ENVIRONMENT'] !== 'live')
  || !isset($_SERVER['PANTHEON_ENVIRONMENT'])){
  $conf['xmlsitemap_engines_engines'] = array();
  $conf['xmlsitemap_regenerate_needed'] = FALSE;
}



// Redis settings:
// http://helpdesk.getpantheon.com/customer/portal/articles/401317-understanding-redis-cache
// All Pantheon Environments.
if (defined('PANTHEON_ENVIRONMENT')) {
  // Use Redis for caching.
  $conf['redis_client_interface'] = 'PhpRedis';
  $conf['cache_backends'][] = 'profiles/PROFILE/modules/contrib/redis/redis.autoload.inc';
  $conf['cache_default_class'] = 'Redis_Cache';
  $conf['cache_prefix'] = array('default' => 'pantheon-redis');
  // Do not use Redis for cache_form (no performance difference).
  $conf['cache_class_cache_form'] = 'DrupalDatabaseCache';
  // Use Redis for Drupal locks (semaphore).
  $conf['lock_inc'] = 'profiles/PROFILE/modules/contrib/redis/redis.lock.inc';
}
