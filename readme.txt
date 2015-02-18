=== Profile Builder - front-end user registration, login and edit profile === 

Contributors: reflectionmedia, barinagabriel, sareiodata, cozmoslabs, adispiac, madalin.ungureanu
Donate link: http://www.cozmoslabs.com/wordpress-profile-builder/
Tags: registration, user profile, user registration, custom field registration, customize profile, user fields, extra user fields, builder, profile builder, custom user profile, user profile page, edit profile, custom registration, custom registration form, custom registration page, registration page, user custom fields, user listing, front-end user listing, user login, user registration form, front-end login, login redirect, login widget, front-end register, front-end registration, front-end edit profile, front-end user registration, custom redirects, user email, avatar upload, email confirmation, user approval, customize registration email, minimum password length, minimum password strength, password strength meter, multiple registration forms, register, register form, member directory

Requires at least: 3.1
Tested up to: 4.1
Stable tag: 2.1.2

Simple to use profile plugin allowing front-end login, user registration and edit profile by using shortcodes.
 
== Description ==

**Profile Builder is WordPress user registration done right.**

It lets you customize your website by adding a front-end menu for all your users, 
giving them a more flexible way to modify their user profile or register new users (front-end user registration). 
Users with administrator rights can customize basic user fields or add custom user fields to the front-end forms.

To achieve this, simply create a new page and give it an intuitive name(i.e. Edit Profile).
Now all you need to do is add the following shortcode: [wppb-edit-profile].
Publish the page and you are done!

= Front-end User Registration, Login, Edit Profile and Password Recovery Shortcodes =
You can use the following shortcode list:

* **[wppb-edit-profile]** - to grant users front-end access to their profile (requires user to be logged in).
* **[wppb-login]** - to add a front-end login form.
* **[wppb-logout]** - to add logout functionality.
* **[wppb-register]** - to add a front-end register form.
* **[wppb-recover-password]** - to add a password recovery form.

Users with administrator rights have access to the following features:

