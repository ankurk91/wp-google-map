<?php
/*
 * Settings Page for "Ank Google Map" Plugin
 *
 */

/* no direct access*/
if (!defined('ABSPATH')) exit;

if (!class_exists('Ank_Google_Map')) {
    wp_die(__('This file can not be run alone. This file is the part of ank-google-map plugin.'));
}

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

    if(strlen($agm_options['info_text'])<=1000){
        /*
        * Save posted data back to database
        */
    update_option('ank_google_map', $agm_options);
    echo "<div class='updated'><p>Your settings has been <b>saved</b>.&emsp;You can always use <code>[ank_google_map]</code> shortcode.</p></div>";

    }else{
        echo "<div class='error'>Nothing saved, Info Window Text should not exceed 1000 characters . Current Length is: ".strlen($agm_options['info_text'])."</div>";

    }
    /*
     * Detect if cache is enabled and warn user to flush cache
     */
   if(WP_CACHE){
       echo "<div class='error warning'>It seems that a caching/performance plugin is active on this site. Please manually <b>invalidate/flush</b> that plugin <b>cache</b> to reflect any settings you saved here.</div>";
   }
}
/*
 *
 * Display notice if current wp installation does not support color picker
 */

if(version_compare($GLOBALS['wp_version'],'3.5','<')){
    echo "<div class='error'>Color Picker won't work here. Please upgrade your WordPress to latest (v3.5+).</div>";
}

