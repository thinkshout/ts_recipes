# Dependent migrations

When there is a relationship between two migrations we need to use the [process plugin](https://www.drupal.org/node/2149801).

This plugin allows us to easily look up the ID of an entity that was migrated before the current migration.

Example:
```yaml
process:
  uid:
    plugin: migration
    migration: users
    source: 
      - author
```

# Using migration plugins derived from YML

Some ThinkShout sites store their migrations as config, which is a feature
provided by migrate_plus which enables you to interact with migrations using
the user interface. The disadvantage of this is that every time you make a
change to a migration, you have to re-import config.

Instead of doing this, you can put your migration YML files in the "migrations"
folder of any module on your site. Then to pick up a change to that YML, all
you have to do is run "drush cr".

# Referencing new paragraphs by ID

If you're writing a Google Sheets migration, you may want to migrate paragraphs
alongside your nodes. The typical "migration_lookup" plugin does not work great
for this however, as an entity reference revision field requires both an ID and
a revision ID.

Luckily with Google Sheet migrations, the ID and revision ID are always the
same, so you can write something like this:
```yaml
  field_sections:
    -
      plugin: explode
      source: field_sections
      delimiter: ','
      strict: true
    -
      plugin: migration_lookup
      migration:
        - content_list
        - xup_cta
        - standard_block
        - thumbnail_strip
    -
      plugin: iterator
      process:
        target_id: '0'
        target_revision_id: '1'
```
Where "field_sections" is a comma separated list of migration source IDs.

# Debugging migration process pipelines

Sometimes debugging migrations process pipelines can be super hard, so to
assist you, you can use this quick "debug" plugin which only exists to trigger
breakpoints:
```php
<?php

namespace Drupal\my_module\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Migration plugin you can throw in YML and use XDebug on.
 *
 * @MigrateProcessPlugin(
 *   id = "debug"
 * )
 */
class Debug extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    // XDebug this line.
    return $value;
  }

  /**
   * {@inheritdoc}
   */
  public function multiple() {
    // Change this to FALSE if you want to see the single (normal) value.
    return TRUE;
  }

}
```
Then you can throw `plugin: debug` anywhere in process pipeline to see what the
data looks like at that point in time.

# Debugging migration sources

You can debug migration sources in a similar way, just add a simple plugin like
this and tweak methods like `::query` to test out modifying source data before
committing to a new source plugin:

```php
<?php

namespace Drupal\my_module\Plugin\migrate\source;

use Drupal\node\Plugin\migrate\source\d7\Node;

/**
 * Node source from database.
 *
 * Use this source plugin when you want to mess with the query.
 *
 * @MigrateSource(
 *   id = "debug",
 *   source_module = "node"
 * )
 */
class Debug extends Node {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = parent::query();
    // Example, restrict migration to a specific Node to speed up testing!
    // $query->condition('n.nid', 1);
    return $query;
  }

}
```

Then when you need to debug, switch the source plugin over, rebuild cache, and
start tweaking.
