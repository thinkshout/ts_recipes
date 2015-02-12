## Problems & (sometimes) Solutions

### This file should contain simple descriptions of annoying problems that come up, and how to address them.


#### Apache
1. Apache won't launch!  
  * You probably have a typo or other inconsistency in your config. Whatever you just changed, undo it! See if Apache launches now. If it does, try your change again but, ya know, different.
2. I can only get one site on SSH at a time.  
  * Take a look at the httpd-vhosts.conf in this very directory: it has this line in it `NameVirtualHost *:443`. You need that somewhere.
