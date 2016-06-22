Date and Time handling

###format_date()

format_date is being deprecated. Here's the [link to find out more](https://api.drupal.org/api/drupal/core!includes!common.inc/function/format_date/8).

D7 example:
`format_date(strtotime($campaign->mc_data->create_time) ,'custom','F j, Y - g:ia')`

D8 example:
`\Drupal::service('date.formatter')->format(strtotime($campaign->mc_data->create_time) ,'custom','F j, Y - g:ia')`
