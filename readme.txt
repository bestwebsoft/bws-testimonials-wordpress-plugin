=== Testimonials by BestWebSoft ===
Contributors: bestwebsoft
Donate link: https://bestwebsoft.com/donate/
Tags: add testimonials, author data, company data, testimonials, testimonials plugin, display testimonials, multiple testimonials, testimonials shortcode, testimonials widget, add testimonials widget, custom post type
Requires at least: 3.9
Tested up to: 4.9.8
Stable tag: 0.2.4
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Add testimonials and feedbacks from your customers to WordPress website posts, pages, and widgets.

== Description ==

Simple plugin which helps to create and add customer testimonials to WordPress website posts, pages, and widgets. Display testimonials using shortcode, widget or PHP function.

Show what other people are saying about your business to generate more sales!

https://www.youtube.com/watch?v=y-9_ThXTUS8

= Features =
* GDPR Compliant
* Add testimonials form via shortcode
* Add unlimited number of testimonials
* Add testimonials block via shortcode
* Add testimonials widget
* Compatible with Google Captcha (reCAPTCHA) [NEW]
* Select the one who can submit new testimonials:
	* All users
	* Logged in users
* Customize testimonials additional info:
	* Author
	* Company name
* Set the number of testimonials to display
* Set the default testimonials sorting order by:
	* ID
	* Title
	* Date
	* Random
	* ASC (ascending order from lowest to highest values)
	* DESC (descending order from highest to lowest values)
* Compatible with latest WordPress version
* Incredibly simple settings for fast setup without modifying code
* Detailed step-by-step documentation and videos
* Multilingual and RTL ready

