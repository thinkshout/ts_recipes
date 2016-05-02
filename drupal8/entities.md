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

## Entity View Modes

Get all view modes of an entity using [\Drupal::entityManager()->getViewModes()](https://api.drupal.org/api/drupal/core!lib!Drupal!Core!Entity!EntityManager.php/function/EntityManager%3A%3AgetViewModes/8).

```php
$entity_type = 'node';
$view_modes = \Drupal::entityManager()->getViewModes($entity_type);
```

## Entity Forms
To load an entity's add/edit/view/delete form use the [EntityFormBuilder::getForm](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Entity%21EntityFormBuilder.php/function/EntityFormBuilder%3A%3AgetForm/8.2.x) function:
```
$user = user_load(1);
$form = \Drupal::service('entity.form_builder')->getForm($user, 'default');
```
This example gets the default form - "default" represents the operation (e.g. add, edit, delete).
The class name for the form for each operation (add, edit, delete, etc.) can be found in the form section of the handlers entity annotation. For example:
```
  handlers = {
    "form" = {
      "default" = "Drupal\redhen_contact\Form\ContactForm",
      "add" = "Drupal\redhen_contact\Form\ContactForm",
      "edit" = "Drupal\redhen_contact\Form\ContactForm",
      "delete" = "Drupal\redhen_contact\Form\ContactDeleteForm",
},
```
Alternatively, the form class can be set from [hook_entity_type_build()](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Entity%21entity.api.php/function/hook_entity_type_build/8.2.x).
