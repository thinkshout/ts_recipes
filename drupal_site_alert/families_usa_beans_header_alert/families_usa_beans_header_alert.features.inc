<?php
/**
 * @file
 * families_usa_beans_header_alert.features.inc
 */

/**
 * Implements hook_ctools_plugin_api().
 */
function families_usa_beans_header_alert_ctools_plugin_api($module = NULL, $api = NULL) {
  if ($module == "bean_admin_ui" && $api == "bean") {
    return array("version" => "5");
  }
}
