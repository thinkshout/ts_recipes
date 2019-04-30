### Fix AddThis Accessibility on D7/D8 sites
_Drupal 7/8_

Adds labels and href values to AddThis to remove accessibility errors. Update social accounts below. Keep the space in the href values to avoid triggering an error.

```
/**
 * Alter AddThis markup for accessibility.
 */
function splc_hate_map_addthis_markup_alter(& $markup) {
  $markup['facebook']['#attributes']['href'] = " "; // Fixes href error.
  $markup['facebook']['#attributes']['aria-label'] = "Facebook: Share this page";
  $markup['twitter']['#attributes']['href'] = " "; // Fixes href error.
  $markup['twitter']['#attributes']['aria-label'] = "Twitter: Share this page";
  $markup['email']['#attributes']['href'] = " "; // Fixes href error.
  $markup['email']['#attributes']['aria-label'] = "Email: send this page to a friend";
}
```
