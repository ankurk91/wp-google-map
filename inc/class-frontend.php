<?php
namespace Ankur\Plugins\Ank_Google_Map;
/**
 * Class Frontend
 * @package Ankur\Plugins\Ank_Google_Map
 */
class Frontend
{

    private $db = array();

    function __construct()
    {
        // Register our short-code [ank_google_map]
        add_shortcode('ank_google_map', array($this, 'process_shortcode'));
        // Store database options for later use
        $this->db = get_option('ank_google_map');
    }


    /**
     * Returns dynamic javascript options to be used by frontend js
     * @return array
     */
    private function get_js_options()
    {
        $db = $this->db;

        $map_type_array = array(
            1 => 'ROADMAP',
            2 => 'SATELLITE',
            3 => 'HYBRID',
            4 => 'TERRAIN',
        );

        $marker_anim_array = array(
            1 => 'NONE',
            2 => 'BOUNCE',
            3 => 'DROP',
        );

        $return_array = array(
            'map' => array(
                'lat' => $db['map_Lat'],
                'lng' => $db['map_Lng'],
                'zoom' => $db['map_zoom'],
                'type' => $map_type_array[$db['map_type']],
            ),
            'marker' => array(
                'enabled' => absint($db['marker_on']),
                'animation' => esc_js($marker_anim_array[$db['marker_anim']]),
                'title' => esc_js($db['marker_title']),
                'color' => $this->get_marker_url($db['marker_color']),
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
            ),
            'mobile' => array(
                'scrollwheel' => absint($db['disable_mouse_wheel']),
                'draggable' => absint($db['disable_drag_mobile']),
            )
        );

        return $return_array;
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
        $w_unit = ($db["div_width_unit"] === 1) ? 'px' : '%';
        $b_color = ($db["div_border_color"] === '') ? '' : 'border:1px solid ' . esc_attr($db["div_border_color"]);
        echo '<div class="agm-canvas" id="agm-canvas" style="margin: 0 auto;width:' . esc_attr($db["div_width"]) . $w_unit . ';height:' . esc_attr($db["div_height"]) . 'px;' . $b_color . '"></div>';


        // Decide language code
        $lang_code = (esc_attr($db['map_lang_code']) === '') ? '' : '&language=' . esc_attr($db['map_lang_code']);
        //Decide API key
        $api_key = empty($db['api_key']) ? '' : '&key=' . esc_js($db['api_key']);
        // Enqueue google map api
        wp_enqueue_script('agm-google-map-api', "https://maps.googleapis.com/maps/api/js?v=3.24" . $lang_code . $api_key, array(), null, true);

        // Enqueue frontend js file
        $is_min = (defined('WP_DEBUG') && WP_DEBUG == true) ? '' : '.min';
        wp_enqueue_script('agm-frontend-js', plugins_url('assets/frontend' . $is_min . '.js', AGM_BASE_FILE), array('agm-google-map-api'), AGM_PLUGIN_VERSION, true);

        // WP inbuilt hack to print js options object just before this script
        wp_localize_script('agm-frontend-js', '_agmOpt', $this->get_js_options());
        return ob_get_clean();
    }


    /**
     * We depends on Google server for maker images
     * @link http://ex-ample.blogspot.in/2011/08/all-url-of-markers-used-by-google-maps.html
     */
    private function get_marker_url($id)
    {

        $base_url = 'https://maps.gstatic.com/intl/en_us/mapfiles/';
        $path = array(
            // Key 1 is reserved for default
            '2' => $base_url . 'marker.png',
            '3' => $base_url . 'marker_black.png',
            '4' => $base_url . 'marker_grey.png',
            '5' => $base_url . 'marker_orange.png',
            '6' => $base_url . 'marker_white.png',
            '7' => $base_url . 'marker_yellow.png',
            '8' => $base_url . 'marker_purple.png',
            '9' => $base_url . 'marker_green.png',
        );
        if (array_key_exists($id, $path)) {
            return $path[$id];
        } else {
            return false;
        }

    }

}