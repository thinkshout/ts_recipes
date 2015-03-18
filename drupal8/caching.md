# Caching

[New cache API](https://www.drupal.org/node/1272696)

### Get cached data

```php
$cached_data = \Drupal::cache()->get($cache_id);
$cached_data->data;
```

### Get cached data from a specific cache bin

```php
$cache = \Drupal::cache('cache_bin_name');
$cached_data = $cache->get($cache_id);
$cached_data->data;
```

### Define a custom cache bin

Create file `module_name.services.yml`

```
services:
  cache.cache_bin_name:
    class: Drupal\Core\Cache\CacheBackendInterface
    tags:
      - { name: cache.bin }
    factory_method: get
    factory_service: cache_factory
    arguments: ['cache_bin_name']
```
