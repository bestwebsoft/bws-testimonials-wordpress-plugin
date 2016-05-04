=== Testimonials by BestWebSoft ===
Contributors: bestwebsoft
Donate link: http://bestwebsoft.com/donate/
Tags: add testimonial, add testimonials plugin, add testimonials free plugin, add testimonials, author data, company data, create testimonials, custom post type, display testimonials, free, free plugin, free testimonials plugin, multiple testimonials, plugin, testimonial, testimonial shortcode, Testimonials plugin, testimonials widget, widget, wordpress, wp, wp plugin, wp free plugin, wp testimonials, wp testimonials plugin, wp simple testimonials plugin, wp free testimonials, wp free testimonials plugin, wordpress plugin, wordpress free plugin, wordpress testimonials, wordpress testimonials plugin, wordpress simple testimonials plugin, wordpress free testimonials, wordpress free testimonials plugin
Requires at least: 3.8
Tested up to: 4.5
Stable tag: 0.1.6
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Plugin for displaying Testimonials.

== Description ==

This plugin allows creating and displaying a Testimonial on your website. This testimonial can be displayed using the shortcode, a widget or by adding a code to your theme's template. 

http://www.youtube.com/watch?v=y-9_ThXTUS8

<a href="http://www.youtube.com/watch?v=YMPuEmLELfk" target="_blank">Testimonials by BestWebSoft Video instruction on Installation</a>

<a href="http://wordpress.org/plugins/bws-testimonials/faq/" target="_blank">Testimonials by BestWebSoft FAQ</a>

<a href="http://support.bestwebsoft.com" target="_blank">Testimonials by BestWebSoft Support</a>

= Features =

* Create a testimonial via custom post type.
* Add author and company data to each testimonial.
* Display testimonials through the widget or using the shortcode. 
* Choose a number of testimonials to be displayed.

If you have a feature, suggestion or idea you'd like to see in the plugin, we'd love to hear about it! <a href="http://support.bestwebsoft.com/hc/en-us/requests/new" target="_blank">Suggest a Feature</a>

= Recommended Plugins =

The author of the Testimonials also recommends the following plugins:

* <a href="http://wordpress.org/plugins/updater/">Updater</a> - This plugin updates WordPress core and the plugins to the recent versions. You can also use the auto mode or manual mode for updating and set email notifications.
There is also a premium version of the plugin <a href="http://bestwebsoft.com/products/updater/">Updater Pro</a> with more useful features available. It can make backup of all your files and database before updating. Also it can forbid some plugins or WordPress Core update.

= Translation =

* Hungarian (hu_HU) (thanks to <a href="mailto:solarside09@gmail.com">Peter Aprily</a> www.aprily.com)
* Russian (ru_RU)
* Ukrainian (uk)

If you would like to create your own language pack or update the existing one, you can send <a href="http://codex.wordpress.org/Translating_WordPress" target="_blank">the text of PO and MO files</a> to <a href="http://support.bestwebsoft.com" target="_blank">BestWebSoft</a>, and we'll add it to the plugin. You can download the latest version of the program for working with PO and MO files  <a href="http://www.poedit.net/download.php" target="_blank">Poedit</a>.

= Technical support =

Dear users, our plugins are available for free download. If you have any questions or recommendations regarding the functionality of our plugins (existing options, new options, current issues), please feel free to contact us. Please note that we accept requests in English only. All messages in other languages won't be accepted.

If you notice any bugs in the plugin's work, you can notify us about them and we'll investigate and fix the issue then. Your request should contain website URL, issues description and WordPress admin panel credentials.
Moreover, we can customize the plugin to match your requirements. It's a paid service (as a rule it costs $40, but the price can vary depending on the amount of the necessary changes and their complexity). Please note that we could also include this or that feature (developed for you) in the next release and share it with the other users then.
We can fix some things for free for the users who provide a translation of our plugin into their native language (this should be a new translation of a certain plugin, you can check available translations on the official plugin page).

== Installation == 

1. Upload the `bws-testimonials` folder to `/wp-content/plugins/` directory.
2. Activate the plugin using the 'Plugins' menu in your WordPress admin panel.
3. You can add the testimonial using your WordPress admin panel in "Testimonials" > "Add New".
4. You can add "Testimonials Widget" to the necessary sidebar on "Appearance" > "Widgets" page, or copy and paste this shortcode into your post or page: [bws_testimonials].

<a href="https://docs.google.com/document/d/1DrmEayBxjXaBVEvwOQjT8Gdpiy5vWrl2XNKDNuhM9qA/edit" target="_blank">View a Step-by-step Instruction on Testimonials Installation</a>.

http://www.youtube.com/watch?v=YMPuEmLELfk

== Frequently Asked Questions ==

= How can I add Testimonials to my website? =

1. Please create a testimonial using your WordPress admin panel in "Testimonials" > "Add New".
2. There are 3 ways to add Testimonials:

- You can add "Testimonials Widget" to the necessary sidebar on "Appearance" > "Widgets" page.
- You can copy and paste this shortcode into your post or page: [bws_testimonials]
- Paste the following strings into the template source code `<?php if ( has_action( 'tstmnls_show_testimonials' ) ) { do_action( 'tstmnls_show_testimonials' ); } ?>`

= I have added Testimonials Widget to the sidebar, yet nothing changed =

Please create the testimonial using your WordPress admin panel in "Testimonials" > "Add New".

= I set 5 in 'Number of testimonials to be displayed,' but only three testimonials are currently displayed. Why so? =

To have more testimonials displayed on your website, you need to add them using your WordPress admin panel in "Testimonials" > "Add New".

= I have some problems with the plugin's work. What Information should I provide to receive proper support? =

Please make sure that the problem hasn't been discussed on our forum yet (<a href="http://support.bestwebsoft.com" target="_blank">http://support.bestwebsoft.com</a>). If not, please provide the following data along with your problem's description:

1. the link to the page, on which the problem occurs
2. the plugin’s name and version. If you are using a pro version - your order number.
3. the version of your WordPress installation
4. copy and paste your system status report into the message . Please read more here: <a href="https://docs.google.com/document/d/1Wi2X8RdRGXk9kMszQy1xItJrpN0ncXgioH935MaBKtc/edit" target="_blank">Instruction on System Status</a>

== Screenshots ==

1. Testimonials Settings page.
2. Testimonials Widget in WordPress admin panel.
3. 'Add New Testimonial' page in WordPress admin panel.
4. Testimonials Widget display.

== Changelog ==

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
