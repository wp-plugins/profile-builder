=== Profile Builder === 

Contributors: reflectionmedia, barinagabriel
Donate link: http://www.cozmoslabs.com/2011/04/12/wordpress-profile-builder-a-front-end-user-registration-login-and-edit-profile-plugin
Tags: registration, profile, user registration, custom field registration, customize profile, user fields, builder, profile builder
Requires at least: 3.1
Tested up to: 3.1.2
Stable tag: 1.0.9


Login, registration and edit profile shortcodes for the front-end. Also you can chose what fields should be displayed.

 
== Description ==

Profile Builder lets you customize your website by adding a front-end menu for all your users, 
giving them a more flexible way to modify their user-information or to register new users. 
Also, grants users with administrator rights to customize basic fields. 
To achieve this, just create a new page, and give it an intuitive name(i.e. Edit Profile).
Now all you need to do is add the following shortcode(for the previous example): [wppb-edit-profile]. 
Publish your page and you are done!

You can use the following shortcodes:

* [wppb-edit-profile] - to grant users a front-end acces to their personal information(requires user to be logged in).
* [wppb-login] - for a log-in form.
* [wppb-register] - to add a registration form.

Also, users with administrator rights have access to the following features:

* add a custom stylesheet/inherit values from the current theme or use the default one, built into this plug-in.
* select whether to display or not the admin bar in the front end for a specific user-group registered to the site.
* select which information-field can the users see/modify. The hidden fields' values remain unmodified.

NOTE:

This plugin only adds/removes fields in the front-end. The default information-fields will still be visible(and thus modifiable) 
from the back-end, while custom fields will only be visible in the front-end.
	


== Installation ==

1. Upload the profile-builder folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Create a new page and use one of the shortcodes available

== Frequently Asked Questions ==

= Will it change the default admin-panel from the back-end by adding/disableing information-fields? =

No, profile-builder only affects the front-end of your site, leaving the default fields unmodified

= Will the information be also updated for any hidden fields? =

No, only fields visible to the users will/can be modified/updated.


== Screenshots ==

1. Login Page: screenshot-1.jpg
2. Register Page: screenshot-2.jpg
3. Backend Settings: screenshot-3.jpg

== Changelog ==

= 1.0.10 =
Bugfix - The wp_update_user attempts to clear and reset cookies if it's updating the password.
 Because of that we get "headers already sent". Fixed by hooking into the init.

= 1.0.9 =
Bugfix - On the edit profile page the website field added a new http:// everytime you updated your profile.
Bugfix/ExtraFeature - Add support for shortcodes to be run in a text widget area.

= 1.0.6 =
Apparently the WordPress.org svn converts my EOL from Windows to Mac and because of that you get "The plugin does not have a valid header."

= 1.0.5 =
You can now actualy install the plugin. All because of a silly line break.

= 1.0.4 =
Still no Change.

= 1.0.3 =
No Change.

= 1.0.2 =
Small changes.

= 1.0.1 =
Changes to the ReadMe File

= 1.0 =
Added the posibility of displaying/hiding default WordPress information-fields, and to modify basic layout.