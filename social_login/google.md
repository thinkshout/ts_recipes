Google Configuration for Hybridauth
=======

You should tell the client to create or provide the address of a google account so they can be added to the app once you create it. Also, ask them for a 120x120 image to use as the logo when the confirmation screen pops up.

Log in to your TS google account and go to https://console.developers.google.com

Click "Create Project", name your project.

Once in your App, go to Permissions and add the client and other developers.

Go to Apis & Auth -> APIs, and enable "Contacts API" and "Google+ API".

Go to Apis & Auth -> Credentials. On the Credentials tab, click on "Add credentials" and select "OAuth 2.0 client ID" from the list. Note: It may redirect you to the "OAuth consent screen" where you will need to provide a product name for your project before you can add origins. Once you've done that, select 'web application' and add all your site domains, including any test or dev sites, and your local ngrok aliases, under "Javascript Origins". Include http & https URLs as needed. Under Redirect URIs, do the same but include the custom menu callback and query parameter used by hybridauth. For example:
https://www.thinkshout.com/hybridauth/endpoint?hauth.done=Google

Go to the "Consent Screen" tab and complete all the logical stuff: this is where you want to upload the image provided by the client.

Now, go to the hybridauth configuration on your site and add the appropriate authentication configuration. (admin/config/people/hybridauth/provider/Google)

Make sure you check the box for Google on the main hybridauth config dialogue.
