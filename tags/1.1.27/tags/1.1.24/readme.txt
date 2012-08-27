=== Profile Builder === 

Contributors: reflectionmedia, barinagabriel
Donate link: http://www.cozmoslabs.com/wordpress-profile-builder/
Tags: registration, profile, user registration, custom field registration, customize profile, user fields, builder, profile builder, custom profile, user profile, custom user profile, user profile page, 
custom registration, custom registration form, custom registration page, extra user fields, registration page, user custom fields, user listing, user login, user registration form, front-end login, 
front-end register, front-end registration, frontend edit profile, edit profile
Requires at least: 3.1
Tested up to: 3.3
Stable tag: 1.1.24

Simple to use profile plugin allowing front-end login, registration and edit profile by using shortcodes. 
 
== Description ==

Profile Builder is WordPress registration done right. 

It lets you customize your website by adding a front-end menu for all your users, 
giving them a more flexible way to modify their user-information or register new users (front-end registration). 
Also, grants users with administrator rights to customize basic user fields or add custom ones. 

To achieve this, just create a new page and give it an intuitive name(i.e. Edit Profile).
Now all you need to do is add the following shortcode(for the previous example): [wppb-edit-profile]. 
Publish the page and you are done!

You can use the following shortcodes:

* **[wppb-edit-profile]** - to grant users front-end access to their personal information (requires user to be logged in).
* **[wppb-login]** - to add a front-end log-in form.
* **[wppb-register]** - to add a front-end registration form.
* **[wppb-recover-password]** - to add a password recovery form.

Users with administrator rights have access to the following features:

* add a custom stylesheet/inherit values from the current theme or use one of the following built into this plugin: default, white or black.
* select whether to display or not the admin bar in the front end for a specific user-group registered to the site.
* select which information-field can users see/modify. The hidden fields values remain unmodified.

**PROFILE BUILDER PRO**

The [Pro version](http://www.cozmoslabs.com/wordpress-profile-builder/) has the following extra features:

* Create Extra User Fields (Heading, Input, Checkbox, Agree to Terms Checkbox, Radio Buttons, DatePicker, Textareas, Upload fields, Selects, Country Selects, Timezone selects, Avatar Upload)
* Add avatar upload for users
* Front-end User Listing (sorting included)
* Custom Redirects
* Select one of the 2 additional CSS styles: black or white
* Access to support forums and documentation
* 1 Year of Updates / Priority Support

[Click here to find out more](http://www.cozmoslabs.com/wordpress-profile-builder/) or watch the video below:

[youtube http://www.youtube.com/watch?v=Uv8piGapOoA]

NOTE:

This plugin only adds/removes fields in the front-end. The default information-fields will still be visible (and thus modifiable) from the back-end, while custom fields will only be visible in the front-end.
	


== Installation ==

1. Upload the profile-builder folder to the '/wp-content/plugins/' directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Create a new page and use one of the shortcodes available. Publish the page and you're done!

== Frequently Asked Questions ==

= I navigated away from Profile Builder and now I can’t find it anymore; where is it? =
	
	Profile Builder can be found in the default menu of your WordPress installation under the “Users” sub-menu.

= Why do the custom WordPress fields still show up, even though I set it to be "hidden"? =

	Profile Builder only disables the default fields in the front-end of your site/blog, it does absolutely nothing in the dashboard.
 

= I can’t find a question similar to my issue; Where can I find support? =

	For more information please visit http://www.cozmoslabs.com and check out the faq section from Profile Builder


== Screenshots ==
1. Basic information: screenshot1.jpg
2. Layout Control: screenshot2.jpg
3. Show/Hide Admin Bar: screenshot3.jpg
4. Select Default User Fields: screenshot4
6. Register Page: screenshot6.jpg
7. Logged in Page: screenshot7.jpg

== Changelog ==
= 1.1.24 = 
Wordpress 3.3 support

= 1.1.23 =
Consecutive bugfixes.

= 1.1.14 =
Compatibility fix for WP version 3.3

= 1.1.13 = 
Minor changes to different parts of the plugin. Also updated the english translation.

= 1.1.12 = 
Minor changes to readme file.

= 1.1.11 = 
Minor changes to readme file.

= 1.1.10 = 
Minor changes to readme file.

= 1.1.9 = 
Minor changes to readme file.

= 1.1.8 =
Added the possibility to set the default fields as required (only works in the front end for now), and added a lot of new filters for a better and easier way to personalize the plugin. Also added a recover password feature (shortcode) to be in tune with the rest of the theme.
Added translations:
*italian (thanks to Gabriele, globalwebadvices@gmail.com)
*updated the english translation

= 1.1.7 =
Minor modification in the readme file.

= 1.1.6 =
Minor upload bug on WP repository. 

= 1.1.5 =
Added translations:
*czech (thanks to Martin Jurica, martin@jurica.info)
*updated the english translation

= 1.1.4 =
Added the possibility to set up the default user-role on registration; by adding the role="role_name" argument (e.g. [wppb-register role="editor"]) the role is automaticly set to all new users. 
Added translations:
*norvegian (thanks to Havard Ulvin, haavard@ulvin.no)
*dutch (thanks to Pascal Frencken, pascal.frencken@dedeelgaard.nl)
*german (thanks to Simon Stich, simon@1000ff.de)
*spanish (thanks to redywebs, www.redywebs.com) 
 

= 1.1.3 =
Minor bugfix.

= 1.1.2 =
Added translations to: 
*hungarian(thanks to Peter VIOLA, info@violapeter.hu)
*french(thanks to Sebastien CEZARD, sebastiencezard@orange.fr)

Bugfixes/enhancements:
*login page now automaticly refreshes itself after 1 second, a little less annoying than clicking the refresh button manually
*fixed bug where translation didn't load like it should
*added new user notification: the admin will now know about every new subscriber
*fixed issue where adding one or more spaces in the checkbox options list, the user can't save values.


= 1.1 =
Added a new user-interface (borrowed from the awesome plugin OptionTree created by Derek Herman), and bugfixes.

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

