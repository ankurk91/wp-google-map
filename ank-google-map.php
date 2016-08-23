<?php
namespace Ankur\Plugins\Ank_Google_Map;
?><?php
/*
Plugin Name: Google Map
Plugin URI: https://github.com/ankurk91/wp-google-map
Description: Simple, light weight and non-bloated Google Map Plugin. Written in pure javascript, no jQuery at all, responsive, configurable, no ads and 100% Free of cost. Short code : <code>[ank_google_map]</code>
Version: 2.0.0
Author: Ankur Kumar
Author URI: http://ankurk91.github.io/
License: MIT
License URI: https://opensource.org/licenses/MIT
Text Domain: ank-google-map
Domain Path: /languages
*/
?><?php

/* No direct access */
if (!defined('ABSPATH')) die;

define('AGM_PLUGIN_VERSION', '2.0.0');
define('AGM_BASE_FILE', __FILE__);


if (is_admin() && (!defined('DOING_AJAX') || !DOING_AJAX)) {
    require __DIR__ . '/inc/class-admin.php';
    new Admin();

} else {
    require __DIR__ . '/inc/class-frontend.php';
    new Frontend();
}
