# Hooks

## Creating & Invoking a Custom Hook

Where you once used ```module_invoke_all('my_hook_name', $arg1, $arg2)```, you now use ```\Drupal::moduleHandler()->invokeAll('my_hook_name', array($arg1, $arg2, ....))```

Invoking the hook is the same as before. However, your hook won't register properly in the system unless you create and properly format a file called ```modulename.api.php```. In particular, be sure to include this comment before your first hook documentation:

```
/**
 * @addtogroup hooks
 * @{
 */
```

Additionally, you should document your hook with a category, like so:

```
/**
 * My hook does cool stuff
 *
 * @param $arg1
 * @param $arg2
 *
 * @ingroup my_group_name
 */
function hook_mymodule_hook($arg1, $arg2) {}
```