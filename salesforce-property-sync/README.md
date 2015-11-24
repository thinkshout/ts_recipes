
# Syncing data to Salesforce with entity_property_info_alter

This is a pattern you can use to sync data that isn't directly attached
to a Drupal entity (such as via a property or field) that avoids messy
Salesforce hook code. Check out myproject_event.module - it defines a
new property on Event Nodes for "Registration capacity" which allows you
to define a direct mapping for this property to the corresponding
Salesforce field using the Salesforce module. The getters and setters
shuffle the data into the registration entity settings.

Consider using this pattern any time you need to sync data to Salesforce
that isn't directly attached to the Drupal entity that you're syncing.

