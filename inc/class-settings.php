<?php
namespace Ankur\Plugins\Ank_Google_Map;
/**
 * Class Settings
 * @package Ankur\Plugins\Ank_Google_Map
 */
class Settings
{

    /**
     * Constants
     */
    const PLUGIN_OPTION_GROUP = 'agm_plugin_options';

    /**
     * Utility class instance
     * @var Util
     */
    private $util;

    public function __construct()
    {
        // Register setting
        add_action('admin_init', array($this, 'register_plugin_settings'));

        // To save default options upon activation
        register_activation_hook(plugin_basename(AGM_BASE_FILE), array($this, 'do_upon_plugin_activation'));

        // Check for database upgrades
        add_action('plugins_loaded', array($this, 'perform_upgrade'));

        $this->util = new Util();

    }

    /**
     * Returns default plugin db options
     * @return array
     */
    public function get_default_options()
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
     * Save default settings upon plugin activation
     */
    public function do_upon_plugin_activation()
    {

        if (false == get_option('ank_google_map')) {
            add_option('ank_google_map', $this->get_default_options());
        }

    }

    /**
     * Register plugin settings, using WP settings API
     */
    public function register_plugin_settings()
    {
        register_setting(self::PLUGIN_OPTION_GROUP, 'ank_google_map', array($this, 'validate_form_post'));
    }


    /**
     * Load plugin option page view
     * @throws \Exception
     */
    public function load_option_page()
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
     * Validate form $_POST data
     * @param $in array
     * @return array Validated array
     */
    public function validate_form_post($in)
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

}