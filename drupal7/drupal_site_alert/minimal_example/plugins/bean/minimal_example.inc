<?php
/**
 * @file
 * Displays a Header Pop-up bean.
 */

class MinimalExampleAlert extends BeanPlugin {

  /**
   * Displays the bean.
   */
  public function view($bean, $content, $view_mode = 'default', $langcode = NULL) {

    // Render the bean how the BeanPlugin would (i.e. no modification).
    $content = parent::view($bean, $content, $view_mode, $langcode);

    // Attach custom js to conditionally show bean to only visitors who have not seen it.
    $content['#attached']['library'][] = array('system', 'jquery.cookie');
    $path = drupal_get_path('module', 'minimal_example');
    $content['#attached']['js'][] = $path . '/js/minimal_example.js';

//    // Load alert bean.
//    $entity_wrapper = entity_metadata_wrapper('bean', $bean);
//    // Get number of hours before cookie expires from field on bean.
//    $expire_hours = $entity_wrapper->field_alert_expire_hours->value();
//
//    // Settings
//    $alert_settings = array(
//      'hours' => $expire_hours,
//    );
//
//    // Add JS settings to Drupal render array.
//    $content['#attached']['js'][] = array(
//      'data' => array(
//        'alert_settings' => $alert_settings,
//      ),
//      'type' => 'setting',
//    );


    return $content;
  }
}