# Adding a custom field search 

Implement hook_entity_property_info_alter() to create a field. Example:

```php
/**
 * Implements hook_entity_property_info_alter().
 */
function redesign_activity_entity_property_info_alter(&$info) {
  $properties = &$info['node']['properties'];
  $properties['skill_fields'] = array(
    'type' => 'list<text>',
    'label' => t('Skills'),
    'sanitized' => TRUE,
    'getter callback' => 'redesign_activity_property_skills_getter_callback',
  );
}
```

This field will be found and can be added from the following path, under the "ADD RELATED FIELDS" option: admin/config/search/search_api/index/default_node_index/fields

Once added, your getter callback can be used to get the information you need. Example:

```php
/**
 * Getter callback for skills property.
 */
function redesign_activity_property_skills_getter_callback($item) {
  $skill_list = array();
  if ($item->type == 'activity') {
    // Start a new EFQ
    $query = new EntityFieldQuery();


    $query->entityCondition('entity_type', 'node')
      // Get all skills
      ->entityCondition('bundle', 'skill')
      // that reference this activity.
      ->fieldCondition('field_activity', 'target_id', $item->nid, '=');

    $result = $query->execute();

    // If results are returned, it will be in node key
    if (isset($result['node'])) {
      $nids = array_keys($result['node']);
      // Load all the nodes that were returned.
      $skills = node_load_multiple($nids, array('type' => 'skill'));

      $skill_list = array();
      // Build array of node titles.
      foreach ($skills as $skill) {
        // Unlinked title of the node.
        $skill_list[] = entity_label('node', $skill);
      }
    }
  }
  return $skill_list;
}
```