=== Google Map ===
Tags: google map, map, responsive, light weight, free, easy
Requires at least: 4.0.0
Tested up to: 4.8.0
Stable tag: 2.6.0
License: MIT
License URI: https://opensource.org/licenses/MIT
Contributors: ankurk91

Simple, light-weight and non-bloated WordPress Google Map Plugin.

== Description ==
Simple, light-weight and non-bloated Google Map Plugin for WordPress.<br>
Written in pure javascript, no jQuery at all, responsive, configurable, no ads and 100% free of cost.


= Notable Features =
* Add Google Map API key
* Adjust map canvas height and width
* Responsive map, auto center map upon resize
* Configure map canvas border color
* Disable/Enable map controls
* Find your location by typing address (Auto complete)
* Change map's language eg: Hindi
* Place animated and colorful marker on map
* Place info window on marker with custom text/markup.
* Disable dragging on mobile devices / touch enabled devices
* Disable mouse wheel zoom
* Map Style - eg: Grayscale
* Custom marker icon/image file
* Cooperative Gesture Handling (Two fingers zoom on mobile devices)

= php v7.0 and Bedrock compatibility =
* This plugin is is fully compatible with php 7.0 and roots' [bedrock](https://github.com/roots/bedrock).
* Fallback support to php v5.3+

= Featured on =
* [wpnewsify.com](https://wpnewsify.com/plugins/best-wordpress-google-maps-plugins/)
* [wpaisle.com](https://wpaisle.com/wordpress-widgets/google-map-widgets-for-wordpress/)
* [webdesigncone.com](http://webdesigncone.com/2014/best-wordpress-plugins/)
* [wpin.me](http://wpin.me/how-to-add-google-maps-wordpress/)
* [onplugins.com](http://onplugins.com/most-active-wordpress-easy-map-plugins/)


> <strong>Found bugs ?</strong><br>
> I am happy to resolve bugs, report bugs [here](https://github.com/ankurk91/wp-google-map/issues)<br>
> Please use WordPress forums for any kind of support.

== Installation ==
1. Search for 'ank google map' in WordPress Plugin Directory and Download the .zip file & extract it.
2. Upload the folder `ank-google-map` to the `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins List' page in WordPress Admin Area.
4. Configure this plugin via Settings-->Google Map
5. Paste the `[ank_google_map]` short-code in your pages/posts/widgets.


== Frequently Asked Questions ==

= What is the short-code for this plugin =

`[ank_google_map]`

= Why did you call it Light Weight ? =

It utilize WordPress dash-icons, color picker, and text editor on plugin Options Page.<br>
It does not create additional tables in your database, uses inbuilt 'wp_options' table.<br>
The whole package is about 25 kb (zipped).

= What do you mean by Non Bloated ? =

There are many of Map plugins in plugin directory, but most of them not written well.<br>
Means they put lots of java script (uncompressed) code on every page of your website.
They also loads jQuery file before them which effects your page speed.
This plugin will enqueue its JS files on the required page only.
It does not depends on external js library like: jQuery.

= Map controls not shown correctly on front-end =

Add this css code to your theme's style.css file to fix this

`
.gmnoprint img, #agm-canvas img { max-width: none; }

`

= Full screen control not visible =

This is because of you theme css, test with WP default theme first.

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
You can also upload your own marker images.

= What if i uninstall/remove this plugin ? =

No worry! It will remove its traces from database upon uninstall.
You have to remove short-code from your pages by yourself.

= How do i enter correct language code ? =

You can force google to load a specific language for all visitors.<br>
Get latest supported language code list from [here](https://developers.google.com/maps/faq#languagesupport).
If you don't specify language code then google will try to load the language requested by visitor's web browser.

= How to make it responsive =

Set Map Canvas Width to 100 %. Map will auto center upon resize.

= I don't want border on map canvas =

Leave the color field empty and it will not be applied.

= Did you test it with old version of WordPress ? =

No, tested with v4.7.2 (latest as of now) only. So i recommend you to upgrade to latest WordPress today.

= Failed to load Google Map. Refresh this page and try again. What is this ? =

It means 'Google Map API' is not loaded.

Possible reasons are -

* No internet connection. (Internet is must).
* Other plugin's java script conflict. (Try disabling them one by one).
* This plugin has a problem/bug. (Report it now).

= How do i insert the API key ? =
* Obtain a browser key, see steps [here](https://developers.google.com/maps/documentation/javascript/get-api-key#get-an-api-key)
* Insert your key on option page and you are good to go
* It may take upto 15 minutes for API key to work upon installation

= Future Plans ? =

* Multiple Maps with Multiple Markers.




== Upgrade Notice ==
Please upgrade to v2.0.0 for better experience

== Screenshots ==
1. General Options
2. Location Options
3. Marker Options
4. Info Window Options


== Changelog ==

= 2.6.0 =
* Compatible with WP v4.8.0
* Update: Google Map API v3.28
* Remove: [draggable](https://developers.google.com/maps/documentation/javascript/reference#MapOptions) option

= 2.5.0 =
* Add: Expose Google Map object to `window`, [read](https://github.com/ankurk91/wp-google-map/wiki/Hook-into-JS)
* Add: Expose two actions before and after shortcode, [read](https://github.com/ankurk91/wp-google-map/wiki/Before-and-After-shortcode-actions)

= 2.4.0 =
* Add: [Gesture Handling](https://developers.google.com/maps/documentation/javascript/interaction#gesture-handling)
* Update: Bump Google Map API version to 3.27 stable

= 2.3.1 =
* Add: Add a filter to style.json content array, see class-util.php or [see](https://github.com/ankurk91/wp-google-map/wiki/How-to-add-your-own-styles)

= 2.3.0 =
* Fix: Parse error: syntax error,unexpected '[' with php 5.3
* Fix: Marker image visibility on Safari
* Add: Select marker image from Media Library

= 2.2.0 =
* Add: FullScreen Control
* Fix: Undefined index warnings on updates

= 2.1.0 =
* Allow styling map by using some predefined styles from [snazzymaps](https://snazzymaps.com/)
* Allow custom marker icon image file

= 2.0.0 =
* Revamp UI
* Translation ready

= 1.7.9 =
* Updated Links
* Namespace changed

= 1.7.8 =
* Option to add API key

= 1.7.7 =
* Tested upto WP Version 4.5.1
* Better touch device detection
* Google Map no longer supports IE 9

= 1.7.6 =
* Allow developers to add API key to google map (see FAQ)
* Bump Google Map API version to 3.24
* Fixed a bug in InfoWindow text

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
