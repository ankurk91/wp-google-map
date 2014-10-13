=== Ank Google Map ===
Tags: google map, responsive, light weight, ank, free, easy map
Requires at least: 3.8.0
Tested up to: 4.0
Stable tag: 1.5.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Contributors:ank91

Simple and non-bloated WordPress Google Map Plugin.

== Description ==
One Website , One Map , One Marker.
Simple and non-bloated WordPress Google Map Plugin.
Written in pure javascript, no jQuery at all, responsive, configurable, no ads and 100% Free of cost.
Official Site : http://ank91.github.io/ank-google-map

= Some Features =
* Adjust map canvas height and width.
* Responsive map, auto center map upon resize.
* Configure map canvas border color.
* Disable/Enable map controls.
* Find your location by typing address (Auto complete)
* Change map's language eg:Hindi/Urdu
* Place animated and colorful marker on map
* Place info window on marker with custom text/markup.


== Installation ==
1. Search for 'ank google map' in WordPress Plugin Directory and Download the .zip file & extract it.
2. Upload the folder `ank-google-map` to the `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins List' page in WordPress Admin Area.
4. Configure this plugin via Settings-->Ank Google Map
5. Paste the `[ank_google_map]` short-code in your pages/posts/widgets.


== Frequently Asked Questions ==

= Why did you call it Light Weight ? =

Because it does not depend on jQuery, written in pure Java Script.
Options page utilize inbuilt jQuery and Color Picker.
It uses WP dash-icons in Plugin Options Page.
It does not create additional tables in your database, uses inbuilt wp_options table.
The whole package is about 50 kb. (~12 kb zipped).

= What do you mean by Non Bloated =

There are many of Map plugins in plugin directory, but most of them not written well.
Means they put lots of java script (uncompressed) code on every page of your website.
They also loads jquery file before them which effect your page speed.
This plugin will put its code on the required page only.
It will write compressed java script code, and does not depends on external js library like: jQuery.

= What is the short-code for this plugin =

`[ank_google_map]`

= Options page does not work well :( =

You must have modern browser to configure the map option.
Old browsers will not work well.

= Color picker could not load :( =

This plugin utilize inbuilt WP Color API.
You must have WordPress v3.5+ in order to use this feature.

= Shortcode does not work in text widget =

Add this line to your theme's functions.php
`add_filter( 'widget_text', 'do_shortcode' );`

= Changes does not reflect after saving settings ? =

Are you using some Cache/Performance plugin ?
Flush your WP cache and refresh target page.

= Where does it store settings and options ? =

WP Database->wp-options->ank_google_map.
Uses a Single Row, stored in array for faster access.

= From where does it loads additional Marker (colored) images ? =

Every marker image is loaded from official Google Server.

= What if i uninstall/remove this plugin? =

No worry! It will remove its traces from database upon uninstall.
You have to remove short-code from your pages by yourself.

= How do i enter correct language code ? =
You can force google to load a specific language for all visitors.
Get latest supported language code list from [here](https://spreadsheets.google.com/pub?key=p9pdwsai2hDMsLkXsoM05KQ&gid=1).
If you don't specify language code then google will try to load the language according to your visitor's web browser language.

= How to make it responsive =

Set Map Canvas Width to 100 %.

= I don't want border on map canvas =

Choose a border color that match the map canvas surroundings.

= I don't want css fixes for map controls, how to disable them ? =
Use this short-code `[ank_google_map css_fix=0]`

= Did you test it with old version of WordPress ? =

No, tested with v4.0 (latest as of now) only. So i recommend you to upgrade to latest WordPress today.

= Do you hate jQuery ? =

No, I love it as like you. But I prefer faster websites.

= Can i modify this plugin ? =

Yes you can. But you can't make money by selling this. You can ask for donation.

= Any plan to support more than one map. =

Only if i got some time to code.

= Is Google Map API is free. =

Until we break its terms and conditions.
Google Map API V3 does not need an API Key.

= Future Plans ? =

* Localization for Option Page.
* More security approaches.
* More options.

== Upgrade Notice ==

== Screenshots ==
1. Plugin Option Page Screen

== Changelog ==

= 1.5.2 =
* Option page re-styled.
* Added Help Menu on top of option page.
* Removed Map Height Unit Option, Height will be in px always.
* Bug fix in marker color option.
* Options to disable css-fixes. (Read FAQ).

= 1.5.1 =
* Prevent form submission when user press Enter in auto complete
* Screenshot moved to assets, reduce package size
* More FAQ

= 1.5 =
* First release on WordPress Plugin Directory
* Add Search by Address (Auto complete)
* Add Marker Color option

= 1.4 =
* Fix controls appears incorrectly in certain conditions (css fixes)
* Code clean up

= 1.3 =
* Added notes about flushing cache
* Load Color API only on Option page
* Special checks for Color API

= 1.2 =
* Fix Bugs

= 1.1 =
* Fix Bugs
* Sanitize Inputs
* Allow HTML in info window

= 1.0 =
* First public beta



== Arbitrary section ==
Nothing in this section, Read FAQ.
