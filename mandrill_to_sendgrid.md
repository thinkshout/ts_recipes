# Mandrill to SendGrid
Some of our clients are switching to SendGrid, now that Mandrill is no longer
being offered as a free service. This information is intended to make the switch
as easy as possible for our team.

## Modules

[**smtp**](https://www.drupal.org/project/smtp) - *recommended*

* Replace mandrill with the smtp module in the site's build dependency files.
* Visit the SMTP configurations page via this path: `/admin/config/system/smtp`
* Follow the [SendGrid recommended SMTP settings](https://sendgrid.com/docs/Classroom/Basics/Email_Infrastructure/recommended_smtp_settings.html).


[**sendgrid_integration**](https://www.drupal.org/project/sendgrid_integration) -
This module depends on composer_manager. We want to avoid this if we can. Additionally, the SMTP module offers more support from the Drupal community and is more widely used.
