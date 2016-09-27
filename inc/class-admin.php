<?php
namespace Ankur\Plugins\Ank_Google_Map;
/**
 * Class Admin
 * @package Ankur\Plugins\Ank_Google_Map
 */
class Admin
{

    /**
     * Constants
     */
    const PLUGIN_SLUG = 'agm_settings';
    const PLUGIN_OPTION_GROUP = 'agm_plugin_options';

    /**
     * Utility class instance
     * @var Util
     */
    private $util;

    function __construct()
    {
        // To save default options upon activation
        register_activation_hook(plugin_basename(AGM_BASE_FILE), array($this, 'do_upon_plugin_activation'));

        // For register setting
        add_action('admin_init', array($this, 'register_plugin_settings'));

        // Add settings link to plugin list page
        add_filter('plugin_action_links_' . plugin_basename(AGM_BASE_FILE), array($this, 'add_plugin_actions_links'), 10, 2);

        // Add settings link under admin->settings menu->Google map
        add_action('admin_menu', array($this, 'add_submenu_page'));

        // Check for database upgrades
        add_action('plugins_loaded', array($this, 'perform_upgrade'));

        // Be multilingual
        add_action('plugins_loaded', array($this, 'load_text_domain'));

        // Init class
        $this->util = new Util();
    }

    /*
    * Save default settings upon plugin activation
    */
    function do_upon_plugin_activation()
    {

        if (false == get_option('ank_google_map')) {
            add_option('ank_google_map', $this->get_default_options());
        }

    }

    /**
     * Register plugin settings, using WP settings API
     */
    function register_plugin_settings()
    {
        register_setting(self::PLUGIN_OPTION_GROUP, 'ank_google_map', array($this, 'validate_form_post'));
    }

    public static function load_text_domain()
    {
        load_plugin_textdomain('ank-google-map', false, dirname(plugin_basename(AGM_BASE_FILE)) . '/languages/');
    }

    /**
     * Returns default plugin db options
     * @return array
     */
    function get_default_options()
    {

        return array(
            'plugin_ver' => AGM_PLUGIN_VERSION,
            'div_width' => '100',
            'div_width_unit' => 2,
            'div_height' => '300',
            'div_border_color' => '',
            'map_Lat' => '28.613939100000003',
            'map_Lng' => '77.20902120000005',
            'map_zoom' => 2,
            'map_control_2' => '0',
            'map_control_3' => '0',
            'map_control_4' => '1',
            'map_control_5' => '1',
            'map_lang_code' => '',
            'map_type' => 1,
            'marker_on' => '1',
            'marker_title' => 'We are here',
            'marker_anim' => 1,
            'marker_color' => 1,
            'marker_file' => '',
            'info_on' => '1',
            'info_text' => '<b>Your Destination</b>',
            'info_state' => '0',
            'disable_mouse_wheel' => '0',
            'disable_drag_mobile' => '1',
            'api_key' => '',
            'map_style' => 0 //disabled
        );

    }

    /**
     * Adds a 'Settings' link for this plugin on plugin listing page
     *
     * @param $links
     * @return array  Links array
     */
    function add_plugin_actions_links($links)
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
    function add_submenu_page()
    {
        $page_hook_suffix = add_submenu_page('options-general.php', 'Google Map', 'Google Map', 'manage_options', self::PLUGIN_SLUG, array($this, 'load_option_page'));
        /*
        * Add the color picker js  + css file to option page
        * Available for wp v3.5+ only
        */
        add_action('admin_print_scripts-' . $page_hook_suffix, array($this, 'add_color_picker'));

        // Add help drop down menu on option page,  WP v3.3+
        add_action("load-$page_hook_suffix", array($this, 'add_help_menu_tab'));

        add_action('admin_print_scripts-' . $page_hook_suffix, array($this, 'print_admin_assets'));
    }

