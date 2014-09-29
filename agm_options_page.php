<?php
/*
 * Settings Page for "Ank Google Map" Plugin
 *
 */

if(!class_exists('Ank_Google_Map')){exit();}


if(!current_user_can('manage_options'))
{
    wp_die(__('You do not have sufficient permissions to access this page.'));
}

if (isset( $_POST['save_agm'] ) )
{
check_admin_referer( 'agm_form' );//security check
$to_update = get_option('ank_google_map');
//need validation + sanitize here
$to_update['div_width'] = $_POST['div_width'];
$to_update['div_height'] = $_POST['div_height'];
$to_update['div_width_unit'] = $_POST['div_width_unit'];
$to_update['div_height_unit'] = $_POST['div_height_unit'];
$to_update['div_border_color'] = $_POST['div_border_color'];
$to_update['map_Lat'] = sanitize_text_field($_POST['map_Lat']);
$to_update['map_Lng'] = sanitize_text_field($_POST['map_Lng']);
$to_update['map_zoom'] = $_POST['map_zoom'];
if(isset($_POST['map_control_1'])){ $to_update['map_control_1']='1';}else{$to_update['map_control_1']='0';}
if(isset($_POST['map_control_2'])){ $to_update['map_control_2']='1';}else{$to_update['map_control_2']='0';}
if(isset($_POST['map_control_3'])){ $to_update['map_control_3']='1';}else{$to_update['map_control_3']='0';}
if(isset($_POST['map_control_4'])){ $to_update['map_control_4']='1';}else{$to_update['map_control_4']='0';}
$to_update['map_lang_code'] = sanitize_text_field($_POST['map_lang_code']);
$to_update['map_type'] = $_POST['map_type'];
if(isset($_POST['marker_on'])){ $to_update['marker_on']='1';}else{$to_update['marker_on']='0';}
$to_update['marker_title'] = sanitize_text_field($_POST['marker_title']);
$to_update['marker_anim'] = $_POST['marker_anim'];
if(isset($_POST['info_on'])){ $to_update['info_on']='1';}else{$to_update['info_on']='0';}
if(isset($_POST['info_state'])){ $to_update['info_state']='1';}else{$to_update['info_state']='0';}
$to_update['info_text'] = esc_textarea($_POST['info_text']);
update_option('ank_google_map', $to_update);
echo "<div class='updated'><p>Your settings has been <strong>saved</strong>.&emsp;You can always use <code>[ank_google_map]</code> shortcode. </p></div>";

}

$options = get_option('ank_google_map');

?>
<style>
    input[type=range],select{cursor: pointer}
    .agm_tbl{width: 100%; }
    .agm_tbl tr:first-child td:first-child{width: 15%}
    #agm_map_canvas{height: 220px; width: 80%; border: 1px solid #bcbcbc;}
