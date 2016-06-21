# Add destination param to URL & populate pseudo-field
---

Use case: Redirect to a specific URL after editing an entity. This example is
from NCYL where we wanted to redirect back to the student goal dashboard after
editing a goal entity. Good article on pseudo fields in Drupal 8: https://www.webomelette.com/creating-pseudo-fields-drupal-8

```
function my_module_node_view_alter(array &$build, EntityInterface $entity, EntityViewDisplayInterface $display) {

  // ... other module code ...

  // Redirect back to dashboard after edit.
  $destination = Url::fromRoute('student.goals', [
    'redhen_org' => $student_org_id,
  ])->getInternalPath();

  // Populate the pseudo field edit button with the link and return destination.
  $build['edit'] = Link::fromTextAndUrl(t('Edit'),
    Url::fromRoute('student.goals.edit', [
      'redhen_org' => $student_org_id,
      'node' => $entity->id(),
      'destination' => $destination,
    ])
  )->toRenderable();
}
```
