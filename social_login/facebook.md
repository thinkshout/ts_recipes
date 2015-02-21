Facebook Configuration for Hybridauth
=======

Ask the client for an exactly 1024x1024 image, and a 16x16, to attach to the app for use in the interface that will show up for end users.

You will need a Facebook account.

Login to Facebook and go to https://developers.facebook.com/

My Apps -> Add a New App -> Website

Go through the app creation dialogue. Most of this can be changed later so don't stress about it. Also, it's easy to create another one.

When you get your app created, go to "Settings":

Make sure to put the Top Level Domain for the site into "App Domains".*

Configure the site URL. 

Under "App Details" you may want to configure a logo or other business: this is where those two images will come in handy.

Add other developers, and the client, under "Roles".

Anyone added in the Roles can use the App while it's private. When you launch, you'll need to go to the "Status & Review" section and flip the switch at the top ("Do you want to make this app and all its live features available to the general public?").

Now, go to the hybridauth configuration on your site and add the appropriate authentication configuration. (admin/config/people/hybridauth/provider/Facebook)

- Under Advanced Settings here, you only need "Email". Minimizing the checked boxes will make the authentication less creepy. Add more if you need them. offline_access is a good idea, probably.

Make sure you check the box for Facebook on the main hybridauth config dialogue.

* For development purposes, you can create a "test app" by clicking on the app name above the menu on the left: you can set that app to point to your ngrok.com domain, and use ngrok to route external traffic to your development environment. Google ngrok if you haven't done this before. In these cases, point the "App Domain" to ngrok.com. HOWEVER -- you will need to create a separate live app for the Pantheon TEST and DEV sites if you want them to work. Test Apps don't accept logins from any accounts that aren't manually added to the app, so they are pretty useless for client testing.