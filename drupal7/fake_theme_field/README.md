# Theme functions to mimic theme_field

When rendering a property on an entity or an 'extra_field' added by hook_field_extra_fields() it's often nice to have it structured much like other fields.


```
// Add the extra fields element with our render array.
$node->content['EXTRA_FIELD_NAME'] = array(
  '#theme' => 'MODULE_extra_field',
  '#label' => t('Skills(s)'),
  '#items' => array(array('#markup' => implode(', ', $skill_list))),
  '#classes' => 'field field-label-inline clearfix',
);