?>
<!-- agm options page start -->
<style type="text/css">
    input[type=range], select { cursor: pointer }
    .agm_tbl { width: 100%; border: none; border-collapse: collapse}
    .agm_tbl td{padding: 2px}
    .agm_tbl tr:first-child td:first-child { width: 15%; }
    .agm_tbl tr td:first-child { font-weight: bold; padding-left: 2% }
    #agm_map_canvas { height: 250px; width: 99%; border: 1px solid #bcbcbc;}
    option[selected]{color: #0076b3 }
    #agm_zoom_pre{color: #2290d1 }
    .gmnoprint img { max-width: none; }
    #agm_auto_holder{ position: relative; }
    #agm_auto_holder:before{ transform:rotate(720deg);position: absolute; top: -3px; left: 3px; color: #02768c; font-size: 22px; }
    #agm_auto_holder input[type=text]{ padding-left: 25px; width: 99%;font-weight: bolder; }
    .hndle{ cursor: default!important; background: #F5F5F5; border-bottom-color:#DFDFDF!important; }
    div.warning{border-left-color: #ffd504 !important; }
    .agm_notice{margin:0;padding:5px;font-size:12px}
    #agm_meta_ajax_result{display: none;font-weight: bold}
    @media screen and (max-width:782px){.agm_notice{display: none}}
</style>
<div class="wrap">
    <h2 style="line-height: 1"><i class="dashicons-before dashicons-admin-generic" style="color: #005299"> </i>Ank Google Map Settings</h2>
    <div id="poststuff">
    <form action="" method="post">
        <div class="postbox">
        <h3 class="hndle"><i class="dashicons-before dashicons-admin-appearance" style="color: #02af00"> </i><span>Map Canvas Options</span></h3>
        <div class="inside">
        <table class="agm_tbl">
            <tr>
                <td>Map Canvas Width:</td>
                <td><input required type="number" min="1" name="div_width" value="<?php echo esc_attr($agm_options['div_width']); ?>">
                    <select name="div_width_unit">
                        <optgroup label="Unit"></optgroup>
                        <option <?php if (esc_attr($agm_options['div_width_unit']) === '1') echo 'selected' ?> value="1"> px</option>
                        <option <?php if (esc_attr($agm_options['div_width_unit']) === '2') echo 'selected' ?> value="2"> %</option>
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
        </div>
        </div><!-- post box ends -->
        <!--- tab2 start-->
        <div class="postbox">
        <h3 class="hndle"><i class="dashicons-before dashicons-admin-settings" style="color: #458eb3"> </i>Configure Map Options</h3>
        <div class="inside">
            <table class="agm_tbl">
            <tr>
                <td>Latitude:</td>
                <td><input id="agm_lat" pattern="[0-9\-.]+" title="Only Numbers [0-9], dash[-] and dot[.]" placeholder='eg 33.123333' type="text" required name="map_Lat" value="<?php echo esc_attr($agm_options['map_Lat']); ?>"></td>
                <td rowspan="6">
                    <span class="dashicons-before dashicons-search" id="agm_auto_holder"><input id="agm_autocomplete" type="text" placeholder="Enter an address here to get instant results" maxlength="200"></span>
                    <div id="agm_map_canvas"></div>
                    <i><b>Quick Tip</b>: Right click on this map to set new Latitude and Longitude values.</i><br>
                    <i>You can also drag marker to your desired location to set that point as the new center of map.</i>
                </td>
            </tr>
            <tr>
                <td>Longitude:</td>
                <td><input id="agm_lng" pattern="[0-9\-.]+" title="Only Numbers [0-9], dash[-] and dot[.]" placeholder='eg 77.456789' type="text" required name="map_Lng" value="<?php echo esc_attr($agm_options['map_Lng']); ?>"></td>
            </tr>
            <tr>
                <td>Zoom Level: <b><i id="agm_zoom_pre"><?php echo esc_attr($agm_options['map_zoom']); ?></i></b></td>
                <td><input title="Hold me and slide to change zoom" id="agm_zoom" type="range" max="21" min="1" required name="map_zoom" value="<?php echo esc_attr($agm_options['map_zoom']); ?>"></td>
            </tr>
            <tr>
                <td>Disable Controls:</td>
                <td><input <?php if (esc_attr($agm_options['map_control_1']) === '1') echo 'checked' ?> type="checkbox" name="map_control_1" id="map_control_1"><label for="map_control_1">Disable Pan Control</label><br>
                    <input <?php if (esc_attr($agm_options['map_control_2']) === '1') echo 'checked' ?> type="checkbox" name="map_control_2" id="map_control_2"><label for="map_control_2">Disable Zoom Control</label><br>
                    <input <?php if (esc_attr($agm_options['map_control_3']) === '1') echo 'checked' ?> type="checkbox" name="map_control_3" id="map_control_3"><label for="map_control_3">Disable MapType Control</label><br>
                    <input <?php if (esc_attr($agm_options['map_control_4']) === '1') echo 'checked' ?> type="checkbox" name="map_control_4" id="map_control_4"><label for="map_control_4">Disable StreetView Control</label><br>
                    <input <?php if (esc_attr($agm_options['map_control_5']) === '1') echo 'checked' ?> type="checkbox" name="map_control_5" id="map_control_5"><label for="map_control_5">Enable OverviewMap Control</label>
                </td>
            </tr>
            <tr>
                <td>Language Code:</td>
                <td><input placeholder="empty=auto" pattern="([A-Za-z\-]){2,5}" title="Valid Language Code Like: en OR en-US" type="text" name="map_lang_code" value="<?php echo esc_attr($agm_options['map_lang_code']); ?>">
                    <a title="Language Code List" href="https://spreadsheets.google.com/pub?key=p9pdwsai2hDMsLkXsoM05KQ&amp;gid=1" target="_blank" style="text-decoration: none;"><i class="dashicons-before dashicons-editor-help"></i></a></td>
            </tr>
            <tr>
                <td>Map Type:</td>
                <td><select name="map_type">
                        <optgroup label="Map type to show"></optgroup>
                        <option <?php if (esc_attr($agm_options['map_type']) === '1') echo 'selected' ?> value="1">ROADMAP</option>
                        <option <?php if (esc_attr($agm_options['map_type']) === '2') echo 'selected' ?> value="2"> SATELLITE</option>
                        <option <?php if (esc_attr($agm_options['map_type']) === '3') echo 'selected' ?> value="3">HYBRID</option>
                        <option <?php if (esc_attr($agm_options['map_type']) === '4') echo 'selected' ?> value="4">TERRAIN</option>
                    </select></td>
            </tr>
        </table>
        </div>
        </div><!-- post box ends -->
        <!--- tab3 start-->
        <div class="postbox">
        <h3 class="hndle"><i class="dashicons-before dashicons-location" style="color: #dc1515"> </i>Marker Options</h3>
        <div class="inside">
        <table class="agm_tbl">
            <tr>
                <td>Enable marker:</td>
                <td><input <?php if (esc_attr($agm_options['marker_on']) === '1') echo 'checked' ?> type="checkbox" name="marker_on" id="agm_mark_on">
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
                        <option <?php if (esc_attr($agm_options['marker_anim']) === '1') echo 'selected' ?> value="1">NONE</option>
                        <option <?php if (esc_attr($agm_options['marker_anim']) === '2') echo 'selected' ?> value="2"> BOUNCE</option>
                        <option <?php if (esc_attr($agm_options['marker_anim']) === '3') echo 'selected' ?> value="3">DROP</option>
                    </select></td>
            </tr>
            <tr>
                <td>Marker Color:</td>
                <td><select name="marker_color">
                        <optgroup label="Marker Color"></optgroup>
                        <option <?php if (esc_attr($agm_options['marker_color']) === '1') echo 'selected' ?> value="1">Default</option>
                        <option <?php if (esc_attr($agm_options['marker_color']) === '2') echo 'selected' ?> value="2">Light Red</option>
                        <option <?php if (esc_attr($agm_options['marker_color']) === '3') echo 'selected' ?> value="3">Black</option>
                        <option <?php if (esc_attr($agm_options['marker_color']) === '4') echo 'selected' ?> value="4">Gray</option>
                        <option <?php if (esc_attr($agm_options['marker_color']) === '5') echo 'selected' ?> value="5">Orange</option>
                        <option <?php if (esc_attr($agm_options['marker_color']) === '6') echo 'selected' ?> value="6">White</option>
                        <option <?php if (esc_attr($agm_options['marker_color']) === '7') echo 'selected' ?> value="7">Yellow</option>
                        <option <?php if (esc_attr($agm_options['marker_color']) === '8') echo 'selected' ?> value="8">Purple</option>
                        <option <?php if (esc_attr($agm_options['marker_color']) === '9') echo 'selected' ?> value="9">Green</option>
                    </select></td>
            </tr>
        </table>
        </div>
        </div><!-- post box ends -->
        <!-- tab4 start-->
        <div class="postbox">
        <h3 class="hndle"><i class="dashicons-before dashicons-admin-comments" style="color: #988ccc"> </i>Info Window Options</h3>
        <div class="inside">
        <table class="agm_tbl">
            <tr>
                <td>Enable Info Window:</td>
                <td><input <?php if (esc_attr($agm_options['info_on']) === '1') echo 'checked' ?> type="checkbox" name="info_on" id="agm_info_on">
                    <label for="agm_info_on">Check to enable <i>(also needs marker to be enabled)</i></label></td>
            </tr>
            <tr>
                <td>Info Window State:</td>
                <td><input <?php if (esc_attr($agm_options['info_state']) === '1') echo 'checked' ?> type="checkbox" name="info_state" id="agm_info_state">
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
        </div>
        </div><!-- post box ends -->
        <?php wp_nonce_field('agm_form','_wpnonce-agm_form'); ?>
    </form>
     </div><!--post stuff ends-->
    Created with &hearts; by <a target="_blank" href="http://ank91.github.io/"> Ankur Kumar</a> | <a target="_blank" href="http://ank91.github.io/ank-google-map">View on GitHub</a> | <a target="_blank" href="https://wordpress.org/plugins/ank-google-map">View on WordPress.org</a>

    <?php if(isset($_GET['debug'])){
        echo '<hr><p><h5>Showing Debugging Info:</h5>';
        var_dump($agm_options);
        echo '</p><hr>';
    }?>

</div><!-- end wrap-->
<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?libraries=places"></script>
<script type="text/javascript">window.jQuery || alert('Could not load jQuery.\nThis page needs jQuery to work.')</script>
<script type="text/javascript">
    function $ID(a){
        return document.querySelector('#'+a)||document.getElementById(a);
    }
    function Load_agm_Map() {
        var center = new google.maps.LatLng(<?php echo esc_attr($agm_options['map_Lat']).','.esc_attr($agm_options['map_Lng']) ?>);
        var opt = { overviewMapControl: true, center: center,streetViewControl: false, zoom: <?php echo esc_attr($agm_options['map_zoom']) ?>, mapTypeId: google.maps.MapTypeId.ROADMAP};
        var map = new google.maps.Map(agm_map, opt);

        var agm_lat = jQuery('#agm_lat'),
            agm_lng = jQuery('#agm_lng'),
            agm_zoom = jQuery('#agm_zoom'),
            agm_zoom_pre = jQuery('#agm_zoom_pre');
        var marker = new google.maps.Marker({ draggable: true, position: center, map: map, title: 'Current Position' });

        google.maps.event.addListener(map, 'rightclick', function (event) {
            agm_lat.val(event.latLng.lat());
            agm_lng.val(event.latLng.lng());
            marker.setTitle('Selected Position');
            marker.setPosition(event.latLng);
        });
        google.maps.event.addListener(marker, 'dragend', function (event) {
            agm_lat.val(event.latLng.lat());
            agm_lng.val(event.latLng.lng());
        });
        google.maps.event.addListener(map, 'zoom_changed', function () {
            agm_zoom.val(map.getZoom());
            agm_zoom_pre.html(map.getZoom());
        });
        google.maps.event.addListener(map, 'center_changed', function () {
            var location = map.getCenter();
            agm_lat.val(location.lat());
            agm_lng.val(location.lng());
        });
        /*zoom slider control*/
        agm_zoom.on('input click',function () {
            agm_zoom_pre.html(this.value);
            map.setZoom(parseInt(agm_zoom.val()));
        });
        /*
         *Auto-complete feature
         */
        var map_auto = new google.maps.places.Autocomplete($ID('agm_autocomplete'));
        google.maps.event.addListener(map_auto, 'place_changed', function(){
            var place = map_auto.getPlace();
            if (place.geometry) {
                map.panTo(place.geometry.location);
                marker.setPosition(place.geometry.location);
                map.setZoom(15);
                marker.setTitle(place.formatted_address);
            }
        });

    }/* main function ends here*/
    /*
     * Prevent form submission when user press enter key in auto-complete
     *
     */
    jQuery("#agm_autocomplete").keydown(function (e) {
        if (e.which == 13 ||e.which==13) {
            e.preventDefault();
            e.stopPropagation();
        }
    });

    /*
    *Prepare to load google map
    */
    var agm_map = $ID("agm_map_canvas");
    if (typeof google == "object") {
        google.maps.event.addDomListener(window, "load", Load_agm_Map)
    }
    else {
        agm_map.innerHTML = '<h4 style="text-align: center;color: #ba060b">Failed to load Google Map.<br>Refresh this page and try again.</h4>'
    }

    jQuery(function ($) {
      <?php if(version_compare($GLOBALS['wp_version'],3.5)>=0){
     /*
      * WP v3.5+ inbuilt Color Picker
      * @Docs and options: https://github.com/automattic/Iris
      */
     ?>
        $('#agm_color_field').wpColorPicker();
    <?php } ?>

        $('#screen-options-wrap div#agm_plugin_settings').find('input').change(function(){
            var panel = $(this).parents('div.metabox-prefs');
            var params = panel.find('input').serialize();
            params = params + '&action=save_settings-' + panel.attr('data-id');
            var results= $("#agm_meta_ajax_result");
            results.stop(true,true).fadeOut(0);
            $.post(
                'admin-ajax.php',
                params,
                function(d,s){
                    if(d=='1'&&s=='success'){
                        results.fadeIn(function(){
                            results.delay('10000').fadeOut(0);
                        });
                    }
                }
            );
        });
    });
</script>
<!--agm options page ends here -->
