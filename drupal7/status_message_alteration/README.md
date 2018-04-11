# Status Message Alterations
---

Use case: A client wants to replace status message text with a custom message.

```
/**
 * Implements hook_preprocess_HOOK().
 */
function lac_redhen_preprocess_status_messages(&$variables) {
  $message_to_replace = "Contact information saved.";

  if (!empty($_SESSION['messages']['status']) && in_array($message_to_replace, $_SESSION['messages']['status'])) {
    $replacement = "Thank you! Your changes have been saved.";
    $_SESSION['messages']['status'] = array_replace($_SESSION['messages']['status'],
      array_fill_keys(
        array_keys($_SESSION['messages']['status'], $message_to_replace),
        $replacement
      )
    );
  }
}
```
