=== Ank Google Map ===
Tags: google map, responsive, light weight, ank, free, easy map
Requires at least: 3.8.0
Tested up to: 4.0
Stable tag: 1.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

More info can be found here: http://ank91.github.io/ank-google-map
== Description ==
One Website <--> One Map <--> One Marker.
Simple and non-bloated WordPress Google Map Plugin.
Written in pure javascript, no jQuery at all, responsive, configurable, no ads and 100% Free of cost.

== Installation ==

1. Upload the folder `ank-google-map` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Configure this plugin via Settings-->Ank Google Map
4. Paste the `[ank_google_map]` short-code in your pages/posts.


== Frequently Asked Questions ==
==Why did u call it light weight ?==
Because it does not depend no jQuery, written in pure Java Script.
Settings page is also written in pure java script.
It uses WP dash-icons in Plugin Settings Page.
We store our settings in database but uses on row.

= Options page does not work well :( =
You must have modern browser to configure the map option.
Old browsers will not work well.

= Color picker does not work well :( =
This plugin utilize inbuilt WP Color API.
You must have WordPress v3.5+.

= Changes does not reflect ?=
Are you using some Cache plugin ? Flush your WP cache.

= Where does it store settings? =
Database->wp-options->ank_google_map. A Single Row, stored in array for faster access.

= What if i uninstall this plugin? =
No worry! It will remove its traces from database.
You have to remove short-code from pages by yourself.

= Can i modify this plugin ? =
Yes you can. But you can't make money by selling this. You can ask for donation.

= Any plan to support more than one map. =
Only if i got some time to code.

= Is Google Map API is free. =
Until we break its terms and conditions.

= Future Plans ? =
Localization for Option Page.
More security approaches.
More options.


== Screenshots ==
1. Plugin Option page.

== Changelog ==

=1.3=
*Added notes about flushing cache
*Load Color API only on Option page
*Special checks for Color API

=1.2=
*Fix Bugs

=1.1=
*Fix Bugs
*Sanitize Inputs
*Allow HTML in info window

= 1.0 =
* First public beta
