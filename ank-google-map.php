<?php
/*
Plugin Name: Ank Google Map
Plugin URI: http://ank91.github.io/ank-google-map/
Description: Simple ,light weight, and non-bloated WordPress Google Map Plugin. Written in pure javascript, no jQuery at all, responsive, configurable, no ads and 100% Free of cost.
Version: 1.1 (b)
Author: Ankur Kumar
Author URI: http://www.ankurkumar.hostreo.com
License: GPL2
*/
/*  Copyright 2014  Ankur Kumar  (http://github.com/ank91)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* no direct access*/
if( !defined( 'ABSPATH' ) ) exit;
//define('WP_DEBUG', true); //enable debugging

class Ank_Google_Map {

    function __construct() {
        add_filter( 'plugin_action_links', array( $this, 'plugin_actions_links'), 10, 2 ); //add settings link to plugin list page
        add_action( 'admin_menu', array( $this, 'settings_menu' ) ); // grant admin permission
        add_shortcode('ank_google_map', array( $this, 'agm_shortCode' ));//create a short code
        add_filter( 'plugin_row_meta', array( $this, 'set_plugin_meta' ), 10, 2 );//additional link



        if( false == get_option( 'ank_google_map' ) ) {
            $this->settings_init(); //save settings first time
        }

        add_action('admin_enqueue_scripts',array( $this, 'agm_add_color_picker' ));//color picker for settings page


    }

    /*  **********add settings link*************** */
    private function settings_page_url() {
        return add_query_arg( 'page', 'agm_settings', 'options-general.php' );
    }

    function settings_menu() {
       add_submenu_page( 'options-general.php', 'Ank Google Map', 'Ank Google Map', 'manage_options', 'agm_settings', array( $this, 'settings_page' ) );

    }

    function plugin_actions_links( $links, $file ) {
		static $plugin;
		$plugin = plugin_basename( __FILE__ );
		if( $file == $plugin && current_user_can('manage_options')) {
            array_unshift(
                $links,
                sprintf( '<a href="%s">%s</a>', esc_attr( $this->settings_page_url() ), __( 'Settings' ) )
            );
        }

		return $links;
	}
    function set_plugin_meta( $links, $file ) {
        static $plugin;
        $plugin = plugin_basename( __FILE__ );
        if ( $file == $plugin ) {
            $links[] = '<a target="_blank" href="'.plugins_url().'/'.basename(__DIR__).'/readme.txt">Read Me</a>';
        }
        return $links;
    }

    function settings_page() {
        //get settings page from this file
        require('agm_options_page.php');
    }

    function settings_init() {
        //save settings in array for faster access
        //these are default settings
        $new_options = array(
            'div_width' => '100',
            'div_width_unit' => '2',
            'div_height' => '300',
            'div_height_unit' => '1',
            'div_border_color' => '#ccc',
            'map_Lat' => '29.6969365',
            'map_Lng' => '77.6766793',
            'map_zoom' => '8',
            'map_control_1' => '0',
            'map_control_2' => '0',
            'map_control_3' => '0',
            'map_control_4' => '0',
            'map_lang_code' => 'en',
            'map_type' => '1',
            'marker_on' => '1',
            'marker_title' => 'I am here',
            'marker_anim' => '1',
            'info_on' => '1',
            'info_text' => 'Your Destination',
            'info_state' => '0'
        );

        add_option( 'ank_google_map', $new_options );
    }
    function agm_add_color_picker() {
            //available for wp v3.5+
            // Add the color picker js  + css file (for settings page only)
            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_script( 'wp-color-picker' );
    }

    /* ********* *********  */
    function write_html_code(){
        $options =get_option( 'ank_google_map' );
        $w_unit=($options["div_width_unit"]===1)?'px':'%';
        $h_unit=($options["div_height_unit"]===1)?'px':'%';
        echo '<div id="agm_map_canvas" style="width:'.esc_attr($options["div_width"]).$w_unit.';height:'.esc_attr($options["div_height"]).$h_unit.';margin: 0 auto;border:1px solid '.esc_attr($options["div_border_color"]).'"></div>';
    }

