<?php
/**
 * This recipe helps you isolate a live Salesforce instance from accidentally getting connected to a development site.
 * 
 * First, if you are on a version of the Salesforce module BEFORE 7.x-3.3, you will need the latest patch from this issue:
 * 
 * https://www.drupal.org/node/2900041
 * 
 * Then, you need to add the following to your settings.php (or, more likely, a site.settings.php):
 */

$conf['salesforce_endpoint'] = 'https://test.salesforce.com';

if (defined('PANTHEON_ENVIRONMENT')) {
  // Prevent connection to the Production SF instance if not on the Production
  // Pantheon server.
  if (PANTHEON_ENVIRONMENT == 'live') {
    $conf['salesforce_endpoint'] = 'https://login.salesforce.com';
  }
}
