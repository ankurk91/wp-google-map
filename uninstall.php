<?php

/**
 * Uninstall file for "Ank Google Map" Plugin
 * This file will be used to remove all traces of this plugin when uninstalled
 */


// Make sure that uninstall was called by WordPress
if (!defined('WP_UNINSTALL_PLUGIN'))
    exit;


// Remove the database entry created by this plugin
delete_option('ank_google_map');

