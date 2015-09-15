/**
 * Implements hook_form_alter().
 */
function ysc_survivor_story_form_alter(&$form, &$form_state, $form_id) {
  // Specify the form id to isolate which form will be altered.
  switch ($form_id) {
    case "views_exposed_form":
      // Add surrogate field to view form to filter for Survivor Stories that
      // contain videos.
      $form['video_only'] = array(
        '#type' => 'checkbox',
        '#title' => 'Video',
      );

      // Add custom validation handler for nodes with video content.
      $form['#validate'][] = '_ysc_survivor_story_form_validate';
    break;
  }
}

/**
 * This is a custom validation handler used to determine whether a video field
 * value is present for a YSC Survivor Story, with the intention on allowing
 * users to search for node stories that contain videos.
 */
function _ysc_survivor_story_form_validate(&$form, &$form_state) {
  // The exposed video filter operator is set to "is not empty", so the nodes
  // that initially appear on this listing page are ones with video values.
  if ($form_state['values']['video_only']) {
    $form_state['values']['field_video_embed_url_video_url_op'] = 'not empty';
  }
  else {
    // This allows nodes without video values to appear in the initial listing
    // page, in addition to those with video values.
    $form_state['values']['field_video_embed_url_video_url'] = NULL;
  }
}
