<?php
/**
 * @file
 * Displays a Header Alert bean.
 */

class FamiliesUsaHeaderAlert extends BeanPlugin {

  /**
   * Displays the bean.
   */
  public function view($bean, $content, $view_mode = 'default', $langcode = NULL) {

    // Render the bean how the BeanPlugin would (i.e. no modification).
    $content = parent::view($bean, $content, $view_mode, $langcode);

    // Attach custom js to conditionally show bean to only visitors who have not seen it.
    $content['#attached']['library'][] = array('system', 'jquery.cookie');
    $path = drupal_get_path('module', 'families_usa_beans_header_alert');
    $content['#attached']['js'][] = $path . '/js/fusa_header_alert.js';

//    $entity_wrapper = entity_metadata_wrapper('bean', $bean);
//    $cooldown_hours = $entity_wrapper->field_alert_cooldown_hours->value();

    // Display Front Page Alert bean.
//    $alert_settings = array(
//      'enabled' => true,
//      'hours' => $cooldown_hours,
//      'hours' => 1,
//    );

    // Don't display the bean if this isn't the front page or
    // no bean instances are enabled.
//    if (!drupal_is_front_page() || (!kidneys_get_front_page_alert_bean())) {
//      $alert_settings['enabled'] = false;
//    }

//    drupal_add_js(array('alert_settings' => $alert_settings), 'setting');

    return $content;
  }
}