# Inivisible reCapcha

Use the reCaptcha drupal module: https://www.drupal.org/project/recaptcha
Install the invisible recaptcha patch: https://www.drupal.org/project/recaptcha/issues/2852269#comment-13003893

Set up on the reCaptcha side: 

- [ ] reCAPTCHA type is v2 w/ the invisible option (cannot be edited once setup)
- [ ] Add domain (can be edited and domains added)
- [ ] Add owner (can be edited and owners added)

Set up on the Drupal side (reCaptcha):
- [ ] Configuration > People > CAPTCHA module settings
- [ ] Set Default challenge type to “reCAPTCHA (from module recaptcha)
- [ ] Make sure the “Log wrong responses” checkbox is checked
- [ ] Select “reCAPTCHA” tab
- [ ] Copy and paste Site and Secret keys from the reCaptcha settings 
- [ ] Make sure the “Size” dropdown in the “Widget” field group is set to “Invisible"

Set up CAPTCHA Points: A captcha point will need to be added for a form to have a captcha attached to it. 
- [ ] Go to the CAPTCHA Points tab
- [ ] Click “Add captcha point"
- [ ] The Form ID will be “contact_message_” + “[form_machine_name]” + “_form”. For example, the Form ID for an accessibility contact form could be “contact_message_accessibility_form"
- [ ] Select “reCAPTCHA (from module recaptcha)” for the Challenge type.
- [ ] Hit “Save"

In the theme: 
```
.captcha {
  summary,
  .details-wrapper .details-description {
    left: -10000px;
    height: 1px;
    overflow: hidden;
    position: absolute;
    top: auto;
    width: 1px;
  }
}
```

Test to see if it fails:

- Host this work on `https`. Try turning off the JavaScript in the browser, fill out the form, and hit save. If a reCaptcha error appears, it's set up correctly.

**Please note -** 

The recaptcha element will be invisible but present for all anonymous visitors, i.e. it will not be testable if logged in. 

You’ll be able to tell is a recaptcha is present if you see the little recaptcha pop up in the lower right corner of your screen.

Filling out the form:
* If the form is filled out immediately, the recaptcha stays invisible. If a user waits to fill it out, a recaptcha challenge will get triggered.
