<?php

/**
 * Implements hook_theme().
 */
function MODULE_theme() {
  return array(
    'MODULE_extra_field' => array(
      'variables' => array(
        'label_hidden' => FALSE,
        'title_attributes' => NULL,
        'label' => '',
        'content_attributes' => NULL,
        'items' => array(),
        'item_attributes' => array(0 => ''),
        'classes' => '',
        'attributes' => '',
      ),
    ),
  );
}

/**
 * Theme function for extra fields.
 *
 * Simple wrapper around theme_field that sets default values and ensures
 * properties render consistently with fields.
 */
function theme_MODULE_extra_field($variables) {
  return theme_field($variables);
}
