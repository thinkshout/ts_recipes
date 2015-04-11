# Fields

## Field Types

Field types are plugins in Drupal 8.

* [Field type plugins in Drupal 8](http://cgit.drupalcode.org/drupal/tree/core/lib/Drupal/Core/Field/Plugin/Field/FieldType)

Standard field types are below. The ID should be used when defining a field as part of an entity schema, for example.

| Class                 | ID               | Notes                                      |
| --------------------- | ---------------- | ------------------------------------------ |
|   BooleanItem         | boolean          |                                            |
|   ChangedItem         | changed          | UNIX timestamp                             |
|   CreatedItem         | created          | UNIX timestamp                             |
|   DecimalItem         | decimal          |                                            |
|   EmailItem           | email            |                                            |
|   EntityReferenceItem | entity_reference |                                            |
|   FloatItem           | float            |                                            |
|   IntegerItem         | integer          |                                            |
|   LanguageItem        | language         |                                            |
|    MapItem            | map              | Serialized array of values                 |
|   PasswordItem        | password         |                                            |
|   StringItem          | string           |                                            |
|   StringLongItem      | string_long      | Creates either a blob or text field in MySQL. [Example](http://cgit.drupalcode.org/drupal/tree/core/lib/Drupal/Core/Field/Plugin/Field/FieldType/StringLongItem.php#n31). |
|   TimestampItem       | timestamp        |                                            |
|   UriItem             | uri              |                                            |
|   UuidItem            | uuid             |                                            |
