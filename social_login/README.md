Social Login
=======

Social login allows you to add little buttons to the login or register screens to use external user accounts to create and authenticate accounts on a Drupal site. EG: Facebook, Google accounts. This document is based on work done for the DC-campaign website.

You will need the following:

- The "Hybridauth" module: https://www.drupal.org/project/hybridauth
- The "Hybridauth" library: https://github.com/hybridauth/hybridauth

There is an example.make in this directory which you can use to add these items to your drush makefile.

Configuration:
admin/config/people/hybridauth

- "Authentication Providers": Holy WOW what a list. Each requires it's own config, see specific Provider docs for instructions on each one.
- "Required Information" tab: Reducing this to "email" is fine for most cases.
- "Widget Settings": basic appearance settings for the login buttons. Be prepared to hit these with some CSS as well.
- "Account Settings": Pretty self-explanatory, but have a look.
- "Drupal Forms": Where to show this login tool. Pretty obvious.

Permissions:
Be sure to set the "Use Hybridauth" permission for basically everyone, otherwise it's not particularly useful.

Now, make sure you configure some providers!
