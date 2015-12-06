<?php
/**
 * Main php file for 'Ank_Google_Map' plugin
 * Adding namespace on top, no content allowed before namespace declaration
 */
namespace Ank91\Ank_Google_Map_Plugin;

?><?php
/*
Plugin Name: Ank Google Map
Plugin URI: https://github.com/ank91/ank-google-map
Description: Simple, light weight, and non-bloated WordPress Google Map Plugin. Written in pure javascript, no jQuery at all, responsive, configurable, no ads and 100% Free of cost.
Version: 1.7.4
Author: Ankur Kumar
Author URI: http://ank91.github.io/
License: GPL2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/
?><?php

/* No direct access */
if (!defined('ABSPATH')) die;

define('AGM_PLUGIN_VERSION', '1.7.4');
define('AGM_BASE_FILE', __FILE__);

/**
 * Registering class auto-loader
 * @requires php v5.3.0
 */
spl_autoload_register(__NAMESPACE__ . '\ank_google_map_autoloader');

/**
 * Auto-loader for our plugin classes
 * @param $class_name
 * @throws \Exception
 */
function ank_google_map_autoloader($class_name)
{
    //make sure this loader work only for this plugin's related classes
    if (false !== strpos($class_name, __NAMESPACE__)) {
        $cls = strtolower(str_replace(__NAMESPACE__ . '\Ank_Google_Map_', '', $class_name));
        $cls_file = __DIR__ . "/inc/class-" . $cls . ".php";
        if (is_readable($cls_file)) {
            require_once($cls_file);
        } else {
            throw new \Exception('Class file - ' . esc_html($cls_file) . ' not found');
        }

    }
}

if (is_admin() && (!defined('DOING_AJAX') || !DOING_AJAX)) {
    new Ank_Google_Map_Admin();

} else {
    new Ank_Google_Map_FrontEnd();
}
