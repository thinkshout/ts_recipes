### The l() and url() functions are gone:
Here's the thread regarding the deprecation: https://www.drupal.org/node/2346779

#### Drupal::l($text, Url $url) - _(deprecated)_:
[Documentation](https://api.drupal.org/api/drupal/core%21lib%21Drupal.php/function/Drupal%3A%3Al/8)

Renders a link with a given link test and Url Object.

### Options and Functions for Moving Forward:

#### Url::fromUri($uri, $options = [])
[Documentation](https://api.drupal.org/api/drupal/core!lib!Drupal!Core!Url.php/function/Url%3A%3AfromUri/8)

Creates a new Url object from a URI. For non-routed local URIs relative to the base path, use this.

#### Url::fromRoute($route_name, $route_params = array(), $options = array())
[Documentation](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Url.php/function/Url%3A%3AfromRoute/8)

Creates a new Url object for a URL that has a Drupal route.

#### Url::fromUserInput($user_input, $options = [])
[Documentation](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Url.php/function/Url%3A%3AfromUserInput/8)

Creates a Url object for a relative URI reference submitted by user input. use this function to create a URL for user_entered paths that may or may not correspond to a valid route.

#### Link::fromTextAndUrl($text, Url $url)
[Documentation](https://api.drupal.org/api/drupal/core!lib!Drupal!Core!Link.php/function/Link%3A%3AfromTextAndUrl/8)

Creates a link from a given Url object. Likely requires [additional methods](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Link.php/class/Link/8) to get the desired effect.

Using `Link::fromTextAndUrl` and `toRenderable()` is certainly a way to do replace
the `l()` and `url()` functions, however Drupal Core Link documentation recommends
using the form API like so:

```
$build['examples_link'] = [
  '#title' => $this->t('Examples'),
  '#type' => 'link',
  '#url' => Url::fromRoute('examples.description')
];
```

Links will be rendered as Drupal intended if they are applied to the `data`
structure of the element. If it's applied elsewhere, it may result in this
error: `Recoverable fatal error: Object of class Drupal\Core\Url could not be
converted to string`. The `data` structure can handle renderable arrays, unlike
many alternative placements.
