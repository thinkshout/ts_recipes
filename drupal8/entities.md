# Entities

## Entity info

`entity_get_info()` is deprecated in Drupal 8. Use [\Drupal::entityManager()->getDefinitions()](https://www.drupal.org/node/1929006).

## Configuration Entities

Configuration entities are intented to store site configuration data.

* [Configuration API documentation](https://www.drupal.org/developing/api/8/configuration)
* [Configuration entities documentation](https://www.drupal.org/node/1818734)
* [Creating a configuration entity](https://www.drupal.org/node/1809494)

Configuration entities are stored in the `config` database table.

## Content Entities

Content entities are basically intended to be used the same way as Drupal 7 entities.

* [Creating a content entity](https://www.drupal.org/node/2192175)

## View Builders

Entities in Drupal 8 can be rendered using view builders.

* [Entities are now rendered by a view builder](https://www.drupal.org/node/1819308)
  * Note that the above example defines the entity's view_builder property in a "controllers" object, while ["controllers" is now "handlers"](https://www.drupal.org/node/2200867)
