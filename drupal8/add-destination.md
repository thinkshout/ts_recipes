# Add Destination Param to URL
---

Use case: Redirect to a specific URL after editing an entity. This example is
from NCYL where we wanted to redirect back to the student goal dashboard after
editing a goal entity.

```
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

```
