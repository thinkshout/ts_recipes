# Logging

[Watchdog is no more](https://www.drupal.org/node/2270941).

### Logging a Notice

```php
\Drupal::logger('module_name')->notice($message);
```

### Logging an Error

```php
\Drupal::logger('module_name')->error($message);
```

### Logging an Error with Token Replacement

```php
\Drupal::logger('mailchimp')->error('An exception occurred. The message was: {message}', array(
  'message' => $e->getMessage()));
```
