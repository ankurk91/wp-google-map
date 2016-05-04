<div class="wrap">
    <h2>Ank Google Map
        <small>(v<?php echo AGM_PLUGIN_VERSION; ?>)</small>
        Settings
    </h2>
    <div id="poststuff">
        <form action="<?php echo admin_url('options.php') ?>" method="post" id="agm_form">
            <?php
            $options = get_option('ank_google_map');
            //wp inbuilt nonce field , etc
            settings_fields(self::PLUGIN_OPTION_GROUP);
            ?>
            <div class="postbox">
                <h3 class="hndle"><i class="dashicons-before dashicons-admin-appearance"
                    > </i><span>Map Canvas Options</span></h3>
                <table class="agm_tbl inside">
                    <tr>
                        <td>Map Canvas Width:</td>
                        <td><input required type="number" min="1" name="ank_google_map[div_width]"
                                   value="<?php echo esc_attr($options['div_width']); ?>">
                            <select name="ank_google_map[div_width_unit]">
                                <optgroup label="Unit"></optgroup>
                                <option <?php selected($options['div_width_unit'], '1') ?> value="1"> px
                                </option>
                                <option <?php selected($options['div_width_unit'], '2') ?> value="2"> %
                                </option>
                            </select> <i>Choose % (percent) to make it responsive</i></td>
                    </tr>
                    <tr>
                        <td>Map Canvas Height:</td>
                        <td><input required type="number" min="1" name="ank_google_map[div_height]"
                                   value="<?php echo esc_attr($options['div_height']); ?>">
                            <i>Height will be in px</i>
                        </td>
                    </tr>
                    <tr>
                        <td>Border Color:</td>
                        <td><input placeholder="Color" type="text" id="agm_color_field"
                                   name="ank_google_map[div_border_color]"
                                   value="<?php echo esc_attr($options['div_border_color']); ?>"><i
                                style="vertical-align: top">Border will be 1px solid. Leave empty to
                                disable.</i></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><label><input <?php checked($options['disable_mouse_wheel'], '1') ?>
                                    type="checkbox" name="ank_google_map[disable_mouse_wheel]">Disable Mouse Wheel Zoom</label>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><label><input <?php checked($options['disable_drag_mobile'], '1') ?>
                                    type="checkbox" name="ank_google_map[disable_drag_mobile]">Disable Dragging on
                                Mobile
                                Devices</label></td>
                    </tr>
                </table>
            </div><!-- post box ends -->
            <!--- tab2 start-->
            <div class="postbox agm-left-col">
                <h3 class="hndle"><i class="dashicons-before dashicons-admin-settings"
                    > </i>Configure Map Options</h3>
                <table class="agm_tbl inside">
                    <tr>
                        <td>Latitude:</td>
                        <td><input id="agm_lat" pattern="-?\d{1,3}\.\d+" title="Valid Latitude"
                                   placeholder='33.123333' type="text" required name="ank_google_map[map_Lat]"
                                   value="<?php echo esc_attr($options['map_Lat']); ?>"></td>
                    </tr>
                    <tr>
                        <td>Longitude:</td>
                        <td><input id="agm_lng" pattern="-?\d{1,3}\.\d+" title="Valid Longitude"
                                   placeholder='77.456789' type="text" required name="ank_google_map[map_Lng]"
                                   value="<?php echo esc_attr($options['map_Lng']); ?>"></td>
                    </tr>
                    <tr>
                        <td>Zoom Level: <b><i
                                    id="agm_zoom_pre"><?php echo esc_attr($options['map_zoom']); ?></i></b>
                        </td>
                        <td><input title="Hold me and slide to change zoom" id="agm_zoom" type="range" max="21"
                                   min="1" required name="ank_google_map[map_zoom]"
                                   value="<?php echo esc_attr($options['map_zoom']); ?>"></td>
                    </tr>
                    <tr>
                        <td>Disable Controls:</td>
                        <td>
                            <input <?php checked($options['map_control_2'], '1') ?> type="checkbox"
                                                                                    name="ank_google_map[map_control_2]"
                                                                                    id="map_control_2"><label
                                for="map_control_2">Disable Zoom Control</label><br>
                            <input <?php checked($options['map_control_3'], '1') ?> type="checkbox"
                                                                                    name="ank_google_map[map_control_3]"
                                                                                    id="map_control_3"><label
                                for="map_control_3">Disable MapType Control</label><br>
                            <input <?php checked($options['map_control_4'], '1') ?> type="checkbox"
                                                                                    name="ank_google_map[map_control_4]"
                                                                                    id="map_control_4"><label
                                for="map_control_4">Disable StreetView Control</label>
                        </td>
                    </tr>
                    <tr>
                        <td>Language Code:</td>
                        <td><input placeholder="empty=auto" pattern="([A-Za-z\-]){2,5}"
                                   title="Language Code Like: en OR en-US" type="text"
                                   name="ank_google_map[map_lang_code]"
                                   value="<?php echo esc_attr($options['map_lang_code']); ?>">
                            <a title="Language Code List"
                               href="https://developers.google.com/maps/faq#languagesupport" target="_blank"
                               style="text-decoration: none;"><i
                                    class="dashicons-before dashicons-editor-help"></i></a></td>
                    </tr>
                    <tr>
                        <td>Map Type:</td>
                        <td><select name="ank_google_map[map_type]">
                                <optgroup label="Map type to show"></optgroup>
                                <option <?php selected($options['map_type'], '1') ?> value="1">ROADMAP
                                </option>
                                <option <?php selected($options['map_type'], '2') ?> value="2">SATELLITE
                                </option>
                                <option <?php selected($options['map_type'], '3') ?> value="3">HYBRID
                                </option>
                                <option <?php selected($options['map_type'], '4') ?> value="4">TERRAIN
                                </option>
                            </select></td>
                    </tr>
                </table>
            </div><!-- post box ends -->
            <div class="postbox agm-right-col">
                        <span class="dashicons-before dashicons-search" id="agm_auto_holder"><input
                                id="agm_autocomplete" type="text"
                                placeholder="Enter an address here to get instant results" maxlength="200"></span>

                <div id="agm_map_canvas"></div>
            </div>
            <div class="clearfix"></div>
            <p class="agm-map-tip">
                <i><b>Quick Tip</b>: Right click on this map to set new Latitude and Longitude values.
                    You can also drag marker to your desired location to set that point as the new center of
                    map.</i>
            </p>
            <!--- tab3 start-->
            <div class="postbox">
                <h3 class="hndle"><i class="dashicons-before dashicons-location"> </i>Marker
                    Options</h3>
                <table class="agm_tbl inside">
                    <tr>
                        <td>Enable Marker:</td>
                        <td><input <?php checked($options['marker_on'], '1') ?> type="checkbox"
                                                                                name="ank_google_map[marker_on]"
                                                                                id="agm_mark_on">
                            <label for="agm_mark_on">Check to enable</label></td>
                    </tr>
                    <tr>
                        <td>Marker Title:</td>
                        <td><input style="width: 40%" maxlength="200" type="text" name="ank_google_map[marker_title]"
                                   value="<?php echo esc_attr($options['marker_title']); ?>">
                            <i>Don't use html tags here (max 200 characters)</i></td>
                    </tr>
                    <tr>
                        <td>Marker Animation:</td>
                        <td><select name="ank_google_map[marker_anim]">
                                <optgroup label="Marker Animation"></optgroup>
                                <option <?php selected($options['marker_anim'], '1') ?> value="1">NONE
                                </option>
                                <option <?php selected($options['marker_anim'], '2') ?> value="2">BOUNCE
                                </option>
                                <option <?php selected($options['marker_anim'], '3') ?> value="3">DROP
                                </option>
                            </select></td>
                    </tr>
                    <tr>
                        <td>Marker Color:</td>
                        <td><select name="ank_google_map[marker_color]">
                                <optgroup label="Marker Color"></optgroup>
                                <option <?php selected($options['marker_color'], '1') ?> value="1">Default
                                </option>
                                <option <?php selected($options['marker_color'], '2') ?> value="2">Light
                                    Red
                                </option>
                                <option <?php selected($options['marker_color'], '3') ?> value="3">Black
                                </option>
                                <option <?php selected($options['marker_color'], '4') ?> value="4">Gray
                                </option>
                                <option <?php selected($options['marker_color'], '5') ?> value="5">Orange
                                </option>
                                <option <?php selected($options['marker_color'], '6') ?> value="6">White
                                </option>
                                <option <?php selected($options['marker_color'], '7') ?> value="7">Yellow
                                </option>
                                <option <?php selected($options['marker_color'], '8') ?> value="8">Purple
                                </option>
                                <option <?php selected($options['marker_color'], '9') ?> value="9">Green
                                </option>
                            </select></td>
                    </tr>
                </table>
            </div><!-- post box ends -->
            <!-- tab4 start-->
            <div class="postbox">
                <h3 class="hndle"><i class="dashicons-before dashicons-admin-comments"
                    > </i>Info Window Options</h3>
                <table class="agm_tbl inside">
                    <tr>
                        <td>Enable Info Window:</td>
                        <td><input <?php checked($options['info_on'], '1') ?> type="checkbox"
                                                                              name="ank_google_map[info_on]"
                                                                              id="agm_info_on">
                            <label for="agm_info_on">Check to enable <i style="display: none">(also needs marker
                                    to be enabled)</i></label></td>
                    </tr>
                    <tr>
                        <td>Info Window State:</td>
                        <td><input <?php checked($options['info_state'], '1') ?> type="checkbox"
                                                                                 name="ank_google_map[info_state]"
                                                                                 id="agm_info_state">
                            <label for="agm_info_state">Show by default</label></td>
                    </tr>
                    <tr>
                        <td>Info Window Text:</td>
                        <td>
                            <?php $this->get_text_editor(stripslashes($options['info_text'])); ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <?php submit_button() ?>
                        </td>
                    </tr>
                </table>
            </div><!-- post box ends -->
        </form>
    </div><!--post stuff ends-->
    <p class="dev-info">
        Created with &hearts; by <a target="_blank" href="https://ank91.github.io/">Ankur Kumar</a> |
        Fork on <a target="_blank" href="https://github.com/ank91/ank-google-map">GitHub</a> |
        ★ Rate on <a target="_blank" href="https://wordpress.org/support/view/plugin-reviews/ank-google-map?filter=5">WordPress</a>
    </p>
    <!--dev info ends-->
    <?php if (WP_DEBUG == true) {
        echo '<hr><p><h5>Showing Debugging Info:</h5><pre>';
        print_r($options);
        echo '</pre></p><hr>';
    } ?>
</div><!-- end wrap-->
