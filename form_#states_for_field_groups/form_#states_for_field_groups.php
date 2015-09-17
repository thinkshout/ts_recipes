/**
 * Implements hook_field_group_build_pre_render_alter().
 */
function yourmodule_field_group_build_pre_render_alter(&$element) {

  // Check whether the group array exists.
  if (isset($element['group_name'])) {
    // Add elements required to get Drupal's jQuery to act on the field group.
    $element['group_name'] += array(
      // Add #states specifications.
      '#states' => array(
        'visible' => array(
          // This setup assumes a custom form element.
          'input[name="field_account_type[und]"]' => array('value' => 'somevalue'),
        ),
      ),
      // Create an HTML id for the field group so it can be targeted by jQuery.
      '#id' => 'group_name',
    );
  }
}