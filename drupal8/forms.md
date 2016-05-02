# Forms

## Collapsible Fieldsets

`#collapsible` and `#collapsed` are [no longer valid in Drupal 8](https://www.drupal.org/node/1852020).

Instead, set `#type` to `details` and `#open` to `TRUE` or `FALSE`

```php
<?php
$form['advanced'] = array(
  '#type' => 'details',
  '#title' => t('Advanced settings'),
  '#description' => t('Lorem ipsum.'),
  '#open' => TRUE, // Controls the HTML5 'open' attribute. Defaults to FALSE.
);
?>
```

## Submit Buttons

Submit buttons are no longer part of the form render array. Add or edit submit buttons by overriding [EntityForm::actions](https://api.drupal.org/api/drupal/core!lib!Drupal!Core!Entity!EntityForm.php/function/EntityForm%3A%3Aactions/8)

***Change the Submit Button Label***

  ```php
  class MyContentForm extends ContentEntityForm {

    protected function actions(array $form, FormStateInterface $form_state) {
      $actions = parent::actions($form, $form_state);

      $actions['submit']['#value'] = t('Custom Submit Label');
    }

  }
  ```

***Add a Custom Submit Button***

  ```php
  class MyContentForm extends ContentEntityForm {

    protected function actions(array $form, FormStateInterface $form_state) {
      $actions = parent::actions($form, $form_state);

      $actions['custom_submit'] = array(
        '#type' => 'submit',
        '#value' => t('Custom Submit Button'),
        '#submit' => array('::submitForm', '::customSubmit'),
      );
    }

    public function customSubmit(array $form, FormStateInterface $form_state) {
      // Perform submit action.

      // If you need to rebuild the form, do so here:
      $form_state->setRebuild(TRUE);
    }

  }
  ```

## Error Messages

`form_set_error` is deprecated in Drupal 8. Instead use:

`$form_state->setErrorByName('field_name', t('Error message.');`

## Autocomplete Fields

`#autocomplete_path` is deprecated in Drupal 8. Instead use `#autocomplete_route_name`. Example:

```php
$form['autocomplete_field'] = array(
  '#type' => 'textfield',
  '#autocomplete_route_name' => 'your_module.autocomplete',
  '#autocomplete_route_parameters' => array(
    'param' => $value,
  ),
);
```

See: [Creating a Route](https://github.com/thinkshout/ts_recipes/blob/master/drupal8/menus_paths.md#creating-a-route)

### Autocomplete Controller

Your autocomplete route should point to a controller method that returns a JsonResponse object. Example:

```php
/**
 * Autocomplete callback method.
 *
 * @param string $param
 *   A parameter.
 *
 * @return \Symfony\Component\HttpFoundation\JsonResponse
 *   A JSON response.
 */
public function autocomplete($param) {
  $data = array()

  $data[] = array(
    'value' => 'item_1',
    'label' => 'Item One',
  );

  return new JsonResponse($data);
}
```

***Important:*** Make sure `'value'` is always a string. autocomplete.js calls .search() when the select handler is triggered and will break on non-string values.

## Loading an entity's add/edit/view/delete form.
Use the [EntityFormBuilder::getForm](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Entity%21EntityFormBuilder.php/function/EntityFormBuilder%3A%3AgetForm/8.2.x) function:
```
$user = user_load(1);
$form = \Drupal::service('entity.form_builder')->getForm($user, 'default');
```
This example gets the default form - "default" represents the operation (e.g. add, edit, delete).
The class name for the form for each operation (add, edit, delete, etc.) can be found in the form section of the handlers entity annotation. For example:
```
  handlers = {
    "form" = {
      "default" = "Drupal\redhen_contact\Form\ContactForm",
      "add" = "Drupal\redhen_contact\Form\ContactForm",
      "edit" = "Drupal\redhen_contact\Form\ContactForm",
      "delete" = "Drupal\redhen_contact\Form\ContactDeleteForm",
},
```
Alternatively, the form class can be set from [hook_entity_type_build()](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Entity%21entity.api.php/function/hook_entity_type_build/8.2.x).
