<?php
/*
Plugin Name: Ank Google Map
Plugin URI: http://ank91.github.io/ank-google-map
Description: Simple, light weight, and non-bloated WordPress Google Map Plugin. Written in pure javascript, no jQuery at all, responsive, configurable, no ads and 100% Free of cost.
Version: 1.5.6
Author: Ankur Kumar
Author URI: http://ank91.github.io/
License: GPL2
*/
/*
    Copyright 2014  Ankur Kumar  (http://ank91.github.io/)

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
 ?>
<?php
/* no direct access*/
if (!defined('ABSPATH')) exit;

/*check for duplicate class*/
if (!class_exists( 'Ank_Google_Map' ) ) {

   if(!defined('AGM_PLUGIN_VERSION')){
        define('AGM_PLUGIN_VERSION','1.5.6');
    }
    if(!defined('AGM_PLUGIN_SLUG')){
        define('AGM_PLUGIN_SLUG','agm_plugin_settings');
    }
    if(!defined('AGM_AJAX_ACTION')){
        define('AGM_AJAX_ACTION','agm_meta_settings');
    }

class Ank_Google_Map
{

    function __construct()
    {

        /*
         * Add settings link to plugin list page
        */
        add_filter('plugin_action_links', array($this, 'agm_plugin_actions_links'), 10, 2);
        /*
         *  Add settings link under admin->settings menu->ank google map
         */
        add_action('admin_menu', array($this, 'agm_settings_menu'));
        /*
         * Additional link
         */
        add_filter('plugin_row_meta', array($this, 'agm_set_plugin_meta'), 10, 2);
        /*
         * Save settings if first time
         */

        if (false == get_option('ank_google_map')) {
            $this->agm_settings_init();
        }
        /*
         * Register our short-code [ank_google_map]
         */
        add_shortcode('ank_google_map', array($this, 'agm_shortCode'));
        /*
         * Some (Notice) text on top of option page
         */
        add_action('admin_notices', array($this, 'agm_notice'));
         /*add custom screen options panel wp v3.0+*/
        add_filter('screen_settings', array($this,'agm_screen_options'),10,2);
        /* register ajax save function */
        add_action('wp_ajax_'.AGM_AJAX_ACTION, array(&$this, 'agm_save_screen_options'));

    }/*end constructor*/


    private function agm_settings_page_url()
    {
        return add_query_arg('page', AGM_PLUGIN_SLUG, 'options-general.php');
    }

    function agm_settings_menu()
    {
        $page_hook_suffix =add_submenu_page('options-general.php', 'Ank Google Map', 'Ank Google Map', 'manage_options', AGM_PLUGIN_SLUG, array($this, 'agm_settings_page'));
        /*
         * load color picker on plugin options page only
         */
        add_action('admin_print_scripts-'. $page_hook_suffix, array($this, 'agm_add_color_picker'));
        /*
         * add help drop down menu on option page  wp v3.3+
         */
        if ( version_compare( $GLOBALS['wp_version'], '3.3', '>=' ) ) {
            add_action( "load-$page_hook_suffix", array( $this, 'agm_help_menu' ) );
        }

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

    function agm_set_plugin_meta($links,$file)
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

    function agm_settings_page()
    {
       /*
        * get settings page from this separate file
        */
        if(file_exists(__DIR__.'/agm_options_page.php')){
            require('agm_options_page.php');
        }else{
            wp_die(__('A required file not found on server. Reinstall this plugin.<br>If problem persist, contact plugin developer.'));
        }

    }
    /*
     * Add a help tab at top of plugin option page
     */
    public static function agm_help_menu()
    {
        /*get current screen obj*/
        $curr_screen = get_current_screen();

        $curr_screen->add_help_tab(
            array(
                'id'		=> 'agm-overview',
                'title'		=> 'Overview',
                'content'	=>'<p><strong>Thanks for using "Ank Google Map"</strong><br>'.
                'This plugin allows you to put a custom Google Map on your website. Just configure options below and '.
                'save your settings. Copy/paste <code>[ank_google_map]</code> short-code on your page/post/widget to view your map.
                </p>'

            )
        );

        $curr_screen->add_help_tab(
            array(
                'id'		=> 'agm-troubleshoot',
                'title'		=> 'Troubleshoot',
                'content'	=>'<p><strong>Things to remember</strong><br>'.
                '<ul>
                <li>If you are using a cache/performance plugin, you need to flush/delete your site cache after  saving settings here.</li>
                <li>Only one map is supported at this time. Don&apos;t put short-code twice on the same page.</li>
                <li>Only one marker supported at this time, Marker will be positioned at the center of your map.</li>
                <li>Info Window needs marker to be enabled first.</li>
                </ul>
                </p>'

            )
        );
        $curr_screen->add_help_tab(
            array(
                'id'		=> 'agm-more-info',
                'title'		=> 'More',
                'content'	=>'<p><strong>Need more information ?</strong><br>'.
                 'A brief FAQ is available on plugin&apos;s official website.'.
                 'OR click <a href="https://wordpress.org/plugins/ank-google-map/faq/" target="_blank">here</a> for more.<br>'.
                 'You can report a bug at plugin&apos;s GitHub <a href="https://github.com/ank91/ank-google-map" target="_blank">page</a>.'.
                 'I will try to reply as soon as possible. </p>'

            )
        );

        /*help sidebar links */
        $curr_screen->set_help_sidebar(
            '<p><strong>Quick Links</strong></p>' .
            '<p><a href="https://wordpress.org/plugins/ank-google-map/faq/" target="_blank">Plugin FAQ</a></p>' .
            '<p><a href="http://ank91.github.io/ank-google-map" target="_blank">Plugin Home</a></p>'
        );
    }

    function agm_notice()
    {
        /*
         *  Print notice text on top of our option page only
         */
        $dir = is_rtl() ? 'left' : 'right';
        if(strpos( get_current_screen()->id, AGM_PLUGIN_SLUG ) !== false)
            echo "<p class='agm_notice' style='float:".$dir.";'>Explore More, Just click here &Longrightarrow;</p>";
    }

    function agm_screen_options($current, $screen)
    {
        /*
         * @source http://www.w-shadow.com/blog/2010/06/29/adding-stuff-to-wordpress-screen-options/
         */
        if(strpos( $screen->id, AGM_PLUGIN_SLUG ) !== false){
            $options= get_option('ank_google_map');
            $current.='<h5>Text Editor Options</h5>';
            $current.='<div class="metabox-prefs agm_meta_box">';
            $current.='<label for="agm_load_editor"><input ';
            $current.=($options['te_meta_1']==='1')?' checked ':'';
            $current.='type="checkbox" name="agm_load_editor" id="agm_load_editor">Load Text Editor</label> ';
            $current.='<label for="agm_load_media"><input ';
            $current.=($options['te_meta_2']==='1')?' checked ':'';
            $current.='type="checkbox" name="agm_load_media" id="agm_load_media">Show Media Uploader*</label>';
            $current.='<label for="agm_load_teeny"><input ';
            $current.=($options['te_meta_3']==='1')?' checked ':'';
            $current.='type="checkbox" name="agm_load_teeny" id="agm_load_teeny">Load teeny Editor*</label> ';
            $current.='<span id="agm_meta_ajax_result"></span>';
            $current.='<br><i>* Needs 1st option to be enabled.</i>';
            $current.=wp_nonce_field(AGM_AJAX_ACTION,'_wpnonce-agm_meta_form');
            $current.='<input type="hidden" name="action" value="'.AGM_AJAX_ACTION.'"/>';
            $current.='</div>';
        }
        return $current;
    }

    function agm_save_screen_options()
    {
        if(isset($_GET['action'])&&$_GET['action']===AGM_AJAX_ACTION){
            /*
             * WP inbuilt form security check
             */
            check_ajax_referer(AGM_AJAX_ACTION,'_wpnonce-agm_meta_form');
            $options = get_option('ank_google_map');
            $options['te_meta_1']=(isset($_GET['agm_load_editor']))?'1':'0';
            $options['te_meta_2']=(isset($_GET['agm_load_media']))?'1':'0';
            $options['te_meta_3']=(isset($_GET['agm_load_teeny']))?'1':'0';
            update_option('ank_google_map', $options);
            die('1');
        }
    }

    function agm_get_editor($content,$load,$media,$teeny)
    {
        /**
         * decide if browser support editor or not
         */
        if ( user_can_richedit() && $load==='1') {
            $teeny=($teeny=='1')? true:false;
            $media=($media=='1')? true:false;
            wp_editor( $content,'agm-info-editor',
                array(
                    'media_buttons' => $media,
                    'textarea_name' => 'info_text',
                    'textarea_rows' => 5,
                    'teeny' => $teeny,
                    'quicktags' =>true
                ) );

        } else {
            /*
             * else Show normal text-area to user
             */
            echo '<textarea maxlength="1000" rows="3" cols="35" name="info_text" style="width: 99%">'.$content.'</textarea>';
        }
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
            'map_lang_code' => 'en',
            'map_type' => 1,
            'marker_on' => '1',
            'marker_title' => 'I am here',
            'marker_anim' => 1,
            'marker_color' => 1,
            'info_on' => '1',
            'info_text' => '<b>Your Destination</b>',
            'info_state' => '0',
            'te_meta_1' => '1' ,
            'te_meta_2' => '0',
            'te_meta_3' => '0',
            'plugin_ver' => AGM_PLUGIN_VERSION,

        );
        /*
         * save default settings to wp_options table
         *
         */
        add_option('ank_google_map', $new_options);
    }

    function agm_add_color_picker()
    {
        /*
         * Add the color picker js  + css file (for settings page only)
         * Available for wp v3.5+ only
         */
        if(version_compare($GLOBALS['wp_version'],3.5)>=0){
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_script('wp-color-picker');
        }

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
        $b_color=(esc_attr($options["div_border_color"])==='')? '' : 'border:1px solid '.esc_attr($options["div_border_color"]);
        echo '<div id="agm_map_canvas" style="margin: 0 auto;width:' . esc_attr($options["div_width"]) . $w_unit . ';height:' . esc_attr($options["div_height"]).'px;' . $b_color . '"></div>';

    }

    function agm_write_css(){
        /*
         * Special fixes for google map controls.
         * They may appear incorrectly due to theme style
         */
       echo "<style type='text/css'> .gmnoprint img,#agm_map_canvas img { max-width: none; } </style>";
    }

    function agm_write_js()
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
        ?>function Load_agm_Map() {
                var cn = new google.maps.LatLng(<?php echo esc_attr($options['map_Lat']).','.esc_attr($options['map_Lng']) ?>);
                var op = {
                    <?php if($options['map_control_1']==='1'){echo " panControl: false, ";} ?>
                    <?php if($options['map_control_2']==='1'){echo " zoomControl: false, ";} ?>
                    <?php if($options['map_control_3']==='1'){echo " mapTypeControl: false, ";} ?>
                    <?php if($options['map_control_4']==='1'){echo " streetViewControl: false, ";} ?>
                    <?php if($options['map_control_5']==='1'){echo " overviewMapControl: true, ";} ?>
                    center: cn, zoom: <?php echo intval($options['map_zoom']) ?>, mapTypeId: google.maps.MapTypeId.<?php echo $mapType;?>};
                var map = new google.maps.Map(agm_div, op);
                <?php if($options['marker_on']==='1') {?>
                var mk = new google.maps.Marker({
                    <?php
                    if($options['marker_color']!==1){
                    echo 'icon:"'.$this->agm_marker_url($options['marker_color']).'",';
                    }
                    ?>
                    position: cn, map: map <?php if($options['marker_anim']!==1) { echo ", animation: google.maps.Animation.$marker_anim"; }?>, title: "<?php echo esc_js($options['marker_title']) ?>" });
                <?php  if($options['info_on']==='1') {?>
                var iw = new google.maps.InfoWindow({content: "<?php echo wp_slash($options['info_text'])?>"});
                google.maps.event.addListener(map, 'click', function () {
                    iw.close();
                });
                <?php } ?>
                <?php } ?>
                <?php if($options['marker_on']==='1'&&$options['info_on']==='1') {?>
                google.maps.event.addListener(mk, "click", function () {
                    iw.open(map, mk);
                    mk.setAnimation(null);
                });
                <?php
                 if($options['info_state']==='1'){ ?>
                window.setTimeout(function () {
                    iw.open(map, mk);
                    mk.setAnimation(null);
                }, 2000);
                <?php } ?>
                <?php } ?>

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
                    google.maps.event.addDomListener(window, "load", Load_agm_Map)
                }
                else {
                    agm_div.innerHTML = '<h4 style="text-align: center">Failed to load Google Map.<br>Please try again.</h4>'
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

    function agm_create_js(){
         /*write js to a file*/
        $file_name = __DIR__.'/agm-user-js.js';
        $data=$this->agm_write_js();
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

    function agm_shortCode($params)
    {
        /* we accept one parameter in short-code
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
        $api_url= "//maps.googleapis.com/maps/api/js".$lang_code;
        wp_enqueue_script('agm-google-map-api',$api_url,array(),null,true);

        /*enqueue our main js here*/
        if(!file_exists(__DIR__.'/agm-user-js.js')){
            /*file not found,try to create js file */
           $this->agm_create_js();
        }
        /* unique file version, every time the file get modified */
        $file_ver=esc_attr(filemtime(__DIR__.'/agm-user-js.js'));
        wp_enqueue_script('agm-user-script',plugins_url('agm-user-js.js',__FILE__),array('agm-google-map-api'),$file_ver,true);

        return ob_get_clean();
    }

} /*end  class ank_google_map*/



} /*end if class exists*/

if ( class_exists( 'Ank_Google_Map' ) ) {
    /*Init class */
    if(!isset($Ank_Google_Map_Obj)){
    $Ank_Google_Map_Obj=new Ank_Google_Map();
    }
}


/*
 * use [ank_google_map] short code (default)
 * OR
 * use [ank_google_map css_fix=0] to disable css fixes
 */
?>