* drag & drop to reorder user profile fields
* enable **Email Confirmation** (on registration users will receive a notification to confirm their email address).
* allow users to **Log-in with their Username or Email**
* enforce a **minimum password length** and **minimum password strength** (using the default WordPress password strength meter)
* assign users a specific role at registration (using **[wppb-register role="desired_role"]** shortcode argument for the register form)
* redirect users after login, register and edit-profile using redirect_url shortcode argument ( e.g **[wppb-login redirect_url="www.example.com"]** )
* add register and lost password links below the login form (using **[wppb-login register_url="www.example.com" lostpassword_url="www.example.com"]** shortcode arguments)
* customizable login widget
* add a custom stylesheet/inherit values from the current theme or use the default one built into this plugin.
* chose which user roles view the admin bar in the front-end of the website (Admin Bar Settings page).
* select which profile fields users can see/modify.
* extended functionality available via [Add-ons](http://www.cozmoslabs.com/profile-builder-add-ons/?utm_source=wp.org&utm_medium=plugin-description-page&utm_campaign=PBFree)

**PROFILE BUILDER PRO**

The [Pro version](http://www.cozmoslabs.com/wordpress-profile-builder/?utm_source=wp.org&utm_medium=plugin-description-page&utm_campaign=PBFree) has the following extra features:

* Create Extra User Fields (Heading, Input, Hidden-Input, Checkbox, Agree to Terms Checkbox, Radio Buttons, DatePicker, Textareas, WYSIWYG, reCAPTCHA, Upload fields, Selects, User Role Select, Country Selects, Timezone selects, Avatar Upload)
* Add Avatar Upload for users
* Front-end User Listing (fully customizable, sorting included)
* Create Multiple User Listings
* Custom Redirects
* Multiple Registration Forms (set up multiple registration forms with different profile fields for certain user roles)
* Multiple Edit Profile Forms
* Admin Approval
* Email Customizer (Personalize all emails sent to your users or admins; customize default WordPress registration email)
* reCAPTCHA on user registration form
* Advanced Modules (e.g. custom redirects, user listing, multiple registration forms etc.)
* Access to support forums and documentation
* 1 Year of Updates / Priority Support

[Find out more about Profile Builder PRO](http://www.cozmoslabs.com/wordpress-profile-builder/?utm_source=wp.org&utm_medium=plugin-description-page&utm_campaign=PBFree)


**Profile Builder Add-ons**

For more functionality check out Profile Builder [Add-ons page](http://www.cozmoslabs.com/profile-builder-add-ons/?utm_source=wp.org&utm_medium=plugin-description-page&utm_campaign=PBFree)

= Profile Builder in your Language =
We're focusing on translating Profile Builder in as many languages as we can. So far, the translations for 10 languages are almost complete, but we still need help on a lot of other languages, so please join us at [translate.cozmoslabs.com.](http://translate.cozmoslabs.com/projects/profilebuilder)
You will be able to download all the [available language packs](http://translate.cozmoslabs.com/projects/profilebuilder) as well as help us translate Profile Builder in your language.

NOTE:
This plugin adds/removes user fields in the front-end. Both default and extra profile fields will be visible in the back-end as well.
	


== Installation ==

1. Upload the profile-builder folder to the '/wp-content/plugins/' directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Create a new page and use one of the shortcodes available. Publish the page and you're done!

== Frequently Asked Questions ==

= I navigated away from Profile Builder and now I can�t find it anymore; where is it? =

	Profile Builder can be found in the default menu of your WordPress installation below the �Users� menu item.

= Why do the default WordPress user fields still show up in the back-end? =

	Profile Builder can only remove the default user fields in the front-end of your site/blog, it doesn't remove them from the back-end.

= I can’t find a question similar to my issue; Where can I find support? =

	For more information please visit http://www.cozmoslabs.com and check out the documentation section from Profile Builder - front-end user registration plugin.


== Screenshots ==
1. Basic Information - Profile Builder, front-end user registration plugin
2. General Settings - Profile Builder, front-end user registration plugin
3. Show/Hide Admin Bar
4. Profile Builder - Manage Default User Fields (Add, Edit or Delete)
5. Profile Builder - Drag & Drop to Reorder User Profile Fields
6. Register Form - Front-end User Registration Page
7. User Login Page
8. Edit User Profile Page
9. Recover Password Page
10. Profile Builder Login Widget

== Changelog ==
= 2.1.2 =
* Created Add-On Page in Profile Builder
* Added support for Twenty Fifteen theme to better target inputs
* Add support for "redirect_url" parameter to Login shortcode (will do the same thing as "redirect" - for consistency)
* Added "redirect_url" parameter to Register and Edit-profile shortcodes

= 2.1.1 =
* Added username validation for illegal characters
* Fixed wp_mail() From headers being set sitewide

= 2.1.0 =
* Added option to Log In with either Username or Email.
* Added default values for "Logout" shortcode so it displays even if you don't pass any arguments to it.

= 2.0.9 =
* Fixed bug that was causing the username to be sent instead of the email when login with email was set to true in the default registration emails.
* Fixed bug in Password Reset email when Login with email was on.
* The "This email is already reserved to be used soon" error wasn't appearing on single site when Email Confirmation was on. Now it does when it is the case.
* Fixed bug that was causing an upload incompatibility with WordPress media uploader.
* Fixed bug that was causing Password strength and Password length error messages to not be translatable.
* Interface changes to forms in admin area on Profile Builder Pages.
* Added possibility to edit other users from the front end edit form when an admin is logged in.
* Added a popup in unconfirmed email user listing in admin area where the admin can see the users meta information.
* Add logout shortcode and menu link to Profile Builder.

= 2.0.8 =
* Fixed problem that when Email Confirmation was active the password in the registration emails was empty. We now have a placeholder for when we can't send the actual password.
* Added 'wppb_login_form_args' filter to filter wp_login_form() arguments.
* Added css classes to loged in message links so we can style.
* Fixed bug that was allowing us to change meta_name on un-editable fields:First Name, Last Name etc.
* Fixed "Display Name Publicly as” field on front-end.
* Now User Email Confirmation works on multisite as expected.
* Fixed bug that was throwing “This email is already reserved to be used soon” ERROR on Edit Profile form on multisite.
* Fixed bug that caused metaboxes and the Profile Builder page to appeared for roles that shouldn't have.

= 2.0.7 =
* Added icon with tooltip on registration pages 'Users can register themselves or you can manually create users here' message
* Updated translation files
* Removed some php notices from the code-base
* Improved theme compatibility for the submit buttons inside the Profile Builder forms
* Removed UL dots from Register form in Chrome, Safari

= 2.0.6 =
* Fixed a bug with checkbox field that didn't pass the required if the value of the checkbox contained spaces
* When email confirmation is enabled we no longer can send the selected password via email because we now store the hased password inside wp-signups table and not a encoded version of it. This was done to improve security
* Fixed problem that was causing "Insert into post" image button not to work
* Fixed Fatal error when having both Free and Premium versions activated.
* Removing the meta name for extra fields is no longer possible
* Added translation files


= 2.0.5 =
* Added notification to enable user registration via Profile Builder (Anyone can register checkbox).
* Add register_url and lostpassword_url parameters to login shortcode.
* Added filter to allow changing Lost Password link in login shortcode.

= 2.0.4 =
* Added $account_name as a parameter in the wppb_register_success_message filter
* Fixed typo in password strength meeter.

= 2.0.3 =
* Fixed bug that made radio buttons field types not to throw error when they are required
* Fixed XSS security vulnerability in fallback-page.php
* Reintroduced the filters:'wppb_generated_random_username', 'wppb_userlisting_extra_meta_email' and 'wppb_userlisting_extra_meta_user_name'
* Fixed the bug when changing the password in a edit profile form we were logged out

= 2.0.2 =
* Brand new user interface.
* Drag & drop to reorder User Profile Fields.
* More flexibility for Managing Default User Fields.
* Better Security by Enforcing Minimum Password Length and Minimum Password Strength on all forms (front-end and back-end).
* NOTE: upgrading from Profile Builder 1.1.x to 2.0.2 might make some of your plugin customization (if you have any ) not work because in the restructuring we had to drop some of the filters from 1.1.x.

= 1.1.67 =
* Added stripslashes to register shortcode.

= 1.1.66 =
* Sanitized forms against XSS exploits.

= 1.1.65 =
* Minor changes in the readme and index files.

= 1.1.64 =
* Minor changes in the readme and index files.

= 1.1.63 =
* Changes weren't saving on the Edit Profile page when profile was not fully updated.

= 1.1.62 =
* Minor changes to the readme file.

= 1.1.61 =
* Fixed a few notices in the plugin.

= 1.1.60 =
* Emergency security update regarding the password recovery feature.

= 1.1.59 =
Improved some of the queries meant to select users at certain points, hidden input value on front-end (Pro version) and the remember me checkbox on the login page.

= 1.1.58 =
Small changes to the index.php file

= 1.1.57 =
Fixed some bugs which only appeared in WPMU sites.

= 1.1.57 =
Minor changes to the readme.txt file.

= 1.1.56 =
Added activation_url and activation_link to the "Email Customizer" feature (pro). Also, once the "Email Confirmation" feature is activated, an option will appear to select the registration page for the "Resend confirmation email" feature, which was also added to the back-end userlisting.

= 1.1.55 =
Minor changes in the plugin's readme file and updated the screenshots.

= 1.1.54 =
Minor changes in the plugin's readme file.

= 1.1.53 =
Minor changes in the plugin's readme file.

= 1.1.52 =
Minor changes in the plugin's readme file.

= 1.1.51 =
Minor changes in the plugin's readme file.

= 1.1.50 =
Improved the userlisting feature in the Pro version.

= 1.1.49 =
Fixed "Edit Profile" bug and impred the "Admin Approval" default listing (in the paid versions).

= 1.1.48 =
Improved a few existing features (like WPML compatibility), and added a new feature: login with email address.

= 1.1.47 =
Improved the "Email Confirmation" feature and a few other functions.
Added new options for the "Userlisting" feature (available in the Pro version of Profile Buildeer).
Added translations: persian (thanks to Ali Mirzaei, info@alimir.ir)

= 1.1.46 =
Improved a few existing functions.

= 1.1.45 =
Fixed a few warnings on the register page.

= 1.1.44 =
Minor changes to the readme file.

= 1.1.43 =
Activation bug fixed.

= 1.1.42 =
Added a few improvements and fixed a few bugs.

= 1.1.41 =
Email Confirmation bug on WPMU fixed.

= 1.1.40 =
Minor changes to the readme file.

= 1.1.39 =
Security issue fixed regarding the "Email Confirmation" feature

= 1.1.38 =
Added a fix (suggested by http://wordpress.org/support/profile/maximinime) regarding the admin bar not displaying properly in some instances.

= 1.1.37 =
Minor changes to the readme file.

= 1.1.36 =
Minor changes to the readme file.

= 1.1.35 =
Added support for WP 3.5

= 1.1.34 =
Separated some of the plugin's functions into separate files. Also fixed a few bugs.

= 1.1.33 =
Fixed function where it wouldn't create the _signups table in the free version.

= 1.1.32 =
Error fixed.

= 1.1.31 =
Minor updates to the plugin files.

= 1.1.30 =
Minor changes to the plugin.

= 1.1.29 =
Minor changes to the readme file.

= 1.1.28 =
Changes to the readme file.

= 1.1.27 =
Fixed few warnings.

= 1.1.26 =
Minor changes

= 1.1.25 =
Different security issues fixed with other updates.

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