</style>
<div class="wrap">
    <h2><i class="dashicons-before dashicons-admin-generic" style="color: #005299"> </i>Ank Google Map Settings</h2>
    <form action="" method="post">
        <h3><i class="dashicons-before dashicons-admin-appearance" style="color: #d96400"> </i>Map Canvas Options</h3><hr>
        <table class="agm_tbl">
            <tr>
                <td>Map Canvas Width:</td>
                <td><input required type="number" min="1" name="div_width" value="<?php echo esc_attr( $options['div_width'] ); ?>">
                    <select name="div_width_unit"><optgroup label="Unit"></optgroup>
                        <option <?php if(esc_attr( $options['div_width_unit'] )==='1') echo 'selected' ?> value="1">px</option>
                        <option <?php if(esc_attr( $options['div_width_unit'] )==='2') echo 'selected' ?> value="2">%</option>
                    </select> <i>Choose % (percent) to make it responsive</i></td>
            </tr>
            <tr>
                <td>Map Canvas Height:</td>
                <td><input required type="number" min="1" name="div_height" value="<?php echo esc_attr( $options['div_height'] ); ?>">
                    <select name="div_height_unit"><optgroup label="Unit"></optgroup>
                        <option <?php if(esc_attr( $options['div_height_unit'] )==='1') echo 'selected' ?> value="1">px</option>
                        <option <?php if(esc_attr( $options['div_height_unit'] )==='2') echo 'selected' ?> value="2">%</option>
                    </select></td>
            </tr>
            <tr>
                <td>Border Color:</td>
                <td><input type="text" class="agm-color-field" name="div_border_color" value="<?php echo esc_attr( $options['div_border_color'] ); ?>"><i style="vertical-align: top">Border will be 1px solid.</i></td>
            </tr>
        </table>
        <!--- tab2 start-->
        <h3><i class="dashicons-before dashicons-admin-settings" style="color: #458eb3"> </i>Configure Map Options</h3><hr>
        <table class="agm_tbl">
            <tr>
                <td>Latitude:</td>
                <td><input id="agm_lat" placeholder='eg 33.123333' type="text" required name="map_Lat" value="<?php echo esc_attr( $options['map_Lat'] ); ?>"></td>
                <td rowspan="6"> <div id="agm_map_canvas"></div>
                    <i>Quick Tip: Right click on this map to set Latitude, Longitude and Zoom values</i><br><i>You can also drag marker to your desired location to set that point as new center of map.</i></td>
            </tr>
            <tr>
                <td>Longitude:</td>
                <td><input id="agm_lng" placeholder='eg 77.456789' type="text" required name="map_Lng" value="<?php echo esc_attr( $options['map_Lng'] ); ?>"></td>
            </tr>
            <tr>
                <td>Zoom Level: <b><i id="agm_zoom_show"><?php echo esc_attr( $options['map_zoom'] ); ?></i></b></td>
                <td><input id="agm_zoom" type="range" max="21" min="0" required name="map_zoom" value="<?php echo esc_attr( $options['map_zoom'] ); ?>"></td>
            </tr>
            <tr>
                <td>Disable Controls:</td>
                <td><input <?php if(esc_attr( $options['map_control_1'] )==='1') echo 'checked' ?> type="checkbox" name="map_control_1" id="map_control_1"><label for="map_control_1">Disable Pan Control</label><br>
                    <input <?php if(esc_attr( $options['map_control_2'] )==='1') echo 'checked' ?> type="checkbox" name="map_control_2" id="map_control_2"><label for="map_control_2">Disable Zoom Control</label><br>
                    <input <?php if(esc_attr( $options['map_control_3'] )==='1') echo 'checked' ?> type="checkbox" name="map_control_3" id="map_control_3"><label for="map_control_3">Disable MapType Control</label><br>
                    <input <?php if(esc_attr( $options['map_control_4'] )==='1') echo 'checked' ?> type="checkbox" name="map_control_4" id="map_control_4"><label for="map_control_4">Disable StreetView Control</label><br>
                </td>
            </tr>
            <tr>
                <td>Language Code:</td>
                <td><input placeholder="en (default)" pattern="([A-Za-z\-]){2,5}" title="Valid Language Code Like: en OR en-IN" type="text" name="map_lang_code" value="<?php echo esc_attr( $options['map_lang_code'] ); ?>"></td>
            </tr>
            <tr>
                <td>Map Type:</td>
                <td><select name="map_type"><optgroup label="Map type"></optgroup>
                        <option <?php if(esc_attr( $options['map_type'] )==='1') echo 'selected' ?> value="1">ROADMAP</option>
                        <option <?php if(esc_attr( $options['map_type'] )==='2') echo 'selected' ?> value="2">SATELLITE</option>
                        <option <?php if(esc_attr( $options['map_type'] )==='3') echo 'selected' ?> value="3">HYBRID</option>
                        <option <?php if(esc_attr( $options['map_type'] )==='4') echo 'selected' ?> value="4">TERRAIN</option>
                    </select></td>
            </tr>
        </table>
        <!--- tab3 start-->
        <h3><i class="dashicons-before dashicons-location" style="color: #dc1515"> </i>Marker Options</h3><hr>
        <table  class="agm_tbl">
            <tr>
                <td>Enable marker:</td>
                <td><input <?php if(esc_attr( $options['marker_on'] )==='1') echo 'checked' ?> type="checkbox" name="marker_on" id="agm_mark_on"><label for="agm_mark_on"><i>Check to enable</i></label></td>
            </tr>
            <tr>
                <td>Marker Title:</td>
                <td><input maxlength="100" type="text" name="marker_title" value="<?php echo esc_attr( $options['marker_title'] ); ?>"></td>
            </tr>
            <tr>
                <td>Marker Animation:</td>
                <td><select name="marker_anim"><optgroup label="Animation"></optgroup>
                        <option <?php if(esc_attr( $options['marker_anim'] )==='1') echo 'selected' ?> value="1">NONE</option>
                        <option <?php if(esc_attr( $options['marker_anim'] )==='2') echo 'selected' ?> value="2">BOUNCE</option>
                        <option <?php if(esc_attr( $options['marker_anim'] )==='3') echo 'selected' ?> value="3">DROP</option>
                    </select></td>
            </tr>
        </table>
        <!-- tab4 -->
        <h3><i class="dashicons-before dashicons-admin-comments" style="color: #988ccc"> </i>Info Window Options</h3><hr>
        <table class="agm_tbl">
            <tr>
                <td>Enable Info Window:</td>
                <td><input <?php if(esc_attr( $options['info_on'] )==='1') echo 'checked' ?> type="checkbox" name="info_on" id="agm_info_on"><label for="agm_info_on"><i>Click to enable (needs marker to be enabled)</i></label></td>
            </tr>
            <tr>
                <td>Info Window State:</td>
                <td><input <?php if(esc_attr( $options['info_state'] )==='1') echo 'checked' ?> type="checkbox" name="info_state" id="agm_info_state"><label for="agm_info_state"><i>Show by default</i></label></td>
            </tr>
            <tr>
                <td>Info Window Text:</td>
                <td><textarea name="info_text"><?php echo esc_attr( $options['info_text'] ); ?></textarea></td>
            </tr>
        </table>
        <p><input class="button button-primary" type="submit" name="save_agm" value="Save Map Settings »"></p>
        <?php wp_nonce_field( 'agm_form' ); ?>
    </form>
    <hr>
    <h4><i class="dashicons-before dashicons-editor-help" style="color: #52b849"> </i>Instructions:</h4>
    Just save valid settings and use this ShortCode: <code>[ank_google_map]</code><br>
    <ul>Additional Notes:
        <li>• This plugin support only one map at this time. Please don't use same shortcode twice on a page.</li>
        <li>• Supported Language Codes can be found <a href="https://spreadsheets.google.com/pub?key=p9pdwsai2hDMsLkXsoM05KQ&gid=1" target="_blank">here</a></li>
        <li>• Only one marker supported at this time.</li>
        <li>• Marker position will be same as your map's center.</li>
        <li>• In order to use Info Window, you have to enable Marker .</li>
    </ul>
    Created with ❤ by <a target="_blank" href="http://www.facebook.com/ankurthetechgeek"> <i>Ankur Kumar</i></a> | Thanks for using .<br>
