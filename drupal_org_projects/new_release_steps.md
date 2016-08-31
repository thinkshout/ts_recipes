# Adding a New Release to Drupal.org
---
Here are the steps needed to properly add a new release to one of our contributed modules on Drupal.org:

### Gather and list the available tags

* Run `git fetch [drupal remote]`
* Run `git tag`

### Create your new tag and push it up to Drupal.org

* Run `git tag [new tag]`
* Run `git push [drupal remote] [branch]`
* Run `git push [drupal remote] [new tag]`

### Go to the new release form on Drupal.org

* Go to project page, ex: https://www.drupal.org/project/mailchimp
* Click “Edit”
* Click “Releases” (under the "Edit" menu)
* Click “Add new release” (located at the bottom of the list)

### Create release notes

* Run `drush rn [new tag]`
* Copy and paste the html into new release form
* Remove any commit notes you deem unimportant
* Add a short description for the release
* Hit "Save"
* Rejoice in your awesomeness

### Major releases
If your release is considered a major one (moving from 1.x to 2.x, for example), make sure your latest release the recommended version. You can do this from your "Edit" > "Releases" page.
