### The l() and url() functions are gone:
There's an API for that now: https://www.drupal.org/node/2346779

March, 31st 2016 update:
Please see this [comment](https://www.drupal.org/node/2346779#comment-10977601).

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
