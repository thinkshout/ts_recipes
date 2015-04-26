(Last updated as of Drupal 8 latest commit, February 16 2015)

hook_menu no longer exists in D8. Instead, it's replaced by a combination of two things: "routes", which are a sort of abstract destination, and "links", which are items that appear in menus and point to destinations. This layer of abstraction allows for, say, a "logout" link to be added somewhere on the site that points to a ROUTE for logging out. That route tells you where the actual link destination is, though. That means that if the User system decides to change the Logout URL from "user/logout" to "user/escape", they just make that change in the Route and your link is never the wiser: it just works.

K, cool, so what do you do about it?

First, there are a bunch of howtos and specifications out on the web which don't match each other. It appears that a lot of the keywords and suggested filenames and structures here have been changing during development. The information in this doc is updated as of the date at the top, but if some more of these specific lines change, then... yeah.

### Creating a Route
In your module root, create a file called "modulename.routing.yml".

Each route you want to create goes into this file in the following format. This one is a form style, so it has a "_form" line. There are other types of routes, but this is a pretty common one.

```
modulename.routename:
  path: 'actual/path/to/load/this/thing'
  defaults:
    _form: '\Drupal\modulename\Form\NameOfClassForm'
    _title: 'Title'
  requirements:
    _permission: 'administer foobar'
```
Example:
```
mailchimp.admin:
  path: 'admin/config/services/mailchimp'
  defaults:
    _form: '\Drupal\mailchimp\Form\MailchimpAdminSettingsForm'
    _title: 'Mailchimp'
  requirements:
    _permission: 'administer mailchimp'
```

Now, create a file called "NameOfClassForm.php" in the module directory under "src/Form/". Have a look at the Mailchimp d8 branch to check out how to organize this file. Basically, you extend the ConfigFormBase class with a class named "NameofPHPClassForm", and implement some functions including:
```
public function getFormID() {
  return 'class_machinename_style_form_id_form';
}
public function buildForm(array $form, FormStateInterface $form_state) {
  $form['foo'] = array('form_api_element');
  return parent::buildForm($form, $form_state);
}
public function submitForm(array &$form, FormStateInterface $form_state) {
  // do stuff, then:
  parent::submitForm($form, $form_state);
}
```

Once you have that stuff, you should be able to go to 'actual/path/to/load/this/thing' on your site and get your form.

### Adding links to menu structures

Very basic version, here. This does not yet cover dynamic menu behaviors and whatnot. But, say you want to put a link to your fancy administrative page route into the admin/config menu or something.

Create a file in your module root called "modulename.links.menu.yml". Figure out the name of the parent item you want to get placed under: it'll be in a .links.menu.yml file somewhere, probably. You can also check the "router" table in the database. Your yml link format is like so:

modulename.link_id:
  route_name: modulename.routename (note how this matches the route file)
  title: 'Link Label'
  parent: system.admin_config_system (this is an actual route elsewhere)

Now, if you want to create a link under your own menu item, it would use "modulename.link_id" as the "parent:" (I think: it might be that you use "modulename.routename" -- I'll update when I figure it out). You just add these to the same file.

## Adding tabs to menu structure

If you have an existing item in the menu structure (from the previous section) and want to create some tabs on that item for different config pages, you need to add a file called "modulename.links.tasks.yml". Think of this as the "default local task" from D7 -- or just the default tab, here. It looks like so:
```
modulename.routename:
  route_name: modulename.routename
  base_route: modulename.routename
  title: 'Tab label'
```
Example:
```
mailchimp.admin:
  route_name: mailchimp.admin
  base_route: mailchimp.admin
  title: 'Mailchimp'
```
Now, to add another tab that points to a new route, just add these to the modulename.links.tasks.yml:
```
modulename.newroute:
  route_name: modulename.newroute
  base_route: modulename.routename
  title: 'Next Tab label'
```
Example (from mailchimp_signup.links.task.yml):
```
mailchimp_signup.admin:
  route_name: mailchimp_signup.admin
  base_route: mailchimp.admin
  title: 'Mailchimp Signup'
  weight: 10
```

## Adding Static Page Arguments

With `hook_menu()` it was possible to set static arguments that would be passed to a page.

  ```php
  'page arguments' => array('value_one', 'value_two'),
  ```

In Drupal 8, these are added to the `defaults` section of a route.

  ```
  example_module.page:
    path: 'admin/config/example/page'
  defaults:
    _controller: '\Drupal\example_module\Controller\ExampleModuleController::page'
    _title: 'Example Page'
    arg_one: 'value_one'
    arg_two: 'value_two'
  ```

  ```php
  public function page($arg_one, $arg_two) {}
  ```

## Dynamic Routes

Routes that need to be generated from user input can be generated dynamically.

* [Providing dynamic routes](https://www.drupal.org/node/2122201)