</div>
<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?sensor=false&amp;language=en"></script>
<script type="text/javascript">
    function $Id(b) {
        return document.querySelector("#" + b) || document.getElementById(b)
    }

    function load_agm_Map() {
        var center = new google.maps.LatLng(<?php echo $options['map_Lat'].','.$options['map_Lng'] ?>);
        var opt = { center: center, zoom: <?php echo $options['map_zoom'] ?>, mapTypeId: google.maps.MapTypeId.ROADMAP};
        var map = new google.maps.Map(agm_map, opt);

        var agm_lat=$Id('agm_lat'),
        agm_lng=$Id('agm_lng'),
        agm_zoom=$Id('agm_zoom'),
        agm_zoom_show=$Id('agm_zoom_show');
        var marker = new google.maps.Marker({ draggable:true,position:center,map:map,title:'Current Position' });

        google.maps.event.addListener(map, 'rightclick', function(event) {
            agm_lat.value=event.latLng.lat();
            agm_lng.value=event.latLng.lng();
            agm_zoom.value=map.getZoom();
            agm_zoom_show.innerHTML=map.getZoom();
            marker.setMap(map);
            marker.setPosition(event.latLng);
        });
        google.maps.event.addListener(marker, 'dragend', function(event) {
            agm_lat.value=event.latLng.lat(); agm_lng.value = event.latLng.lng();
        });
        google.maps.event.addListener(map, 'zoom_changed', function() {
            agm_zoom.value=map.getZoom();
            agm_zoom_show.innerHTML=map.getZoom();
        });
        google.maps.event.addListener(map, 'center_changed', function() {
            var location = map.getCenter();
            agm_lat.value=location.lat();
            agm_lng.value=location.lng();
            marker.setMap(null);
        });
        agm_zoom.addEventListener("click", function () {
            agm_zoom_show.innerHTML=agm_zoom.value;
            map.setZoom(parseInt(agm_zoom.value));
        });

    }
    var agm_map = $Id("agm_map_canvas");

    if (typeof google == "object"){google.maps.event.addDomListener(window, "load", load_agm_Map)}
     else {agm_map.innerHTML = '<h4 style="text-align: center;color: #994401">Failed to load Google Map. Refresh this page and try again</h4>'}
     agm_zoom.addEventListener("click", function () {
        agm_zoom_show.innerHTML=agm_zoom.value;
    });
    jQuery(function() {
        /*wp inbuilt color picker*/
        jQuery('.agm-color-field').wpColorPicker();
    });

</script>
