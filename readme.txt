=== Ank Google Map ===
Tags: google map, responsive, light weight, ank, free, easy map
Requires at least: 3.8.0
Tested up to: 4.4.0
Stable tag: 1.7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Contributors: ank91

Simple and non-bloated WordPress Google Map Plugin.

== Description ==
One Website , One Map , One Marker.
Simple and non-bloated WordPress Google Map Plugin.
Written in pure javascript, no jQuery at all, responsive, configurable, no ads and 100% Free of cost.


= Notable Features =
* Adjust map canvas height and width.
* Responsive map, auto center map upon resize.
* Configure map canvas border color.
* Disable/Enable map controls.
* Find your location by typing address (Auto complete)
* Change map's language eg:Hindi/Urdu
* Place animated and colorful marker on map
* Place info window on marker with custom text/markup.
* Disable dragging on mobile devices
* Disable mouse wheel zoom

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
It uses inbuilt WP dash-icons in Plugin Options Page.
It uses inbuilt WP text editor on Plugin Options Page.
It does not create additional tables in your database, uses inbuilt 'wp_options' table.
The whole package is about 100 kb. (~30 kb zipped).

= What do you mean by Non Bloated =

There are many of Map plugins in plugin directory, but most of them not written well.
Means they put lots of java script (uncompressed) code on every page of your website.
They also loads jquery file before them which effects your page speed.
This plugin will enqueue its js files on the required page only.
It does not depends on external js library like: jQuery.

= What is the short-code for this plugin =

`[ank_google_map]`

= Map controls does not shown correctly on front-end =

Add this css code to your theme's style.css file to fix this

`
.gmnoprint img, #agm_map_canvas img { max-width: none; }

`
= Shortcode does not work in text widget =

Add this line to your theme's functions.php
`add_filter( 'widget_text', 'do_shortcode' );`

= Changes does not reflect after saving settings ? =

Are you using some Cache/Performance plugin (eg:WP Super Cache/W3 Total Cache) ?
Then flush your WP cache and refresh target page.

= Where does it store settings and options ? =

WP Database->wp-options->ank_google_map.
Uses a single row, stored in array for faster access.

= From where does it loads additional Marker (colored) images ? =

Every marker image is loaded from official Google Server.

= What if i uninstall/remove this plugin? =

No worry! It will remove its traces from database upon uninstall.
You have to remove short-code from your pages by yourself.

= How do i enter correct language code ? =

You can force google to load a specific language for all visitors.
Get latest supported language code list from [here](https://developers.google.com/maps/faq#languagesupport).
If you don't specify language code then google will try to load the language requested by visitor's web browser.

= How to make it responsive =

Set Map Canvas Width to 100 %. Map will auto center upon resize.

= I don't want border on map canvas =

Leave the color field empty and it will not be applied.

= Did you test it with old version of WordPress ? =

No, tested with v4.0+ (latest as of now) only. So i recommend you to upgrade to latest WordPress today.

= Do you hate jQuery ? =

No, I love it as like you. But I prefer faster websites.


= Is Google Map API is free. =

Until we break its terms and conditions.
Google Map API V3 does not need an API Key.


= Failed to load Google Map. Refresh this page and try again. What is this ? =

It means 'Google Map API' is not loaded.

Possible reasons are -

* No internet connection. (Internet is must).
* Other plugin's java script conflict. (Try disabling them one by one).
* This plugin has a problem/bug. (Report it now).


= Future Plans ? =

* I18n for Option Page.
* Multiple Maps with Multiple Markers.
* Multi-site support
* Improved upgrade paths.

== Upgrade Notice ==

No big changes yet in this plugin, so go ahead and upgrade to new version whenever i release.
It just a matter of a second. It will cost not more than 15 KB.

== Screenshots ==
1. Plugin Option Page Screen

== Changelog ==

= 1.7.4 =
* Minor bug fixes

= 1.7.3 =
* Minor bug fixes
* Tested upto WordPress v4.4.0

= 1.7.2 =
* More adjustment due to recent changes, remove unused code
* Using WP inbuilt Settings API to handle form data
* Updated to Google Map API v3.22, read more [here](https://developers.google.com/maps/articles/v322-controls-diff)
* Removed options : Pan control, Overview Map control

= 1.7.1 =
* Fix drag on mobile option stopped working

= 1.7.0 =
* Minimum php requirement : version 5.3.0
* Removed top screen options, always load text editor
* Removed bloated code, speed improvement

= 1.6.3 =
* Fix Option page not working due to wrong js url

= 1.6.2 =
* Tested upto WordPress 4.3.1
* Fix links and updated docs

= 1.6.1 =
* Disable drag on mobile
* Disable Mouse wheel zoom

= 1.6.0 =
* Tested upto wp v4.2.2
* Minor adjustments

= 1.5.9 =
* Execution Speed Improvements

= 1.5.8 =
* Tested upto WP v4.1
* Enqueue the minified version of js file to option page.
* Miner fixes

= 1.5.7 =
* Option page has it own separate class  (easy to manage code)
* Few More Improvements

= 1.5.6 =
* Add Plugin version to database for future use.
* Java Script Localization for options page.
* Store options page JS code to a separate file. (allow browsers to cache this file)
* Now we enqueue our main JS file on target page. (allow browsers to cache this file)
* JS priority parameter has been removed from short-code.
* Code optimization and many other improvements.

= 1.5.5 =
* Bug Fix - Screen options were not saving settings

= 1.5.4 =
* Using WP inbuilt text editor to edit info window text.
* Increase Info Window Text length to 1000 chars
* Added Screen Option, let user disable text editor
* Load Map's js after other js code. Map's js has lowest (100) priority by default.
  User can disable this behaviour. Read FAQ for more.
* Options Page Slug Changed

= 1.5.3 =
* Bug fix - link to 'readme.txt' was causing malfunction on plugin list page.
* Few more adjustments

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
