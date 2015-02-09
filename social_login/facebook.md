Facebook Configuration for Hybridauth
=======

You will need a Facebook account.

Login to Facebook and go to https://developers.facebook.com/

My Apps -> Add a New App -> Website

Go through the app creation dialogue. Most of this can be changed later so don't stress about it. Also, it's easy to create another one.

When you get your app created, go to "Settings":

Make sure to put the Top Level Domain for the site into "App Domains". (For development purposes, you'll need to create a "test app" by clicking on the app name above the menu on the left: you can set that app to point to your ngrok.com domain, and use ngrok to route external traffic to your development environment. Google ngrok if you haven't done this before. In these cases, point the "App Domain" to ngrok.com)

Configure the site URL.

Under "App Details" you may want to configure a logo or other business.

Add other developers, and the client, under "Roles"

Now, go to the hybridauth configuration on your site and add the appropriate authentication configuration. (admin/config/people/hybridauth/provider/Facebook)

- Under Advanced Settings here, you only need "Email". Minimizing the checked boxes will make the authentication less creepy. Add more if you need them. offline_access is a good idea, probably.

Make sure you check the box for Facebook on the main hybridauth config dialogue.
