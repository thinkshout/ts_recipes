<?php
/**
 * @file
 * families_usa_beans_header_pop_up.bean.inc
 */

/**
 * Implements hook_bean_admin_ui_types().
 */
function families_usa_beans_header_pop_up_bean_admin_ui_types() {
  $export = array();

  $bean_type = new stdClass();
  $bean_type->disabled = FALSE; /* Edit this to true to make a default bean_type disabled initially */
  $bean_type->api_version = 5;
  $bean_type->name = 'header_pop_up';
  $bean_type->label = 'Header Pop-up';
  $bean_type->options = '';
  $bean_type->description = 'Will show at the top of any page the first time a user visits the site';
  $export['header_pop_up'] = $bean_type;

  return $export;
}
