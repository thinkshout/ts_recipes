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

Using Rules with Hybridauth
---------------------------

Using the standard 'redirect' action after 'hybridauth_user_login' event will cause an endless redirect loop in the browser when a use logs in with hybridauth. If you need a rule to redirect social and regular users after login, you will need to make two rules. One for the regular users and one for hybridauth users. 

The rule for regular users can use the 'user_login' event and the 'redirect' action. The rule for hybridauth user must use the 'hybridauth_user_login' event and the 'hybridauth_set_destination' action instead of the standard 'redirect' action. Unfortunately, the 'user_login' event will first first, so you will need to add a condition to this rule to prevent the action from taking place for hybridauth logins. Add the condition 'NOT hybridauth_user_created_hybridauth' to this rule so it will be ignored for hybridauth logins and the next (hybridauth) rule will get executed for hybridauth users afterward. 