    /**
     * Validate form $_POST data
     * @param $in array
     * @return array Validated array
     */
    function validate_form_post($in)
    {

        $out = array();
        $errors = array();
        //always store plugin version to db
        $out['plugin_ver'] = esc_attr(AGM_PLUGIN_VERSION);;

        $out['div_width'] = sanitize_text_field($in['div_width']);
        $out['div_height'] = sanitize_text_field($in['div_height']);
        $out['div_width_unit'] = intval($in['div_width_unit']);
        $out['div_border_color'] = sanitize_text_field($in['div_border_color']);

        $out['map_Lat'] = sanitize_text_field($in['map_Lat']);
        $out['map_Lng'] = sanitize_text_field($in['map_Lng']);

        /**
         * @link http://stackoverflow.com/questions/7549669/php-validate-latitude-longitude-strings-in-decimal-format
         */
        if (!preg_match("/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/", $in['map_Lat'])) {
            $errors[] = __('Invalid Latitude format', 'ank-google-map');
            $out['map_Lat'] = '0';
        }
        if (!preg_match("/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/", $in['map_Lng'])) {
            $errors[] = __('Invalid Longitude format', 'ank-google-map');
            $out['map_Lng'] = '0';
        }

        $out['map_zoom'] = intval($in['map_zoom']);

        $out['map_lang_code'] = sanitize_text_field($in['map_lang_code']);
        $out['map_type'] = intval($in['map_type']);
        $out['map_style'] = intval($in['map_style']);

        $out['marker_title'] = sanitize_text_field($in['marker_title']);
        $out['marker_anim'] = intval($in['marker_anim']);
        $out['marker_color'] = intval($in['marker_color']);
        $out['marker_file'] = sanitize_text_field($in['marker_file']);

        $out['api_key'] = sanitize_text_field($in['api_key']);

        $choices_array = array('disable_mouse_wheel', 'disable_drag_mobile', 'map_control_2', 'map_control_3', 'map_control_4', 'map_control_5', 'marker_on', 'info_on', 'info_state');

        foreach ($choices_array as $choice) {
            $out[$choice] = (isset($in[$choice])) ? '1' : '0';
        }
        /*
        * Lets allow some html in info window
        * This is same as like we make a new post
        */
        $out['info_text'] = balanceTags(wp_kses_post($in['info_text']), true);

        // Show all form errors in a single notice
        if (!empty($errors)) {
            add_settings_error('ank_google_map', 'ank_google_map', implode('<br>', $errors));
        } else {
            add_settings_error('ank_google_map', 'ank_google_map', __('Settings saved. Use this shortcode', 'ank-google-map') . ' - <code>[ank_google_map]</code>', 'updated');
        }

        return $out;
    }

    /**
     * Load plugin option page view
     * @throws \Exception
     */
    function load_option_page()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        $file_path = plugin_dir_path(AGM_BASE_FILE) . 'views/settings.php';

        if (is_readable($file_path)) {
            extract(array(
                'db' => get_option('ank_google_map'),
                'option_group' => self::PLUGIN_OPTION_GROUP,
                'styles' => $this->util->get_styles()
            ));
            require $file_path;
        } else {
            throw new \Exception("Unable to load settings page, File - '" . esc_html($file_path) . "' not found");
        }

    }

    /**
     * Enqueue color picker related css and js
     */
    function add_color_picker()
    {

        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');

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
     * Add option page javascript and css
     */
    function print_admin_assets()
    {
        $is_min = (defined('WP_DEBUG') && WP_DEBUG == true) ? '' : '.min';
        $db = get_option('ank_google_map');
        wp_enqueue_style('agm-admin-css', plugins_url('/assets/option-page' . $is_min . '.css', AGM_BASE_FILE), array(), AGM_PLUGIN_VERSION, 'all');

        $api_key = empty($db['api_key']) ? '' : '&key=' . esc_js($db['api_key']);
        wp_enqueue_script('agm-google-map', 'https://maps.googleapis.com/maps/api/js?v=' . AGM_API_VER . '&libraries=places' . $api_key, array(), null, true);
        wp_enqueue_script('agm-admin-js', plugins_url("/assets/option-page" . $is_min . ".js", AGM_BASE_FILE), array('jquery', 'agm-google-map'), AGM_PLUGIN_VERSION, true);
        // WP inbuilt hack to print js options object just before this script
        wp_localize_script('agm-admin-js', '_agmOpt', $this->get_js_options());
    }

    /**
     * Upgrade plugin database options
     */
    public function perform_upgrade()
    {
        //Get fresh options from db
        $db = get_option('ank_google_map');
        //Check if we need to proceed , if no return early
        if ($this->should_proceed_to_upgrade($db) === false) return;
        //Get default options
        $default_options = $this->get_default_options();
        //Merge with db options , preserve old
        $new_options = (empty($db)) ? $default_options : array_merge($default_options, $db);
        //Update plugin version
        $new_options['plugin_ver'] = AGM_PLUGIN_VERSION;
        //Write options back to db
        update_option('ank_google_map', $new_options);
    }

    /**
     * Check if we need to upgrade database options or not
     * @param $db_options
     * @return bool
     */
    private function should_proceed_to_upgrade($db_options)
    {
        if (empty($db_options) || !is_array($db_options)) return true;
        if (!isset($db_options['plugin_ver'])) return true;
        return version_compare($db_options['plugin_ver'], AGM_PLUGIN_VERSION, '<');
    }

    /*
     * Add a help tab at top of plugin option page
     */
    public static function add_help_menu_tab()
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
