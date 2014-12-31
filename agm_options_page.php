<?php
/*
 * Settings Page for "Ank Google Map" Plugin
 *
 */

/* no direct access*/
if (!defined('ABSPATH')) exit;

if (!class_exists('Ank_Google_Map')) {
    wp_die(__('This file can not be run alone. This file is the part of <b>Ank-Google-Map</b> plugin.'));
}

if (!class_exists( 'Ank_Google_Map_Option_Page' ) ) {

    class Ank_Google_Map_Option_Page {

        function __construct()
        {
            /* Add settings link under admin->settings menu->ank google map */
            add_action('admin_menu', array($this, 'agm_settings_menu'));
            /* Some (Notice) text on top of option page */
            add_action('admin_notices', array($this, 'agm_admin_notice'));
            /*add custom screen options panel wp v3.0+*/
            add_filter('screen_settings', array($this,'agm_print_screen_options'),10,2);
            /* register ajax save function */
            if ( is_admin() ) {
                add_action('wp_ajax_' . AGM_AJAX_ACTION, array(&$this, 'agm_save_screen_options'));
            }

        } /*END construct*/

        function agm_settings_menu()
        {
            $page_hook_suffix =add_submenu_page('options-general.php', 'Ank Google Map', 'Ank Google Map', 'manage_options', AGM_PLUGIN_SLUG, array($this, 'AGM_Option_Page'));
            /* load color picker on plugin options page only */
            add_action('admin_print_scripts-'. $page_hook_suffix, array($this, 'agm_add_color_picker'));
            /* add help drop down menu on option page  wp v3.3+ */
            if ( version_compare( $GLOBALS['wp_version'], '3.3', '>=' ) ) {
                add_action( "load-$page_hook_suffix", array( $this, 'agm_help_menu_tab' ) );
            }

        }




        function AGM_Option_Page(){

            if (!current_user_can('manage_options')) {
                wp_die(__('You do not have sufficient permissions to access this page.'));
            }

            /*
            * Empty option array
            */
            $agm_options=array();
            /*
             * Fetch settings from database once
             */
            $agm_options = get_option('ank_google_map');

            if (isset($_POST['save_agm_form']))
            {
                /*
                * WP inbuilt form security check
                */
                check_admin_referer('agm_form','_wpnonce-agm_form');
                /*
                 * Begin sanitize inputs
                 */
                $agm_options['plugin_ver'] = esc_attr(AGM_PLUGIN_VERSION);
                $agm_options['div_width'] = sanitize_text_field($_POST['div_width']);
                $agm_options['div_height'] = sanitize_text_field($_POST['div_height']);
                $agm_options['div_width_unit'] = intval(sanitize_text_field($_POST['div_width_unit']));

                $agm_options['div_border_color'] = sanitize_text_field($_POST['div_border_color']);

                $agm_options['map_Lat'] = sanitize_text_field($_POST['map_Lat']);
                $agm_options['map_Lng'] = sanitize_text_field($_POST['map_Lng']);
                $agm_options['map_zoom'] = intval($_POST['map_zoom']);

                $agm_options['map_control_1']=(isset($_POST['map_control_1']))?'1':'0';
                $agm_options['map_control_2']=(isset($_POST['map_control_2']))?'1':'0';
                $agm_options['map_control_3']=(isset($_POST['map_control_3']))?'1':'0';
                $agm_options['map_control_4']=(isset($_POST['map_control_4']))?'1':'0';
                $agm_options['map_control_5']=(isset($_POST['map_control_5']))?'1':'0';

                $agm_options['map_lang_code'] = sanitize_text_field($_POST['map_lang_code']);
                $agm_options['map_type'] = intval($_POST['map_type']);
                $agm_options['marker_on']=(isset($_POST['marker_on']))?'1':'0';

                $agm_options['marker_title'] = sanitize_text_field($_POST['marker_title']);
                $agm_options['marker_anim'] = intval($_POST['marker_anim']);
                $agm_options['marker_color'] = intval($_POST['marker_color']);

                $agm_options['info_on']=(isset($_POST['info_on']))?'1':'0';
                $agm_options['info_state']=(isset($_POST['info_state']))?'1':'0';


                /*
                 * Lets allow some html in info window
                 * This is same as like we make a new post
                 */
                $agm_options['info_text'] = balanceTags(wp_kses_post($_POST['info_text']),true);

                /*
                 * @Regx => http://stackoverflow.com/questions/7549669/php-validate-latitude-longitude-strings-in-decimal-format
                 */
                if (!preg_match("/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/", $agm_options['map_Lat']))
                {
                    echo "<div class='error'>Nothing saved, Invalid Latitude Value.</div>";

                }
                elseif (!preg_match("/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/", $agm_options['map_Lng']))
                {
                    echo "<div class='error'>Nothing saved, Invalid Longitude Value. </div>";

                } elseif (strlen($agm_options['info_text']) > 1000)
                {
                    echo "<div class='error'>Nothing saved, Info Window Text should not exceed 1000 characters . Current Length is: " . strlen($agm_options['info_text']) . "</div>";
                }
                else {
                    /* Save posted data back to database */
                    update_option('ank_google_map', $agm_options);
                    echo "<div class='updated'><p>Your settings has been <b>saved</b>.&emsp;You can always use <code>[ank_google_map]</code> shortcode.</p></div>";

                    /*create/update a file that contains js code*/
                    global $Ank_Google_Map_Obj;
                    if(isset($Ank_Google_Map_Obj)&&is_object($Ank_Google_Map_Obj)){
                        if($Ank_Google_Map_Obj->agm_create_js_file()===false){
                            echo "<div class='error'>Unable to create JS file in plugin directory. Please make this plugin's folder writable.</div>";
                        }
                    }else{
                        wp_die('test');
                    }
                }

            }/*if isset post ends*/

            ?>
  <!-- lets print admin-css here -->
<style type="text/css"><?php include(__DIR__.'/agm-admin-css.css') ?></style>
  <!-- agm options page start -->
<div class="wrap">
     <h2 style="line-height: 1"><i class="dashicons-before dashicons-location-alt" style="color: #df630d;"> </i>Ank Google Map <i><small>(v<?php echo @AGM_PLUGIN_VERSION;?>)</small></i> Settings</h2>
      <?php

      /* Detect if cache is enabled and warn user to flush cache */
      if(WP_CACHE&&isset($_POST['save_agm_form'])){
          echo "<div class='notice notice-warning'>It seems that a caching/performance plugin is active on this site. Please manually <b>invalidate/flush</b> that plugin <b>cache</b> to reflect the settings you saved here.</div>";
      }
      /* Display notice if current wp installation does not support color picker */
      if(version_compare($GLOBALS['wp_version'],'3.5','<')){
          echo "<div class='notice notice-info'>Color Picker won't work here. Please upgrade your WordPress to latest (v3.5+).</div>";
      }
      ?>
        <div id="poststuff">
                    <form action="" method="post">
                        <div class="postbox">
                            <h3 class="hndle"><i class="dashicons-before dashicons-admin-appearance" style="color: #02af00"> </i><span>Map Canvas Options</span></h3>
                            <table class="agm_tbl inside">
                                <tr>
                                    <td>Map Canvas Width:</td>
                                    <td><input required type="number" min="1" name="div_width" value="<?php echo esc_attr($agm_options['div_width']); ?>">
                                        <select name="div_width_unit">
                                            <optgroup label="Unit"></optgroup>
                                            <option <?php selected($agm_options['div_width_unit'],'1')  ?> value="1"> px</option>
                                            <option <?php selected($agm_options['div_width_unit'],'2')  ?> value="2"> %</option>
                                        </select> <i>Choose % (percent) to make it responsive</i></td>
                                </tr>
                                <tr>
                                    <td>Map Canvas Height:</td>
                                    <td><input required type="number" min="1" name="div_height" value="<?php echo esc_attr($agm_options['div_height']); ?>">
                                        <i>Height will be in px</i>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Border Color:</td>
                                    <td><input placeholder="Color" type="text" id="agm_color_field" name="div_border_color" value="<?php echo esc_attr($agm_options['div_border_color']); ?>"><i style="vertical-align: top">Border will be 1px solid.</i></td>
                                </tr>
                            </table>
                        </div><!-- post box ends -->
                        <!--- tab2 start-->
                        <div class="postbox agm-left-col">
                            <h3 class="hndle"><i class="dashicons-before dashicons-admin-settings" style="color: #458eb3"> </i>Configure Map Options</h3>
                            <table class="agm_tbl inside">
                                <tr>
                                    <td>Latitude:</td>
                                    <td><input id="agm_lat" pattern="-?\d{1,3}\.\d+" title="Latitude" placeholder='33.123333' type="text" required name="map_Lat" value="<?php echo esc_attr($agm_options['map_Lat']); ?>"></td>
                                </tr>
                                <tr>
                                    <td>Longitude:</td>
                                    <td><input id="agm_lng" pattern="-?\d{1,3}\.\d+" title="Longitude" placeholder='77.456789' type="text" required name="map_Lng" value="<?php echo esc_attr($agm_options['map_Lng']); ?>"></td>
                                </tr>
                                <tr>
                                    <td>Zoom Level: <b><i id="agm_zoom_pre"><?php echo esc_attr($agm_options['map_zoom']); ?></i></b></td>
                                    <td><input title="Hold me and slide to change zoom" id="agm_zoom" type="range" max="21" min="1" required name="map_zoom" value="<?php echo esc_attr($agm_options['map_zoom']); ?>"></td>
                                </tr>
                                <tr>
                                    <td>Disable Controls:</td>
                                    <td><input <?php checked($agm_options['map_control_1'],'1')  ?> type="checkbox" name="map_control_1" id="map_control_1"><label for="map_control_1">Disable Pan Control</label><br>
                                        <input <?php checked($agm_options['map_control_2'], '1')  ?> type="checkbox" name="map_control_2" id="map_control_2"><label for="map_control_2">Disable Zoom Control</label><br>
                                        <input <?php checked($agm_options['map_control_3'],'1')  ?> type="checkbox" name="map_control_3" id="map_control_3"><label for="map_control_3">Disable MapType Control</label><br>
                                        <input <?php checked($agm_options['map_control_4'],'1') ?> type="checkbox" name="map_control_4" id="map_control_4"><label for="map_control_4">Disable StreetView Control</label><br>
                                        <input <?php checked($agm_options['map_control_5'],'1')  ?> type="checkbox" name="map_control_5" id="map_control_5"><label for="map_control_5">Enable OverviewMap Control</label>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Language Code:</td>
                                    <td><input placeholder="empty=auto" pattern="([A-Za-z\-]){2,5}" title="Language Code Like: en OR en-US" type="text" name="map_lang_code" value="<?php echo esc_attr($agm_options['map_lang_code']); ?>">
                                        <a title="Language Code List" href="https://spreadsheets.google.com/pub?key=p9pdwsai2hDMsLkXsoM05KQ&amp;gid=1" target="_blank" style="text-decoration: none;"><i class="dashicons-before dashicons-editor-help"></i></a></td>
                                </tr>
                                <tr>
                                    <td>Map Type:</td>
                                    <td><select name="map_type">
                                            <optgroup label="Map type to show"></optgroup>
                                            <option <?php selected($agm_options['map_type'],'1')?> value="1">ROADMAP</option>
                                            <option <?php selected($agm_options['map_type'], '2')?> value="2">SATELLITE</option>
                                            <option <?php selected($agm_options['map_type'],'3') ?> value="3">HYBRID</option>
                                            <option <?php selected($agm_options['map_type'], '4') ?> value="4">TERRAIN</option>
                                        </select></td>
                                </tr>
                            </table>
                        </div><!-- post box ends -->
                        <div class="postbox agm-right-col">
                                <span class="dashicons-before dashicons-search" id="agm_auto_holder"><input id="agm_autocomplete" type="text" placeholder="Enter an address here to get instant results" maxlength="200"></span>
                                <div id="agm_map_canvas"></div>
                          </div>
                        <div class="clearfix"></div>
                        <p class="agm-map-tip">
                            <i><b>Quick Tip</b>: Right click on this map to set new Latitude and Longitude values.
                            You can also drag marker to your desired location to set that point as the new center of map.</i>
                        </p>
                        <!--- tab3 start-->
                        <div class="postbox">
                            <h3 class="hndle"><i class="dashicons-before dashicons-location" style="color: #dc1515"> </i>Marker Options</h3>
                            <table class="agm_tbl inside">
                                <tr>
                                    <td>Enable marker:</td>
                                    <td><input <?php checked($agm_options['marker_on'], '1') ?> type="checkbox" name="marker_on" id="agm_mark_on">
                                        <label for="agm_mark_on">Check to enable</label></td>
                                </tr>
                                <tr>
                                    <td>Marker Title:</td>
                                    <td><input style="width: 40%" maxlength="200" type="text" name="marker_title" value="<?php echo esc_attr($agm_options['marker_title']); ?>">
                                        <i>Don't use html tags here (max 200 characters)</i></td>
                                </tr>
                                <tr>
                                    <td>Marker Animation:</td>
                                    <td><select name="marker_anim">
                                            <optgroup label="Marker Animation"></optgroup>
                                            <option <?php selected($agm_options['marker_anim'],'1') ?> value="1">NONE</option>
                                            <option <?php selected($agm_options['marker_anim'], '2') ?> value="2"> BOUNCE</option>
                                            <option <?php selected($agm_options['marker_anim'], '3') ?> value="3">DROP</option>
                                        </select></td>
                                </tr>
                                <tr>
                                    <td>Marker Color:</td>
                                    <td><select name="marker_color">
                                            <optgroup label="Marker Color"></optgroup>
                                            <option <?php selected($agm_options['marker_color'], '1')  ?> value="1">Default</option>
                                            <option <?php selected($agm_options['marker_color'],'2')  ?> value="2">Light Red</option>
                                            <option <?php selected($agm_options['marker_color'],'3')  ?> value="3">Black</option>
                                            <option <?php selected($agm_options['marker_color'], '4')  ?> value="4">Gray</option>
                                            <option <?php selected($agm_options['marker_color'], '5') ?> value="5">Orange</option>
                                            <option <?php selected($agm_options['marker_color'],'6')  ?> value="6">White</option>
                                            <option <?php selected($agm_options['marker_color'], '7') ?> value="7">Yellow</option>
                                            <option <?php selected($agm_options['marker_color'], '8') ?> value="8">Purple</option>
                                            <option <?php selected($agm_options['marker_color'], '9')  ?> value="9">Green</option>
                                        </select></td>
                                </tr>
                            </table>
                        </div><!-- post box ends -->
                        <!-- tab4 start-->
                        <div class="postbox">
                            <h3 class="hndle"><i class="dashicons-before dashicons-admin-comments" style="color: #988ccc"> </i>Info Window Options</h3>
                            <table class="agm_tbl inside">
                                <tr>
                                    <td>Enable Info Window:</td>
                                    <td><input <?php checked($agm_options['info_on'],'1')?> type="checkbox" name="info_on" id="agm_info_on">
                                        <label for="agm_info_on">Check to enable <i style="display: none">(also needs marker to be enabled)</i></label></td>
                                </tr>
                                <tr>
                                    <td>Info Window State:</td>
                                    <td><input <?php checked($agm_options['info_state'],'1')  ?> type="checkbox" name="info_state" id="agm_info_state">
                                        <label for="agm_info_state">Show by default</label></td>
                                </tr>
                                <tr>
                                    <td>Info Window Text:</td>
                                    <td>
                                        <?php $this->agm_get_editor(stripslashes($agm_options['info_text']),$agm_options['te_meta_1'],$agm_options['te_meta_2'],$agm_options['te_meta_3']);?>
                                        <i>HTML tags are allowed, Max 1000 characters.</i>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="text-align: center;"><p><button class="button button-primary" type="submit" name="save_agm_form" value="Save »"><i class="dashicons-before dashicons-upload"> </i>Save Map Settings </button></p></td>
                                </tr>
                            </table>
                        </div><!-- post box ends -->
                        <?php wp_nonce_field('agm_form','_wpnonce-agm_form'); ?>
                    </form>
                </div><!--post stuff ends-->
 <p class="dev-info">Created with &hearts; by <a target="_blank" href="http://ank91.github.io/"> Ankur Kumar</a> |
 <a target="_blank" href="http://ank91.github.io/ank-google-map">Fork on GitHub</a> |
 <a target="_blank" href="https://wordpress.org/plugins/ank-google-map">View on WordPress.org</a>
  </p>
  <!--dev info ends-->
   <?php if(isset($_GET['debug'])){
       echo '<hr><p><h5>Showing Debugging Info:</h5>';
       var_dump($agm_options);
         echo '</p><hr>';
     }?>
</div><!-- end wrap-->
<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?libraries=places"></script>
<script type="text/javascript">
     /* <![CDATA[ */
         var agm_opt = {
         map_Lat: "<?php echo esc_attr($agm_options['map_Lat']) ?>",
         map_Lng: "<?php echo esc_attr($agm_options['map_Lng']) ?>",
         map_zoom: <?php echo esc_attr($agm_options['map_zoom']) ?>,
         color_picker: <?php echo (version_compare($GLOBALS['wp_version'],3.5)>=0)?"1":"0"?>,
         ajax_url: "<?php echo admin_url( 'admin-ajax.php' ) ?>"
         };
         /* ]]> */
</script>
<script type="text/javascript">window.jQuery || console.log('Could not load jQuery. This plugin page needs jQuery to work.')</script>
<script type="text/javascript" src="<?php echo plugins_url('agm-admin-js.min.js',__FILE__).'?ver='.esc_attr(AGM_PLUGIN_VERSION) ?>"></script>
<!--agm options page ends here -->
<?php

        }  /*end function agm option page*/

        /*
          * Add a help tab at top of plugin option page
          */
        public static function agm_help_menu_tab()
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
                    'id'		=> 'agm-more-info',
                    'title'		=> 'More',
                    'content'	=>'<p><strong>Need more information ?</strong><br>'.
                        'A brief FAQ is available on plugin&apos;s official website.'.
                        'OR click <a href="https://wordpress.org/plugins/ank-google-map/faq/" target="_blank">here</a> for more.<br>'.
                        'Support is only available on WordPress Forums, click <a href="https://wordpress.org/support/plugin/ank-google-map" target="_blank">here</a> to ask anything about this plugin.<br>'.
                        'You can also report a bug at plugin&apos;s GitHub <a href="https://github.com/ank91/ank-google-map" target="_blank">page</a>.'.
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

        function agm_admin_notice()
        {
            /*
             *  Print notice text on top of our option page only
             */
            $dir = is_rtl() ? 'left' : 'right';
            if(strpos( get_current_screen()->id, AGM_PLUGIN_SLUG ) !== false)
                echo "<p class='agm_notice' style='float:".$dir.";'>Explore More, Just click here &Longrightarrow;</p>";
        }

        function agm_print_screen_options($current, $screen)
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

        function agm_get_editor($content='',$load,$media=0,$teeny=0)
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
                echo '<textarea maxlength="1000" rows="3" cols="33" name="info_text" style="width: 98%">'.$content.'</textarea>';
            }
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


    } /*end class agm option page*/
} /*end if isset class exists*/

?>