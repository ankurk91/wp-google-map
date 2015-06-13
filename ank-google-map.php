<?php
/*
Plugin Name: Ank Google Map
Plugin URI: http://ank91.github.io/ank-google-map
Description: Simple, light weight, and non-bloated WordPress Google Map Plugin. Written in pure javascript, no jQuery at all, responsive, configurable, no ads and 100% Free of cost.
Version: 1.6.1
Author: Ankur Kumar
Author URI: http://ank91.github.io/
License: GPL2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/
?>
<?php
/* no direct access*/
if (!defined('ABSPATH')) exit;

define('AGM_PLUGIN_VERSION', '1.6.1');
define('AGM_PLUGIN_SLUG', 'agm_plugin_settings');
define('AGM_AJAX_ACTION', 'agm_meta_settings');

    class Ank_Google_Map
    {

        function __construct()
        {
            if(is_admin()){
            /*
             * Add settings link to plugin list page
            */
            add_filter('plugin_action_links', array($this, 'agm_plugin_actions_links'), 10, 2);
            /*
             * Additional link
             */
            add_filter('plugin_row_meta', array($this, 'agm_plugin_meta_links'), 10, 2);
            }
            /* Save settings if first time */
            if (false == get_option('ank_google_map')) {
                $this->agm_settings_init();
            }
            /*
             * Register our short-code [ank_google_map]
             */
            add_shortcode('ank_google_map', array($this, 'agm_do_shortCode'));


        }/*end constructor*/


        private function agm_settings_page_url()
        {
            return add_query_arg('page', AGM_PLUGIN_SLUG, 'options-general.php');
        }


        function agm_plugin_actions_links($links, $file)
        {
            static $plugin;
            $plugin = plugin_basename(__FILE__);
            if ($file == $plugin && current_user_can('manage_options')) {
                array_unshift(
                    $links,
                    sprintf('<a href="%s">%s</a>', esc_attr($this->agm_settings_page_url()), __('Settings'))
                );
            }

            return $links;
        }

        function agm_plugin_meta_links($links,$file)
        {
            /*
            * additional link on plugin list page
            */
            static $plugin;
            $plugin = plugin_basename( __FILE__ );
            if ( $file == $plugin ) {
                $links[] = '<a target="_blank" href="' . plugins_url('readme.txt',__FILE__) . '">Read Me</a>';
                $links[] = '<a target="_blank" href="http://ank91.github.io/ank-google-map">GitHub</a>';
            }
            return $links;
        }



        function agm_settings_init()
        {
            /*
             * these are default settings
             * save settings in array for faster access
             */
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
                'te_meta_1' => '1' ,
                'te_meta_2' => '0',
                'te_meta_3' => '0',
                'plugin_ver' => AGM_PLUGIN_VERSION,
                'disable_mouse_wheel'  => '0',
                'disable_drag_mobile'  => '1',


            );
            /*
             * save default settings to wp_options table
             *
             */
            add_option('ank_google_map', $new_options);
        }





        function agm_marker_url($id)
        {
            /**
             * We depends on Google server for maker images
             * @source http://ex-ample.blogspot.in/2011/08/all-url-of-markers-used-by-google-maps.html
             */
            $path=array(
                /* 1 is reserved for default */
                '2'=>'https://maps.gstatic.com/intl/en_us/mapfiles/marker.png',
                '3'=>'https://maps.gstatic.com/intl/en_us/mapfiles/marker_black.png',
                '4'=>'https://maps.gstatic.com/intl/en_us/mapfiles/marker_grey.png',
                '5'=>'https://maps.gstatic.com/intl/en_us/mapfiles/marker_orange.png',
                '6'=>'https://maps.gstatic.com/intl/en_us/mapfiles/marker_white.png',
                '7'=>'https://maps.gstatic.com/intl/en_us/mapfiles/marker_yellow.png',
                '8'=>'https://maps.gstatic.com/intl/en_us/mapfiles/marker_purple.png',
                '9'=>'https://maps.gstatic.com/intl/en_us/mapfiles/marker_green.png',
            );
            if(array_key_exists($id,$path)){
                return $path[$id];
            }else{
                return false;
            }

        }

        /* ********* front end started *********  */
        function agm_write_html($options)
        {
            /*
             * Decide some options here
             */
            $w_unit = ($options["div_width_unit"] === 1) ? 'px' : '%';
            $b_color=($options["div_border_color"]==='')? '' : 'border:1px solid '.esc_attr($options["div_border_color"]);
            echo '<div id="agm_map_canvas" style="margin: 0 auto;width:' . esc_attr($options["div_width"]) . $w_unit . ';height:' . esc_attr($options["div_height"]).'px;' . $b_color . '"></div>';

        }

        function agm_write_css()
        {
            /*
             * Special fixes for google map controls.
             * They may appear incorrectly due to theme style
             */ ?><style type='text/css'> .gmnoprint img,#agm_map_canvas img { max-width: none; } </style><?php
        }

        function agm_build_js()
        {

            $options = get_option('ank_google_map');
            /*
             * Decide some options here
             */
            $mapType = 'ROADMAP';
            if ($options['map_type'] === 1) {
                $mapType = 'ROADMAP';
            } elseif ($options['map_type'] === 2) {
                $mapType = 'SATELLITE';
            } elseif ($options['map_type'] === 3) {
                $mapType = 'HYBRID';
            } elseif ($options['map_type'] === 4) {
                $mapType = 'TERRAIN';
            }
            $marker_anim = 'DROP';
            if ($options['marker_on'] === '1') {
                if ($options['marker_anim'] == 2) {
                    $marker_anim = 'BOUNCE';
                } elseif ($options['marker_anim'] == 3) {
                    $marker_anim = 'DROP';
                }
            }
            ?>
            <?php
            /*
            * using ob_start to store content in buffer
            * Note: Don't use single line comment in java script portion
            */
            ob_start();
            ?>
            function loadAgmMap() {
            var wd = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
            var cn = new google.maps.LatLng(<?php echo esc_attr($options['map_Lat']) . ',' . esc_attr($options['map_Lng']) ?>);
            var op = {
            <?php
            if ($options['map_control_1'] === '1') {
                echo " panControl: false, ";
            }
            if ($options['map_control_2'] === '1') {
                echo " zoomControl: false, ";
            }
            if ($options['map_control_3'] === '1') {
                echo " mapTypeControl: false, ";
            }
            if ($options['map_control_4'] === '1') {
                echo " streetViewControl: false, ";
            }
            if ($options['map_control_5'] === '1') {
                echo " overviewMapControl: true, ";
            }
            if ($options['disable_mouse_wheel'] === '1') {
                echo " scrollwheel: false, ";
            }

            if ($options['disable_drag_mobile'] === '1') {
                echo " draggable: wd > 480 ? true : false, ";
            }
            ?>
            center: cn, zoom: <?php echo intval($options['map_zoom']) ?>, mapTypeId: google.maps.MapTypeId.<?php echo $mapType; ?>};
            var map = new google.maps.Map(agm_div, op);
            <?php if ($options['marker_on'] === '1') { ?>
            var mk = new google.maps.Marker({
            <?php
            if ($options['marker_color'] !== 1) {
                echo 'icon:"' . $this->agm_marker_url($options['marker_color']) . '",';
            }
            ?>
            position: cn, map: map <?php if ($options['marker_anim'] !== 1) {
                echo ", animation: google.maps.Animation.$marker_anim";
            } ?>, title: "<?php echo esc_js($options['marker_title']) ?>" });
            <?php if ($options['info_on'] === '1') { ?>
                var iw = new google.maps.InfoWindow({content: "<?php echo wp_slash($options['info_text']) ?>"});
                google.maps.event.addListener(map, 'click', function () {
                iw.close();
                });
            <?php }
              } ?>
            <?php if ($options['marker_on'] === '1' && $options['info_on'] === '1') { ?>
            google.maps.event.addListener(mk, "click", function () {
            iw.open(map, mk);
            mk.setAnimation(null);
            });
            <?php
            if ($options['info_state'] === '1') {
                ?>
                window.setTimeout(function () {
                iw.open(map, mk);
                mk.setAnimation(null);
                }, 2000);
            <?php }
             } ?>
            var rT;
            google.maps.event.addDomListener(window, 'resize', function () {
            if (rT) {
            clearTimeout(rT);
            }
            rT = window.setTimeout(function () {
            map.setCenter(cn);
            }, 300);
            });
            }
            var agm_div = document.getElementById("agm_map_canvas");
            if (agm_div) {
            if (typeof google == "object") {
            google.maps.event.addDomListener(window, "load", loadAgmMap)
            }
            else {
            agm_div.innerHTML = '<p style="text-align: center">Failed to load Google Map.<br>Please try again.</p>';
            agm_div.style.height = "auto";
            }
            }
            <?php
            /*
             * trim the buffered string, will save a few bytes
             */
            return $this->agm_trim_js(ob_get_clean());


        }

        function agm_trim_js($buffer)
        {
            /*we don't try to remove comments- can cause malfunction*/
            /* remove tabs, spaces, newlines, etc. */
            $buffer = str_replace(array("\r\n", "\r", "\t", "\n", '  ', '    ', '     '), '', $buffer);
            /* remove other spaces before/after ) */
            $buffer = preg_replace(array('(( )+\))', '(\)( )+)'), ')', $buffer);
            return $buffer;
        }

        function agm_create_js_file()
        {
            /*write js to a file*/
            $file_name = __DIR__.'/agm-user-js.js';
            $data=$this->agm_build_js();

            $handle = fopen($file_name, 'w');
            if($handle){
                if(!fwrite($handle, $data)){
                    //could not write file
                    @fclose($handle);
                    return false;
                }else{
                    //success
                    @fclose($handle);
                    return true;
                }
            }else{
                //could not open handle
                return false;
            }

        }

        function agm_do_shortCode($params)
        {
            /* We accept one parameter in short-code
            * [ank_google_map css_fix=0] will disable css-fixes
            * Lets Merge user parameters with default
            */
            $params=shortcode_atts(array(
                'css_fix'=>1, /* 1=apply css fix(default), 0=don't apply css fix */
            ),$params);


            ob_start();/* ob_start is here for a reason */
            $options = get_option('ank_google_map');

            if($params['css_fix']==1){
                /* ==write css fixes if== */
                $this->agm_write_css();
            }

            /* ==write html always== */
            $this->agm_write_html($options);

            /* ==enqueue google map api always ==*/
            $lang_code=(esc_attr($options['map_lang_code'])==='')? '' : '?language='.esc_attr($options['map_lang_code']);
            wp_enqueue_script('agm-google-map-api',"//maps.googleapis.com/maps/api/js".$lang_code,array(),null,true);

            /*enqueue our main js here*/
            if(!file_exists(__DIR__.'/agm-user-js.js')){
                /*file not found,try to create js file */
                $this->agm_create_js_file();
            }
            /* unique file version, every time the file get modified */
            $file_ver=esc_attr(filemtime(__DIR__.'/agm-user-js.js'));
            wp_enqueue_script('agm-user-script',plugins_url('agm-user-js.js',__FILE__),array('agm-google-map-api'),$file_ver,true);

            return ob_get_clean();
        }

    } /*end  class ank_google_map*/

/*Init front end class */
global $Ank_Google_Map_Obj;
$Ank_Google_Map_Obj = new Ank_Google_Map();


//load only to wp-admin area
if (isset($Ank_Google_Map_Obj) && is_admin()) {
    /* Include Options Page */
    require(trailingslashit(dirname(__FILE__)) . "agm-options-page.php");
    /*Init option page class class */
    global $Ank_Google_Map_Option_Page_Obj;
    $Ank_Google_Map_Option_Page_Obj = new Ank_Google_Map_Option_Page();

}

/*
 * use [ank_google_map] short code (default)
 * OR
 * use [ank_google_map css_fix=0] to disable css fixes
 */