    function write_js_code(){
        ob_start();
        $options = get_option( 'ank_google_map' );
        $mapType='ROADMAP';
        if($options['map_type']===1){$mapType='ROADMAP';}
        elseif($options['map_type']===2){$mapType='SATELLITE';}
        elseif($options['map_type']===3){$mapType='HYBRID';}
        elseif($options['map_type']===4){$mapType='TERRAIN';}
        $marker_anim='DROP';
        if($options['marker_on']==='1'){
           if($options['marker_anim']==2){$marker_anim='BOUNCE';}elseif($options['marker_anim']==3){$marker_anim='DROP';}
        }
        ?>
        <script src="//maps.googleapis.com/maps/api/js?sensor=false&amp;language=<?php echo esc_attr($options['map_lang_code'])?>"></script>
        <script>
            function loadMap(){
                var cn = new google.maps.LatLng(<?php echo $options['map_Lat'].','.$options['map_Lng'] ?>);
                var op = {
                    <?php if($options['map_control_1']==='1'){echo " panControl: false, ";} ?>
                    <?php if($options['map_control_2']==='1'){echo " zoomControl: false, ";} ?>
                    <?php if($options['map_control_3']==='1'){echo " mapTypeControl: false, ";} ?>
                    <?php if($options['map_control_4']==='1'){echo " streetViewControl: false, ";} ?>
                center: cn, zoom: <?php echo intval($options['map_zoom']) ?>, mapTypeId: google.maps.MapTypeId.<?php echo $mapType;?>};
                var map = new google.maps.Map(agm, op);
                <?php if($options['marker_on']==='1') {?>
                var mk = new google.maps.Marker({position: cn, map:map <?php if($options['marker_anim']!==1) { echo ", animation: google.maps.Animation.$marker_anim"; }?>, title:"<?php echo esc_attr($options['marker_title']) ?>" });
                <?php  if($options['info_on']==='1') {?>
                var iw = new google.maps.InfoWindow({content: "<?php echo addslashes($options['info_text'])?>"});
                google.maps.event.addListener(map, 'click', function(){iw.close();});
                <?php } ?>
                <?php } ?>
                <?php if($options['marker_on']==='1'&&$options['info_on']==='1') {?>
                google.maps.event.addListener(mk, "click", function (){
                    iw.open(map, mk);
                    mk.setAnimation(null);
                });
                <?php
                 if($options['info_state']==='1'){ ?>
                window.setTimeout(function(){ iw.open(map, mk); mk.setAnimation(null); }, 2000);
                <?php } ?>
                <?php } ?>

             var rT;
             google.maps.event.addDomListener(window, 'resize', function(){
                 if (rT){ clearTimeout(rT); }
             rT=window.setTimeout(function(){map.setCenter(cn);}, 250);
             });
            }
            var agm = document.getElementById("agm_map_canvas");
            if (agm){if (typeof google == "object"){google.maps.event.addDomListener(window, "load", loadMap)}
            else {agm.innerHTML = '<h4 style="text-align: center">Failed to load Google Map. Please try again.</h4>'}}</script>
    <?php
        echo $this->trim_js(ob_get_clean());
    }

    function trim_js($buffer) {
        /*we don't try to remove comments- can cause malfunction*/
        /* remove tabs, spaces, newlines, etc. */
        $buffer = str_replace(array("\r\n","\r","\t","\n",'  ','    ','     '), '', $buffer);
        /* remove other spaces before/after ) */
        $buffer = preg_replace(array('(( )+\))','(\)( )+)'), ')', $buffer);
        return $buffer;
    }

    function agm_shortCode() {
            ob_start();
            $this->write_html_code();//write html
            add_action( 'wp_footer', array( $this, 'write_js_code' ) );//put js code in footer
            return ob_get_clean();
    }

}//end class

new Ank_Google_Map();

//use [ank_google_map] short code

?>
