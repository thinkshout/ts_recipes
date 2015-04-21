# Forms

## Collapsible Fieldsets

`#collapsible` and `#collapsed` are no longer valid in Drupal 8.

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

## Error messages

`form_set_error` is deprecated in Drupal 8. Instead use:

`$form_state->setErrorByName('field_name', t('Error message.');`
