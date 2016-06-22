# Dependent migrations

When there is a relationship between two migrations we need to use the [process plugin](https://www.drupal.org/node/2149801).

This plugin allows us to easily look up the ID of an entity that was migrated before the current migration.

Example:
```
process:
  uid:
    plugin: migration
    migration: users
    source: 
      - author
```
