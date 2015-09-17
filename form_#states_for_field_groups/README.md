The following snippet can be used to conditionally hide a field group using the [Form API's #states control](https://api.drupal.org/api/drupal/developer!topics!forms_api_reference.html/7#states). An extensive description of this feature can be found at [drupal\_process\_states()](https://api.drupal.org/api/drupal/includes%21common.inc/function/drupal_process_states/7).

**Note**: this implementation uses a remote condition of "value" which isn't appropriate for all field types. It also assumes the form element used to trigger the remote condition was added to the form via the Field API; hence the `[und]` appended to the field name. If you're using a custom form field as the target of the remote condition, you won't need the `[und]` bit; just the name of the form element.
```php
/**
 * Implements hook_field_group_build_pre_render_alter().
 */
function yourmodule_field_group_build_pre_render_alter(&$element) {

  // Check whether the field group array exists.
  if (isset($element['group_name'])) {
    // Add elements required to get Drupal's jQuery to act on the field group.
    $element['group_name'] += array(
      // Add #states specifications.
      // See the following two links for more info on #states:
      // https://api.drupal.org/api/drupal/developer!topics!forms_api_reference.html/7#states
      // https://api.drupal.org/api/drupal/includes%21common.inc/function/drupal_process_states/7
      '#states' => array(
        'visible' => array(
          // This setup assumes a form element added via the Field API.
          'input[name="field_your_conditional_field[und]"]' => array('value' => 'somevalue'),
        ),
      ),
      // Create an HTML id for the field group so it can be targeted by jQuery.
      // Note: this MUST match the field group form element's name.
      '#id' => 'group_name',
    );
  }
}
```