If you have a feature suggestion or idea you'd like to see in the plugin, we'd love to hear about it! [Suggest a Feature](https://support.bestwebsoft.com/hc/en-us/requests/new)

= Documentation & Videos =

* [[Doc] Installation](https://docs.google.com/document/d/1-hvn6WRvWnOqj5v5pLUk7Awyu87lq5B_dO-Tv-MC9JQ/)
* [[Video] Installation Instruction](https://www.youtube.com/watch?v=YMPuEmLELfk)

= Help & Support =

Visit our Help Center if you have any questions, our friendly Support Team is happy to help - <https://support.bestwebsoft.com/>

= Translation =

* Hungarian (hu_HU) (thanks to [Peter Aprily](mailto:solarside09@gmail.com) www.aprily.com)
* Russian (ru_RU)
* Ukrainian (uk)

Some of these translations are not complete. We are constantly adding new features which should be translated. If you would like to create your own language pack or update the existing one, you can send [the text of PO and MO files](https://codex.wordpress.org/Translating_WordPress) to [BestWebSoft](https://support.bestwebsoft.com/hc/en-us/requests/new) and we'll add it to the plugin. You can download the latest version of the program for work with PO and MO [files Poedit](https://www.poedit.net/download.php).

= Recommended Plugins =

* [Updater](https://bestwebsoft.com/products/wordpress/plugins/updater/?k=91123f9d92aeccd5ae253904a08c8c24) - Automatically check and update WordPress website core with all installed plugins and themes to the latest versions.
* [Google Captcha (reCAPTCHA)](https://bestwebsoft.com/products/wordpress/plugins/google-captcha/?k=50392a4147eefdfb1d4f7a754ece974c) – Protect WordPress website forms from spam entries with Google reCAPTCHA.

== Installation ==

1. Upload the `bws-testimonials` folder to `/wp-content/plugins/` directory.
2. Activate the plugin using the 'Plugins' menu in your WordPress admin panel.
3. You can add the testimonial using your WordPress admin panel in "Testimonials" > "Add New".
4. You can add "Testimonials Widget" to the necessary sidebar on "Appearance" > "Widgets" page, or copy and paste this shortcode into your post or page: [bws_testimonials].

[View a Step-by-step Instruction on Testimonials Installation](https://www.youtube.com/watch?v=YMPuEmLELfk/)

== Frequently Asked Questions ==

= How can I add Testimonials to my website? =

1. Please create a testimonial using your WordPress admin panel in "Testimonials" > "Add New".
2. There are 3 ways to add Testimonials:

- You can add "Testimonials Widget" to the necessary sidebar on "Appearance" > "Widgets" page.
- You can copy and paste this shortcode into your post or page: [bws_testimonials]
- Paste the following strings into the template source code `<?php if ( has_action( 'tstmnls_show_testimonials' ) ) { do_action( 'tstmnls_show_testimonials' ); } ?>`

= I have added Testimonials Widget to the sidebar, yet nothing changed =

Please create the testimonial using your WordPress admin panel in "Testimonials" > "Add New".

= I set 5 in 'Number of testimonials to be displayed', but only three testimonials are currently displayed. Why so? =

To have more testimonials displayed on your website, you need to add them using your WordPress admin panel in "Testimonials" > "Add New".

= I have some problems with the plugin's work. What Information should I provide to receive proper support? =

Please make sure that the problem hasn't been discussed yet on our forum (<https://support.bestwebsoft.com>). If no, please provide the following data along with your problem's description:

1. the link to the page where the problem occurs
2. the name of the plugin and its version. If you are using a pro version - your order number.
3. the version of your WordPress installation
4. copy and paste into the message your system status report. Please read more here: [Instruction on System Status](https://docs.google.com/document/d/1Wi2X8RdRGXk9kMszQy1xItJrpN0ncXgioH935MaBKtc/)

== Screenshots ==

1. Testimonials Widget display.
2. Testimonial form display.
3. Testimonials Settings page.
4. GDPR Compliance option.
5. GDPR Compliance display in Testimonials form.
6. Add Google Captcha option.
7. Google Captcha display in Testimonials form.
8. Testimonials Widget in WordPress admin panel.
9. 'Add New Testimonial' page in WordPress admin panel.

== Changelog ==

= V0.2.4 - 07.08.2018 =
* NEW : The compatibility with Google Captcha (reCAPTCHA) plugin has been added.

= V0.2.3 - 24.05.2018 =
* NEW : Ability to add GDPR Compliance checkbox has been added.

= V0.2.2 - 27.02.2018 =
* NEW : Ability to add testimonials form via shortcode has been added.
* NEW : Ability to select the one who can submit new testimonials has been added.

= V0.2.1 - 21.12.2017 =
* Bugfix : Number of testimonials to be displayed has been fixed.
* Bugfix : Options removal from the database when working on a multisite network has been fixed.

= V0.2.0 - 12.07.2017 =
* Update : We updated all functionality for wordpress 4.8.

= V0.1.9 - 17.04.2017 =
* Bugfix : Multiple Cross-Site Scripting (XSS) vulnerability was fixed.

= V0.1.8 - 12.10.2016 =
* Update : BWS plugins section is updated

= V0.1.7 - 20.07.2016 =
* Update : 'widget_title' filter was added.
* Bugfix : The display of unnecessary elements in Testimonials has been removed.

= V0.1.6 - 01.04.2016 =
* NEW : Testimonials featured image was added.
* Bugfix : Conflict with BuddyPress was fixed.

= V0.1.5 - 10.12.2015 =
* NEW : Testimonials sorting order was added.
* Bugfix : Shortcode displaying was fixed.
* Bugfix : The bug with plugin menu duplicating was fixed.

= V0.1.4 - 03.11.2015 =
* NEW : A button for Testimonials shortcode inserting to the content was added.
* NEW : Hungarian language file is added to the plugin.
* Update : Textdomain was changed.
* Update : We updated all functionality for wordpress 4.3.1.

= V0.1.3 - 31.07.2015 =
* New : Ability to restore settings to defaults.
* Bugfix : We fixed the bug with widgets translation in the Admin Panel.
* Update : Input maxlength is added.
* Update : We updated all functionality for wordpress 4.2.3.

= V0.1.2 - 26.05.2015 =
* Update : We updated all functionality for wordpress 4.2.2

= V0.1.1 - 07.04.2015 =
* Update : We updated all functionality for wordpress 4.2-beta4
* Update : BWS plugins section is updated.

= V0.1 - 27.01.2015 =
* Bugfix : The code refactoring was performed.
* NEW : Css-style was added.

== Upgrade Notice ==

= V0.2.4 =
* Functionality expanded.

= V0.2.3 =
* Functionality improved.

= V0.2.2 =
* Functionality expanded.
* The compatibility with new WordPress version updated.

= V0.2.1 =
* Bugs fixed.

= V0.2.0 =
* The compatibility with new WordPress version updated.

= V0.1.9 =
* Bugs fixed.

= V0.1.8 =
* Plugin optimization completed.

= V0.1.7 =
* Usability improved.
* Bugs fixed.

= V0.1.6 =
Testimonials feautured image was added. Conflict with BuddyPress was fixed.

= V0.1.5 =
Testimonials sorting order was added. Shortcode displaying was fixed. The bug with plugin menu duplicating was fixed.

= V0.1.4 =
A button for Testimonials shortcode inserting to the content was added. Hungarian language file is added to the plugin. Textdomain was changed. We updated all functionality for wordpress 4.3.1.

= V0.1.3 =
Ability to restore settings to defaults. We fixed the bug with widgets translation in the Admin Panel. Input maxlength is added. We updated all functionality for wordpress 4.2.3

= V0.1.2 =
We updated all functionality for wordpress 4.2.2

= V0.1.1 =
We updated all functionality for wordpress 4.2-beta4. BWS plugins section is updated.

= V0.1 =
The code refactoring. Css-style was added.
