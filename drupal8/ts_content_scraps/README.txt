This module is not intended for use as-is. Instead, copy it to your site and
customize as needed. Perhaps in time an installable version will be forked from
this work.

Copy this module into your custom modules folder and add it to your repo that
way.

When enabled, this module adds a menu item under "Content" called "Custom Text
Configuration". That becomes the place that users with "Administer Nodes"
permission can got to customize text on various parts of the site.

To add a new customizable string, edit src/Form/CustomTextForm.php file. Edit
the "buildForm()" function to add a new string. There are a couple examples in
the file, which you can feel free to delete.

To access the customized value in your code, call:
\Drupal::config('ts_content_scraps.items')->get('YOUR_VARIABLE')
