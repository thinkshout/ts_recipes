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

// Everything except live instance on pantheon.
if (!isset($_SERVER['PANTHEON_ENVIRONMENT']) || $_SERVER['PANTHEON_ENVIRONMENT'] !== 'live') {
  // Disable the connection to Mandrill to prevent any emails from being sent.
  $conf['mandrill_api_key'] = '';
}

// Only on a live pantheon environment.
if (isset($_SERVER['PANTHEON_ENVIRONMENT']) && $_SERVER['PANTHEON_ENVIRONMENT'] == 'live') {
  // Set the real mandrill key.
  $conf['mandrill_api_key'] = 'REAL_KEY_HERE';
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

// Simple redirects, for 404s.
$redirects = array(
  'old-1' => 'directory',
  'old-2.asp' => 'directory',
  'old-3.cfm' => 'home',
  'old-4' => '',
);
if (in_array($_GET['q'], array_keys($redirects))) {
  header('HTTP/1.0 301 Moved Permanently');
  header('Location: /' . $redirects[$_GET['q']]);
  exit();
}

// Handle any other 404s and prevent logging in watchdog..
// Paths are controlled by $conf['404_fast_paths'].
drupal_fast_404();
