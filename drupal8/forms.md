# Forms

## Error messages

`form_set_error` is deprecated in Drupal 8. Instead use:

`$form_state->setErrorByName('field_name', t('Error message.');`
