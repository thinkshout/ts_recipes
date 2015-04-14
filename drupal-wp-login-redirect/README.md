## The Problem

Many Drupal sites will get hundreds of 404s per day at the url `wp-login.php`. These are due to bots fishing for WordPress login pages (presumably intending to mount brute force attacks. This costs some server time, but also clogs up your watchdog logs (in one client's case, with 750+ messages per day), making it difficult to identify legitimate broken link issues.

## A Solution

The following snippet will redirect `wp-login.php` traffic to the base url with a 301 status. It does so before the Drupal bootstrap, so it is very cheap computationally and no watchdog message is logged. More sophisticated bots should recognize what a 301 status means, and take their nefarious business elsewhere, where their claws may find purchase.

```php
if ($_SERVER['REQUEST_URI'] === "/wp-login.php") {
  header('HTTP/1.0 301 Moved Permanently');
  header('Location: '. $base_url);
  exit();
}
```

This should be placed in `settings.php`, *before* any other redirect logic (for example, Pantheon environment redirects, or HTTPS redirects). A safe place would be right after the `# drupal_fast_404();` comment.

## Pantheon Environment Note

Pantheon's environment may not have the same variables available in `settings.php` as your local environment, so you may need to define `$base_url` yourself.
