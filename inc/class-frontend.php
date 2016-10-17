<?php
namespace Ankur\Plugins\Ank_Google_Map;
/**
 * Class Frontend
 * @package Ankur\Plugins\Ank_Google_Map
 */
class Frontend
{

    /**
     * Stores database options
     * @var array|mixed|void
     */
    private $db = array();

    /**
     * Util class instance
     * @var Util
     */
    private $util;

    function __construct()
    {
        // Register our short-code [ank_google_map]
        add_shortcode('ank_google_map', array($this, 'process_shortcode'));

        // Store database options for later use
        $this->db = get_option('ank_google_map');
        $this->util = new Util();
    }


    /**
     * Returns dynamic javascript options to be used by frontend js
     * @return array
     */
    private function get_js_options()
    {
        $db = $this->db;

        $map_types = array(
            1 => 'ROADMAP',
            2 => 'SATELLITE',
            3 => 'HYBRID',
            4 => 'TERRAIN',
        );

        $marker_animations = array(
            1 => 'NONE',
            2 => 'BOUNCE',
            3 => 'DROP',
        );

        return array(
            'map' => array(
                'lat' => $db['map_Lat'],
                'lng' => $db['map_Lng'],
                'zoom' => $db['map_zoom'],
                'type' => $map_types[$db['map_type']],
                'styles' => $this->util->get_style_by_id($db['map_style'])
            ),
            'marker' => array(
                'enabled' => absint($db['marker_on']),
                'animation' => esc_js($marker_animations[$db['marker_anim']]),
                'title' => esc_js($db['marker_title']),
                'color' => $this->util->get_marker_url($db['marker_color']),
                'file' => empty($db['marker_file']) ? false : esc_url($db['marker_file']),
            ),
            'info_window' => array(
                'enabled' => absint($db['info_on']),
                'text' => wp_unslash($db['info_text']),
                'state' => absint($db['info_state']),
            ),
            // Disabled controls, 1=disabled
            'controls' => array(
                'zoomControl' => absint($db['map_control_2']),
                'mapTypeControl' => absint($db['map_control_3']),
                'streetViewControl' => absint($db['map_control_4']),
                'fullscreenControl' => absint($db['map_control_5']),
            ),
            'mobile' => array(
                'scrollwheel' => absint($db['disable_mouse_wheel']),
                'draggable' => absint($db['disable_drag_mobile']),
            )
        );

    }


    /**
     * Function runs behind our short-code
     * Does not accept any parameters
     * @return string
     */
    function process_shortcode()
    {

        ob_start();// ob_start is here for a reason
        $db = $this->db;

        // Write canvas html always
        $width_unit = ($db["div_width_unit"] === 1) ? 'px' : '%';
        $border_color = ($db["div_border_color"] === '') ? '' : 'border:1px solid ' . esc_attr($db["div_border_color"]);
        echo '<div class="agm-canvas" id="agm-canvas" style="margin: 0 auto; width:' . esc_attr($db["div_width"]) . $width_unit . '; height:' . esc_attr($db["div_height"]) . 'px;' . $border_color . '"></div>';


        // Decide language code
        $lang_code = (esc_attr($db['map_lang_code']) === '') ? '' : '&language=' . esc_attr($db['map_lang_code']);
        //Decide API key
        $api_key = empty($db['api_key']) ? '' : '&key=' . esc_js($db['api_key']);
        // Enqueue google map api
        wp_enqueue_script('agm-google-map-api', "https://maps.googleapis.com/maps/api/js?v=" . AGM_API_VER . $lang_code . $api_key, array(), null, true);

        // Enqueue frontend js file
        $is_min = (defined('WP_DEBUG') && WP_DEBUG == true) ? '' : '.min';
        wp_enqueue_script('agm-frontend-js', plugins_url('assets/frontend' . $is_min . '.js', AGM_BASE_FILE), array('agm-google-map-api'), AGM_PLUGIN_VERSION, true);

        // WP inbuilt hack to print js options object just before this script
        wp_localize_script('agm-frontend-js', '_agmOpt', $this->get_js_options());
        return ob_get_clean();
    }


}