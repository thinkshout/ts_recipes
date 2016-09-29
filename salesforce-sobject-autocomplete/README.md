# Drupal 7 Salesforce sobject autocomplete

How to implement a Salesforce SOSL autocomplete which will find Salesforce Campaign Ids by Name. To find other object types, go through all of this code and simply swap out "Campaign" for whatever other sobject name, e.g. "Account", "Contact", etc.

Add a hook_menu entry for the autocomplete callback, and clear your cache:

```php
function my_module_menu() {
  $items['my/module/campaign/autocomplete'] = array(
    'title' => 'Autocomplete for Salesforce Campaign',
    'page callback' => 'my_module_campaign_autocomplete',
    'access arguments' => array('administer salesforce'),
    'type' => MENU_CALLBACK,
  );

  return $items;
}

```

Implement the autocomplete callback:

```php
/**
 * Autocomplete callback for Salesforce Campaign.
 */
function my_module_campaign_autocomplete($search) {
  $results = array();

  $salesforce = salesforce_get_api();
  $query = 'FIND {' . $search . '} IN NAME FIELDS RETURNING Campaign(Id, Name) LIMIT 10';
  $data = $salesforce->apiCall('search/?q=' . urlencode($query));

  if ($data) {
    foreach ($data as $result) {
      $results[$result['Id']] = $result['Name'];
    }
  }

  return drupal_json_output($results);
}
```

And hook it up to your field. Here's one way to do it. In this case my field name is 'field_campaign':

```php
/**
 * Implements hook_field_widget_form_alter().
 */
function my_module_field_widget_form_alter(&$element, &$form_state, $context) {
  if ($context['field']['field_name'] == 'field_campaign') {
    $element['value']['#placeholder'] = t('Type a campaign name');
    $element['value']['#autocomplete_path'] = 'my/module/campaign/autocomplete';
  }
}
```
