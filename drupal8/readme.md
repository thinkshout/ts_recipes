
General stuff and how to deal with it:

### So much yaml!
If you don't know yaml, it's easy. Important thing to note is that yaml actually cares about spacing, so pay attention to the spacing in the example files.

### The l() and url() functions are gone:
There's an API for that now: https://www.drupal.org/node/2346779

March, 31st 2016 update:
Please see this [comment](https://www.drupal.org/node/2346779#comment-10977601).

Using `toString()` will result in escaped html. Try using `toRenderable()`, and make
sure the link is being applied to the `data` structure of the element. If it's
applied elsewhere, it may result in this error: `Recoverable fatal error: Object of class
Drupal\Core\Url could not be converted to string`. The `data` structure can handle
renderable arrays.
