#Purpose
Implement a pop-up the first time a visitor comes to a site.

#How
Overview...

1. Define a bean type for the pop-up
2. Write a plugin to extend the default Bean module "view" function
3. Add JS via the view extension so that it is only present when pop-up type beans are rendered
4. Use JS to drop a cookie the first time the pop-up bean is rendered
5. Hide the bean if the cookie is already present in the visitor's browser (need theme\_preprocess\_block to add class idential to bean type machine name to outer div of rendered bean - this ensures the whole bean is hidden; including title)

Extras...

* Define how long the cookie takes to expire

# Example
See the [families\_usa\_beans\_header\_pop\_up module](families_usa_beans_header_pop_up) in this directory.