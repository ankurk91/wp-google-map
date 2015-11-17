<?php
namespace Ank91\Ank_Google_Map_Plugin;
/**
 * Front end class for this plugin
 * Class Ank_Google_Map_Frontend
 */
class Ank_Google_Map_Frontend
{

    private $db_options = array();

    function __construct()
    {

        /* Register our short-code [ank_google_map] */
        add_shortcode('ank_google_map', array($this, 'do_shortcode'));
        /* Store database options*/
        $this->db_options = get_option('ank_google_map');
    }


    /**
     * Print canvas related html to fontend
     */
    function print_canvas_html()
    {
        $options = $this->db_options;
        $w_unit = ($options["div_width_unit"] === 1) ? 'px' : '%';
        $b_color = ($options["div_border_color"] === '') ? '' : 'border:1px solid ' . esc_attr($options["div_border_color"]);
        echo '<div id="agm_map_canvas" style="margin: 0 auto;width:' . esc_attr($options["div_width"]) . $w_unit . ';height:' . esc_attr($options["div_height"]) . 'px;' . $b_color . '"></div>';

    }

    /**
     * Some special fixes , because map controls map appear incorrectly due to theme's css
     */
    function print_css_fix()
    {
        ?>
        <style type='text/css'> .gmnoprint img, #agm_map_canvas img {
                max-width: none;
            } </style><?php
    }

    /**
     * Returns dynamic javascript options to be used by frontend js
     * @return array
     */
    function get_js_options()
    {
        $options = $this->db_options;

        $map_type_array = array(
            1 => 'ROADMAP',
            2 => 'SATELLITE',
            3 => 'HYBRID',
            4 => 'TERRAIN',
        );

        $marker_anim_array = array(
            1 => 'DROP',
            2 => 'BOUNCE',
            3 => 'DROP',
        );

        $return_array = array(
            'map' => array(
                'lat' => $options['map_Lat'],
                'lng' => $options['map_Lng'],
                'zoom' => $options['map_zoom'],
                'type' => $map_type_array[$options['map_type']],
            ),
            'marker' => array(
                'enabled' => absint($options['marker_on']),
                'animation' => esc_js($marker_anim_array[$options['marker_anim']]),
                'title' => esc_js($options['marker_title']),
                'color' => $this->get_marker_url($options['marker_color']),
            ),
            'info_window' => array(
                'enabled' => absint($options['info_on']),
                'text' => wp_slash($options['info_text']),
                'state' => absint($options['info_state']),
            ),
            'controls' => array(
                'panControl' => absint($options['map_control_1']),
                'zoomControl' => absint($options['map_control_2']),
                'mapTypeControl' => absint($options['map_control_3']),
                'streetViewControl' => absint($options['map_control_4']),
                'overviewMapControl' => absint($options['map_control_5']),
            ),
            'mobile' => array(
                'scrollwheel' => absint($options['disable_mouse_wheel']),
                'draggable' => absint($options['disable_drag_mobile']),
            )
        );

        return $return_array;
    }


    /**
     * Function runs behind our short-code
     * @param $params
     * @return string
     */
    function do_shortcode($params)
    {
        /* We accept one parameter in short-code
        * [ank_google_map css_fix=0] will disable css-fixes
        * Lets Merge user parameters with default
        */
        $params = shortcode_atts(array(
            'css_fix' => 1, /* 1=apply css fix(default), 0=don't apply css fix */
        ), $params);


        ob_start();/* ob_start is here for a reason */
        $options = $this->db_options;

        if ($params['css_fix'] === 1) {
            /* ==write css fixes if== */
            $this->print_css_fix();
        }

        /* Write canvas html always */
        $this->print_canvas_html();

        /* Enqueue google map api*/
        $lang_code = (esc_attr($options['map_lang_code']) === '') ? '' : '?language=' . esc_attr($options['map_lang_code']);
        wp_enqueue_script('agm-google-map-api', "//maps.googleapis.com/maps/api/js" . $lang_code, array(), null, true);
        /*Enqueue frontend js file*/
        $is_min = (WP_DEBUG == 1) ? '' : '.min';
        wp_enqueue_script('agm-frontend-js', plugins_url('js/frontend' . $is_min . '.js', __FILE__), array('agm-google-map-api'), AGM_PLUGIN_VERSION, true);
        //wp inbuilt hack to print js options object just before this script
        wp_localize_script('agm-frontend-js', 'agm_opt', $this->get_js_options());
        return ob_get_clean();
    }


    function get_marker_url($id)
    {
        /**
         * We depends on Google server for maker images
         * @source http://ex-ample.blogspot.in/2011/08/all-url-of-markers-used-by-google-maps.html
         */
        $base_url = 'https://maps.gstatic.com/intl/en_us/mapfiles/';
        $path = array(
            /* 1 is reserved for default */
            '2' => $base_url . 'mapfiles/marker.png',
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