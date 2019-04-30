* Drupal 7/8
Adds labels and href values to Add This to remove accessibility errors. 

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
