<?php
namespace Ankur\Plugins\Ank_Google_Map;
/**
 * Class Admin
 * @package Ankur\Plugins\Ank_Google_Map
 */
class Admin
{

    /**
     * Unique plugin option page slug
     */
    const PLUGIN_SLUG = 'agm_settings';

    /**
     * Utility class instance
     * @var Util
     */
    private $util;

    /**
     * Settings class instance
     * @var Settings
     */
    private $settings;

    function __construct()
    {
        // Add settings link to plugin list page
        add_filter('plugin_action_links_' . plugin_basename(AGM_BASE_FILE), array($this, 'add_plugin_actions_links'), 10, 2);

        // Add settings link under admin->settings menu->Google map
        add_action('admin_menu', array($this, 'add_link_to_settings_menu'));

        // Be multilingual
        add_action('plugins_loaded', array($this, 'load_text_domain'));

        // Init class
        $this->util = new Util();
        $this->settings = new Settings();
    }

    /**
     * Adds a 'Settings' link for this plugin on plugin listing page
     *
     * @param $links
     * @return array  Links array
     */
    public function add_plugin_actions_links($links)
    {

        if (current_user_can('manage_options')) {
            $url = add_query_arg('page', self::PLUGIN_SLUG, 'options-general.php');
            array_unshift(
                $links,
                sprintf('<a href="%s">%s</a>', $url, __('Settings'))
            );
        }

        return $links;
    }

    /**
     * Register a page to display plugin options
     */
    public function add_link_to_settings_menu()
    {
        $page_hook_suffix = add_submenu_page(
            'options-general.php',
            'Google Map', //page title
            'Google Map', //menu text
            'manage_options',
            self::PLUGIN_SLUG,
            array($this->settings, 'load_option_page')
        );

        // Add help drop down menu on option page,  WP v3.3+
        add_action("load-$page_hook_suffix", array($this, 'add_help_menu_tab'));

        add_action('admin_print_scripts-' . $page_hook_suffix, array($this, 'add_admin_assets'));
    }

    public function load_text_domain()
    {
        load_plugin_textdomain('ank-google-map', false, dirname(plugin_basename(AGM_BASE_FILE)) . '/languages/');
    }

    /**
     * Returns dynamic javascript options to be used by admin js
     * @return array
     */
    private function get_js_options()
    {
        $db = get_option('ank_google_map');

        return array(
            'map' => array(
                'lat' => esc_attr($db['map_Lat']),
                'lng' => esc_attr($db['map_Lng']),
                'zoom' => absint($db['map_zoom']),
                'style' => absint($db['map_style']),
            ),
            'marker' => array(
                'color' => $this->util->get_marker_url($db['marker_color']),
                'file' => empty($db['marker_file']) ? false : esc_url($db['marker_file'])
            ),
            'styles' => $this->util->get_styles()
        );
    }

    /**
     * Add javascript and css to plugin option page
     */
    public function add_admin_assets()
    {

        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_media();

        $is_min = (defined('WP_DEBUG') && WP_DEBUG == true) ? '' : '.min';
        $db = get_option('ank_google_map');
        wp_enqueue_style('agm-admin-css', plugins_url('/assets/option-page' . $is_min . '.css', AGM_BASE_FILE), array(), AGM_PLUGIN_VERSION, 'all');

        $api_key = empty($db['api_key']) ? '' : '&key=' . esc_js($db['api_key']);
        wp_enqueue_script('agm-google-map', 'https://maps.googleapis.com/maps/api/js?v=' . AGM_API_VER . '&libraries=places' . $api_key, array(), null, true);
        wp_enqueue_script('agm-admin-js', plugins_url("/assets/option-page" . $is_min . ".js", AGM_BASE_FILE), array('jquery', 'agm-google-map'), AGM_PLUGIN_VERSION, true);
        // WP inbuilt hack to print js options object just before this script
        wp_localize_script('agm-admin-js', '_agmOpt', $this->get_js_options());
    }

    /*
     * Add a help tab at top of plugin option page
     */
    public function add_help_menu_tab()
    {

        $screen = get_current_screen();

        $screen->add_help_tab(
            array(
                'id' => 'agm-overview',
                'title' => 'Overview',
                'content' => '<p><strong>Thanks for using this plugin.</strong><br>' .
                    'This plugin allows you to put a custom Google Map on your website. Just configure options below and ' .
                    'save your settings. <br>Copy & paste <code>[ank_google_map]</code> shortcode on your page/post/widget to view your map.
                </p>'

            )
        );

        $screen->add_help_tab(
            array(
                'id' => 'agm-troubleshoot',
                'title' => 'Troubleshoot',
                'content' => '<p><strong>Things to remember</strong><br>' .
                    '<ul>
                <li>Google Map require to setup an API key before start using it. See FAQ.</li>    
                <li>If you are using a cache/performance plugin, you need to flush/delete your site cache after saving settings here.</li>
                <li>Only one map is supported at this time. Don&apos;t put short-code twice on the same page.</li>
                <li>Only one marker supported at this time, Marker will be positioned at the center of your map.</li>                
                </ul>
                </p>'

            )
        );
        $screen->add_help_tab(
            array(
                'id' => 'agm-more-info',
                'title' => 'More',
                'content' => '<p><strong>Need more information ?</strong><br>' .
                    'A brief FAQ is available, ' .
                    'click <a href="https://wordpress.org/plugins/ank-google-map/faq/" target="_blank">here</a> for more.<br>' .
                    'Support is only available on WordPress Forums, click <a href="https://wordpress.org/support/plugin/ank-google-map" target="_blank">here</a> to ask anything about this plugin.<br>' .
                    'You can also report bugs at plugin&apos;s GitHub <a href="https://github.com/ankurk91/wp-google-map/issues" target="_blank">page</a>. ' .
                    'I will try to reply as soon as possible. </p>'

            )
        );

        $screen->set_help_sidebar(
            '<p><strong>Quick Links</strong></p>' .
            '<p><a href="https://wordpress.org/plugins/ank-google-map/faq/" target="_blank">Plugin FAQ</a></p>' .
            '<p><a href="https://github.com/ankurk91/wp-google-map" target="_blank">Plugin Home</a></p>'
        );
    }

}
