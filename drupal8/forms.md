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

***Important:*** Make sure `'value'` is always a string. autocomplete.js calls .search() on that value when the select handler is triggered will break on non-string values.
