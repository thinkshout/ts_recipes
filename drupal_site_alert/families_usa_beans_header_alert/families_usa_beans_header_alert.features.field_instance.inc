<?php
/**
 * @file
 * families_usa_beans_header_alert.features.field_instance.inc
 */

/**
 * Implements hook_field_default_field_instances().
 */
function families_usa_beans_header_alert_field_default_field_instances() {
  $field_instances = array();

  // Exported field_instance: 'bean-header_alert-field_body'
  $field_instances['bean-header_alert-field_body'] = array(
    'bundle' => 'header_alert',
    'default_value' => NULL,
    'deleted' => 0,
    'description' => '',
    'display' => array(
      'default' => array(
        'label' => 'hidden',
        'module' => 'text',
        'settings' => array(),
        'type' => 'text_default',
        'weight' => 2,
      ),
    ),
    'entity_type' => 'bean',
    'fences_wrapper' => 'div',
    'field_name' => 'field_body',
    'label' => 'Body',
    'required' => 0,
    'settings' => array(
      'display_summary' => 0,
      'text_processing' => 1,
      'user_register_form' => FALSE,
    ),
    'widget' => array(
      'active' => 1,
      'module' => 'text',
      'settings' => array(
        'rows' => 20,
        'summary_rows' => 5,
      ),
      'type' => 'text_textarea_with_summary',
      'weight' => 6,
    ),
  );

  // Translatables
  // Included for use with string extractors like potx.
  t('Body');

  return $field_instances;
}
