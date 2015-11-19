<?php
namespace Ank91\Ank_Google_Map_Plugin;
/**
 * Class Ank_Google_Map_Admin
 * @package Ank91\Ank_Google_Map_Plugin
 */
class Ank_Google_Map_Admin
{
    const PLUGIN_SLUG = 'agm_options_page';
    const PLUGIN_OPTION_GROUP = 'agm_plugin_options';

    function __construct()
    {
        /* To save default options upon activation */
        register_activation_hook(AGM_BASE_FILE, array($this, 'do_upon_plugin_activation'));

        /* For register setting */
        add_action('admin_init', array($this, 'register_plugin_settings'));

        /* Add settings link to plugin list page */
        add_filter('plugin_action_links_' . AGM_BASE_FILE, array($this, 'add_plugin_actions_links'), 10, 2);

        /* Add settings link under admin->settings menu->ank google map */
        add_action('admin_menu', array($this, 'add_submenu_page'));

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


    /**
     * Returns default plugin db options
     * @return array
     */
    function get_default_options()
    {

        $default_options = array(
            'plugin_ver' => AGM_PLUGIN_VERSION,
            'div_width' => '100',
            'div_width_unit' => 2,
            'div_height' => '300',
            'div_border_color' => '#ccc',
            'map_Lat' => '29.453182059948965',
            'map_Lng' => '77.7068350911577',
            'map_zoom' => 2,
            'map_control_1' => '0',
            'map_control_2' => '0',
            'map_control_3' => '0',
            'map_control_4' => '0',
            'map_control_5' => '0',
            'map_lang_code' => '',
            'map_type' => 1,
            'marker_on' => '1',
            'marker_title' => 'We are here',
            'marker_anim' => 1,
            'marker_color' => 1,
            'info_on' => '1',
            'info_text' => '<b>Your Destination</b>',
            'info_state' => '0',
            'disable_mouse_wheel' => '0',
            'disable_drag_mobile' => '1',
        );

        return $default_options;
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
            $build_url = add_query_arg('page', self::PLUGIN_SLUG, 'options-general.php');
            array_unshift(
                $links,
                sprintf('<a href="%s">%s</a>', $build_url, __('Settings'))
            );
        }

        return $links;
    }

    /**
     * Register a page to display plugin options
     */
    function add_submenu_page()
    {
        $page_hook_suffix = add_submenu_page('options-general.php', 'Ank Google Map', 'Ank Google Map', 'manage_options', self::PLUGIN_SLUG, array($this, 'load_option_page'));
        /*
        * Add the color picker js  + css file (for settings page only)
        * Available for wp v3.5+ only
        */
        if (version_compare($GLOBALS['wp_version'], 3.5) >= 0) {
            add_action('admin_print_scripts-' . $page_hook_suffix, array($this, 'add_color_picker'));
        }

        /* add help drop down menu on option page  wp v3.3+ */
        if (version_compare($GLOBALS['wp_version'], '3.3', '>=')) {
            add_action("load-$page_hook_suffix", array($this, 'add_help_menu_tab'));
        }

        add_action('admin_print_scripts-' . $page_hook_suffix, array($this, 'print_admin_js'));
    }

    function validate_form_post($in)
    {

        $out = array();
        //always store plugin version to db
        $out['plugin_ver'] = esc_attr(AGM_PLUGIN_VERSION);;

        $out['div_width'] = sanitize_text_field($in['div_width']);
        $out['div_height'] = sanitize_text_field($in['div_height']);
        $out['div_width_unit'] = intval($in['div_width_unit']);
        $out['div_border_color'] = sanitize_text_field($in['div_border_color']);

        $out['disable_mouse_wheel'] = (isset($in['disable_mouse_wheel'])) ? '1' : '0';
        $out['disable_drag_mobile'] = (isset($in['disable_drag_mobile'])) ? '1' : '0';

        $out['map_Lat'] = sanitize_text_field($in['map_Lat']);
        $out['map_Lng'] = sanitize_text_field($in['map_Lng']);
        $out['map_zoom'] = intval($in['map_zoom']);

        $out['map_control_1'] = (isset($in['map_control_1'])) ? '1' : '0';
        $out['map_control_2'] = (isset($in['map_control_2'])) ? '1' : '0';
        $out['map_control_3'] = (isset($in['map_control_3'])) ? '1' : '0';
        $out['map_control_4'] = (isset($in['map_control_4'])) ? '1' : '0';
        $out['map_control_5'] = (isset($in['map_control_5'])) ? '1' : '0';

        $out['map_lang_code'] = sanitize_text_field($in['map_lang_code']);
        $out['map_type'] = intval($in['map_type']);
        $out['marker_on'] = (isset($in['marker_on'])) ? '1' : '0';

        $out['marker_title'] = sanitize_text_field($in['marker_title']);
        $out['marker_anim'] = intval($in['marker_anim']);
        $out['marker_color'] = intval($in['marker_color']);

        $out['info_on'] = (isset($in['info_on'])) ? '1' : '0';
        $out['info_state'] = (isset($in['info_state'])) ? '1' : '0';

        /*
        * Lets allow some html in info window
        * This is same as like we make a new post
        */
        $out['info_text'] = balanceTags(wp_kses_post($in['info_text']), true);

        /*
        * @Regx => http://stackoverflow.com/questions/7549669/php-validate-latitude-longitude-strings-in-decimal-format
        */
        if (!preg_match("/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/", $in['map_Lat'])) {
            add_settings_error('ank_google_map', 'agm_lat', 'Invalid Latitude Value. Please validate.');
        } elseif (!preg_match("/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/", $in['map_Lng'])) {
            add_settings_error('ank_google_map', 'agm_lat', 'Invalid Longitude Value. Please validate.');
        } elseif (strlen($in['info_text']) > 1000) {
            add_settings_error('ank_google_map', 'agm_lat', 'Info Window Text should not exceed 1000 characters. Current length is: ' . strlen($in['info_text']));
        }


        return $out;
    }

