<?php
namespace Ank91\Ank_Google_Map_Plugin;
/**
 * Class Ank_Google_Map_Admin
 * @package Ank91\Ank_Google_Map_Plugin
 */
class Ank_Google_Map_Admin
{

    function __construct()
    {
        /* Save settings if first time */
        if (false == get_option('ank_google_map')) {
            add_option('ank_google_map', $this->get_default_options());
        }

        /* Add settings link to plugin list page */
        add_filter('plugin_action_links_' . AGM_BASE_FILE, array($this, 'add_plugin_actions_links'), 10, 2);

        /* Add settings link under admin->settings menu->ank google map */
        add_action('admin_menu', array($this, 'add_submenu_page'));

    }

    /**
     * Returns default plugin db options
     * @return array
     */
    function get_default_options()
    {

        $new_options = array(
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
            'te_meta_1' => '1',
            'te_meta_2' => '0',
            'te_meta_3' => '0',
            'plugin_ver' => AGM_PLUGIN_VERSION,
            'disable_mouse_wheel' => '0',
            'disable_drag_mobile' => '1',


        );

        return $new_options;
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
            $build_url = add_query_arg('page', AGM_PLUGIN_SLUG, 'options-general.php');
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
        $page_hook_suffix = add_submenu_page('options-general.php', 'Ank Google Map', 'Ank Google Map', 'manage_options', AGM_PLUGIN_SLUG, array($this, 'AGM_Option_Page'));
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


    function AGM_Option_Page()
    {

        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        /*
        * Empty option array
        */
        $agm_options = array();
        /*
         * Fetch settings from database once
         */
        $agm_options = get_option('ank_google_map');

        if (isset($_POST['save_agm_form'])) {
            /*
            * WP inbuilt form security check
            */
            check_admin_referer('agm_form', '_wpnonce-agm_form');
            /*
             * Begin sanitize inputs
             */
            $agm_options['plugin_ver'] = esc_attr(AGM_PLUGIN_VERSION);
            $agm_options['div_width'] = sanitize_text_field($_POST['div_width']);
            $agm_options['div_height'] = sanitize_text_field($_POST['div_height']);
            $agm_options['div_width_unit'] = intval(sanitize_text_field($_POST['div_width_unit']));

            $agm_options['div_border_color'] = sanitize_text_field($_POST['div_border_color']);

            $agm_options['disable_mouse_wheel'] = (isset($_POST['disable_mouse_wheel'])) ? '1' : '0';
            $agm_options['disable_drag_mobile'] = (isset($_POST['disable_drag_mobile'])) ? '1' : '0';

            $agm_options['map_Lat'] = sanitize_text_field($_POST['map_Lat']);
            $agm_options['map_Lng'] = sanitize_text_field($_POST['map_Lng']);
            $agm_options['map_zoom'] = intval($_POST['map_zoom']);

            $agm_options['map_control_1'] = (isset($_POST['map_control_1'])) ? '1' : '0';
            $agm_options['map_control_2'] = (isset($_POST['map_control_2'])) ? '1' : '0';
            $agm_options['map_control_3'] = (isset($_POST['map_control_3'])) ? '1' : '0';
            $agm_options['map_control_4'] = (isset($_POST['map_control_4'])) ? '1' : '0';
            $agm_options['map_control_5'] = (isset($_POST['map_control_5'])) ? '1' : '0';

            $agm_options['map_lang_code'] = sanitize_text_field($_POST['map_lang_code']);
            $agm_options['map_type'] = intval($_POST['map_type']);
            $agm_options['marker_on'] = (isset($_POST['marker_on'])) ? '1' : '0';

            $agm_options['marker_title'] = sanitize_text_field($_POST['marker_title']);
            $agm_options['marker_anim'] = intval($_POST['marker_anim']);
            $agm_options['marker_color'] = intval($_POST['marker_color']);

            $agm_options['info_on'] = (isset($_POST['info_on'])) ? '1' : '0';
            $agm_options['info_state'] = (isset($_POST['info_state'])) ? '1' : '0';


            /*
             * Lets allow some html in info window
             * This is same as like we make a new post
             */
            $agm_options['info_text'] = balanceTags(wp_kses_post($_POST['info_text']), true);

            /*
             * @Regx => http://stackoverflow.com/questions/7549669/php-validate-latitude-longitude-strings-in-decimal-format
             */
            if (!preg_match("/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/", $agm_options['map_Lat'])) {
                echo "<div class='error notice is-dismissible'>Nothing saved, Invalid Latitude Value.</div>";

            } elseif (!preg_match("/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/", $agm_options['map_Lng'])) {
                echo "<div class='error notice is-dismissible'>Nothing saved, Invalid Longitude Value. </div>";

            } elseif (strlen($agm_options['info_text']) > 1000) {
                echo "<div class='error notice is-dismissible'>Nothing saved, Info Window Text should not exceed 1000 characters . Current Length is: " . strlen($agm_options['info_text']) . "</div>";
            } else {
                /* Save posted data back to database */
                update_option('ank_google_map', $agm_options);
                echo "<div class='updated notice is-dismissible'><p>Your settings has been <b>saved</b>.&emsp;You can always use <code>[ank_google_map]</code> shortcode.</p></div>";
            }

        }/*if isset post ends*/
        //load option page
        require_once(__DIR__ . '/pages/options_page.php');

    }


    /**
     * Decides whether to load text editor or not
     * @param string $content
     */
    private function get_text_editor($content = '')
    {
        /**
         * decide if browser support editor or not
         */
        if (user_can_richedit()) {
            wp_editor($content, 'agm-info-editor',
                array(
                    'media_buttons' => false, //disable media uploader
                    'textarea_name' => 'info_text',
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
        $agm_options = get_option('ank_google_map');

        return array(
            'map' => array(
                'lat' => esc_attr($agm_options['map_Lat']),
                'lng' => esc_attr($agm_options['map_Lng']),
                'zoom' => absint($agm_options['map_zoom']),
            ),
            'color_picker' => (version_compare($GLOBALS['wp_version'], 3.5) >= 0),
            'ajax_url' => admin_url('admin-ajax.php')
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
        wp_localize_script('agm-admin-js', 'agm_opt', $this->get_js_options());
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

