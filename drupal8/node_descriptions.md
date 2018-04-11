# Node Form Descriptions

A snippet for including the Node Type "Description" on the Node edit form. Handy simple usability improvement.

``` php
/**
 * Implements hook_form_BASE_FORM_ID_alter().
 */
function MY_MODULE_NAME_form_node_form_alter(&$form, FormStateInterface $form_state, $form_id) {
  $bundle_info = NodeType::load($form_state->getStorage()['form_display']->getTargetBundle());
  $form['description'] = [
    '#markup' => $bundle_info->getDescription(),
    '#weight' => $form['title']['#weight'] - 0.1,
  ];
}
```