    function load_option_page()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        $file_path = __DIR__ . '/pages/options_page.php';

        if (file_exists($file_path)) {
            require($file_path);
        } else {
            throw new \Exception("Unable to load settings page, Template File not found, (v" . AGM_PLUGIN_VERSION . ")");
        }

    }


    /**
     * Decides whether to load text editor or not
     * @param string $content
     */
    private function get_text_editor($content = '')
    {

        if (user_can_richedit()) {
            wp_editor($content, 'agm-info-editor',
                array(
                    'media_buttons' => false, //disable media uploader
                    'textarea_name' => 'ank_google_map[info_text]',
                    'textarea_rows' => 5,
                    'teeny' => false,
                    'quicktags' => true
                ));

        } else {
            /*
             * else Show normal text-area to user
             */
            echo '<textarea maxlength="1000" rows="3" cols="33" name="info_text" style="width: 98%">' . $content . '</textarea>';
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
        $options = get_option('ank_google_map');

        return array(
            'map' => array(
                'lat' => esc_attr($options['map_Lat']),
                'lng' => esc_attr($options['map_Lng']),
                'zoom' => absint($options['map_zoom']),
            ),
            'color_picker' => (version_compare($GLOBALS['wp_version'], 3.5) >= 0)
        );
    }

    /**
     * Print option page javascript
     */
    function print_admin_js()
    {
        $is_min = (WP_DEBUG == 1) ? '' : '.min';
        wp_enqueue_style('agm-admin-css', plugins_url('css/option-page' . $is_min . '.css', __FILE__), array(), AGM_PLUGIN_VERSION, 'all');
        wp_enqueue_script('agm-google-map', '//maps.googleapis.com/maps/api/js?libraries=places', array(), null, true);
        wp_enqueue_script('agm-admin-js', plugins_url("/js/option-page" . $is_min . ".js", __FILE__), array('jquery'), AGM_PLUGIN_VERSION, true);
        //wp inbuilt hack to print js options object just before this script
        wp_localize_script('agm-admin-js', '_agm_opt', $this->get_js_options());
    }

    /*
     * Add a help tab at top of plugin option page
     */
    public static function add_help_menu_tab()
    {

        $curr_screen = get_current_screen();

        $curr_screen->add_help_tab(
            array(
                'id' => 'agm-overview',
                'title' => 'Overview',
                'content' => '<p><strong>Thanks for using "Ank Google Map"</strong><br>' .
                    'This plugin allows you to put a custom Google Map on your website. Just configure options below and ' .
                    'save your settings. Copy/paste <code>[ank_google_map]</code> short-code on your page/post/widget to view your map.
                </p>'

            )
        );

        $curr_screen->add_help_tab(
            array(
                'id' => 'agm-troubleshoot',
                'title' => 'Troubleshoot',
                'content' => '<p><strong>Things to remember</strong><br>' .
                    '<ul>
                <li>If you are using a cache/performance plugin, you need to flush/delete your site cache after saving settings here.</li>
                <li>Only one map is supported at this time. Don&apos;t put short-code twice on the same page.</li>
                <li>Only one marker supported at this time, Marker will be positioned at the center of your map.</li>
                <li>Info Window needs marker to be enabled first.</li>
                </ul>
                </p>'

            )
        );
        $curr_screen->add_help_tab(
            array(
                'id' => 'agm-more-info',
                'title' => 'More',
                'content' => '<p><strong>Need more information ?</strong><br>' .
                    'A brief FAQ is available, ' .
                    'click <a href="https://wordpress.org/plugins/ank-google-map/faq/" target="_blank">here</a> for more.<br>' .
                    'Support is only available on WordPress Forums, click <a href="https://wordpress.org/support/plugin/ank-google-map" target="_blank">here</a> to ask anything about this plugin.<br>' .
                    'You can also report a bug at plugin&apos;s GitHub <a href="https://github.com/ank91/ank-google-map" target="_blank">page</a>.' .
                    'I will try to reply as soon as possible. </p>'

            )
        );

        $curr_screen->set_help_sidebar(
            '<p><strong>Quick Links</strong></p>' .
            '<p><a href="https://wordpress.org/plugins/ank-google-map/faq/" target="_blank">Plugin FAQ</a></p>' .
            '<p><a href="https://github.com/ank91/ank-google-map" target="_blank">Plugin Home</a></p>'
        );
    }

}
