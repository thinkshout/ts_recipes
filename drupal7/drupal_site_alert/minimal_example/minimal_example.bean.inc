<?php
/**
 * @file
 * minimal_example.bean.inc
 */

/**
 * Implements hook_bean_admin_ui_types().
 */
function minimal_example_bean_admin_ui_types() {
  $export = array();

  $bean_type = new stdClass();
  $bean_type->disabled = FALSE; /* Edit this to true to make a default bean_type disabled initially */
  $bean_type->api_version = 5;
  $bean_type->name = 'header_alert';
  $bean_type->label = 'Header Alert';
  $bean_type->options = '';
  $bean_type->description = 'Will show at the top of any page the first time a user visits the site';
  $export['header_alert'] = $bean_type;

  return $export;
}
