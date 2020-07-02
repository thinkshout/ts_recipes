## Clearing files in `/sites/default/files`

### Drupal 7

Disable CSS/JS aggregation:  
`drush vset preprocess_css 0 --yes`  
`drush vset preprocess_js 0 --yes`  

Flush images:
`terminus drush SITE.live if` (will give option list)

### Drupal 8

Disable CSS/JS aggregation:  
`drush -y config-set system.performance css.preprocess 0`  
`drush -y config-set system.performance js.preprocess 0`  

Flush images:
`terminus drush SITE.live if` (will give option list)
