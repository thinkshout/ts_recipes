##Purpose
Implement a alert that displays the first time a visitor comes to a site.

## Implementation Overview

1. Define a bean type for the alert
2. Write a plugin to extend the default Bean module "view" function in order to add JS that will only be present when alert type beans are rendered
4. Use JS to drop a cookie the first time the alert bean is rendered
5. Show the bean if the cookie is NOT already present in the visitor's browser

## Implementation Details
1. **Define a bean type for the alert** – this can be done via the UI and exported with Features or it can be done exclusively in code. The approach you take depends on whether you need things like fields for your alert; if you need fields, create the bean type and any necessary fields through the UI – else define a form for creating a bean of the new type.
2. **Write a plugin to extend the default Bean module "view" function** – this is necessary to add required JS/CSS to the page only when an alert bean is rendered (to avoid the performance hit of loading on every page). This could be accomplished alternatively via a [hook\_block\_view\_alter()](https://api.drupal.org/api/drupal/modules!block!block.api.php/function/hook_block_view_alter/7). In addition to required JS/CSS, you can add any settings (e.g. expiration time) you want to pass to the JS here. You can also modify the default bean view function here, but if you just want to add JS/CSS, render the bean content in the [same way the parent does](minimal_example/plugins/bean/minimal_example.inc#L15).
4. **Use JS to drop a cookie the first time the alert bean is rendered** – This is all accomplished in [minimal_example.js](minimal_example/js/minimal_example.js). Note that we drop a cookie that is [named based on the alert bean's machine name](minimal_example/js/minimal_example.js#L5) so that we can have multiple active alerts silmultaneously.
5. **Show the bean if the cookie is NOT already present in the visitor's browser** – Note we are using `display: none` to hide the bean by default in CSS and [showing it](minimal_example/js/minimal_example.js#L13) if the cookie is not present. **IMPORTANT** - you should add a class to your top-level alert bean div via [template\_preprocess\_block()](https://api.drupal.org/api/drupal/modules!block!block.module/function/template_preprocess_block/7) ([shown here](minimal_example/minimal_example.module#L32-L53)) so the line of JS that creates a cookie based on the bean title and the [jQuery.remove()](http://api.jquery.com/remove/) call function properly. By default there is no CSS identifier that uses the bean type on the top-level div. Putting one there allows us both to easily grab the unique CSS id of the bean used to generate a unique cookie name, and to remove the bean's HTML entirely (i.e. from the top-level).

###Extras...

* Define how long the cookie takes to expire - you can [add settings in the bean view extension](minimal_example/plugins/bean/minimal_example.inc#L22-L38) and then [use these settings in the JS](minimal_example/js/minimal_example.js#L10-L12).

## Examples
See the [families\_usa\_beans\_header\_alert module](families_usa_beans_header_alert) in this directory for a fully featured example.

The [minimal\_example](minimal_example) is more stripped down than the Families USA example in that it does not include any exported features and is more heavily commented to highlight elements from the "Implementation Overview" above.