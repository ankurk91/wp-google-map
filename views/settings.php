<div class="wrap">
    <h1><?php _e('Google Map', 'ank-google-map') ?>
        <small>(v<?php echo AGM_PLUGIN_VERSION; ?>)</small>
    </h1>

    <div class="option-page-wrap">

        <h2 class="nav-tab-wrapper" id="wpt-tabs">
            <a class="nav-tab" id="wpt-general-tab" href="#top#wpt-general"><?php _e('General', 'ank-google-map') ?></a>
            <a class="nav-tab" id="wpt-loc-tab" href="#top#wpt-loc"><?php _e('Location', 'ank-google-map') ?></a>
            <a class="nav-tab" id="wpt-marker-tab" href="#top#wpt-marker"><?php _e('Marker', 'ank-google-map') ?></a>
            <a class="nav-tab" id="wpt-info-tab" href="#top#wpt-info"><?php _e('Info Window', 'ank-google-map') ?></a>
        </h2>

        <form action="<?php echo admin_url('options.php') ?>" method="post" id="agm-form" novalidate>
            <?php
            //wp inbuilt nonce field , etc
            settings_fields($option_group);
            ?>
            <div class="tab-content-wrapper">
                <section id="wpt-general" class="tab-content">
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php _e('API Key', 'ank-google-map'); ?></th>
                            <td>
                                <input type="text"
                                       placeholder="<?php _e('Paste your API key here', 'ank-google-map'); ?>"
                                       name="ank_google_map[api_key]"
                                       value="<?php echo esc_attr($db['api_key']); ?>">
                                <a href="https://developers.google.com/maps/documentation/javascript/get-api-key"
                                   target="_blank"><i class="dashicons-before dashicons-editor-help"></i></a>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Canvas Width', 'ank-google-map'); ?></th>
                            <td><input type="number" placeholder="100" name="ank_google_map[div_width]"
                                       value="<?php echo esc_attr($db['div_width']); ?>"></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Canvas Width Unit', 'ank-google-map'); ?></th>
                            <td>
                                <select name="ank_google_map[div_width_unit]">
                                    <option disabled label="Width Unit"></option>
                                    <option <?php selected($db['div_width_unit'], '1') ?> value="1"> px
                                        (<?php _e('Pixel', 'ank-google-map'); ?>)
                                    </option>
                                    <option <?php selected($db['div_width_unit'], '2') ?> value="2"> %
                                        (<?php _e('Percent', 'ank-google-map'); ?>)
                                    </option>
                                </select>
                                <p class="description"><?php _e('Choose % (percent) to make it responsive', 'ank-google-map'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Canvas Height', 'ank-google-map'); ?></th>
                            <td>
                                <input type="number" placeholder="400" name="ank_google_map[div_height]"
                                       value="<?php echo esc_attr($db['div_height']); ?>">
                                <p class="description"><?php _e('Height will be in px', 'ank-google-map'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Canvas Border Color', 'ank-google-map'); ?></th>
                            <td><input placeholder="#fafafa" type="text" id="agm-border-color"
                                       name="ank_google_map[div_border_color]"
                                       value="<?php echo esc_attr($db['div_border_color']); ?>">
                                <p class="description"><?php _e('Border will be 1px solid. Leave empty to disable', 'ank-google-map'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Disable Mouse Wheel Zoom', 'ank-google-map'); ?></th>
                            <td><input <?php checked($db['disable_mouse_wheel'], '1') ?>
                                    type="checkbox" name="ank_google_map[disable_mouse_wheel]">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Disable Dragging on Mobile Devices', 'ank-google-map'); ?></th>
                            <td><input <?php checked($db['disable_drag_mobile'], '1') ?>
                                    type="checkbox" name="ank_google_map[disable_drag_mobile]">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Map Language', 'ank-google-map'); ?></th>
                            <td><input placeholder="en"
                                       type="text"
                                       name="ank_google_map[map_lang_code]"
                                       value="<?php echo esc_attr($db['map_lang_code']); ?>">
                                <a href="https://developers.google.com/maps/faq#languagesupport" target="_blank"><i
                                        class="dashicons-before dashicons-editor-help"></i></a>
                            </td>
                        </tr>
                    </table>
                </section>

                <section id="wpt-loc" class="tab-content">
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php _e('Latitude', 'ank-google-map'); ?></th>
                            <td><input id="agm-lat" placeholder='33.123333' type="text" name="ank_google_map[map_Lat]"
                                       value="<?php echo esc_attr($db['map_Lat']); ?>"></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Longitude', 'ank-google-map'); ?></th>
                            <td><input id="agm-lng" placeholder='77.456789' type="text" name="ank_google_map[map_Lng]"
                                       value="<?php echo esc_attr($db['map_Lng']); ?>"></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Zoom Level', 'ank-google-map'); ?> <b><span id="agm-zoom-val"
                                                                                               style="color:#0073aa"><?php echo esc_attr($db['map_zoom']); ?></span></b>
                            </th>
                            <td><input id="agm-zoom" type="range" max="21"
                                       min="1" name="ank_google_map[map_zoom]"
                                       value="<?php echo esc_attr($db['map_zoom']); ?>">

                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Disable Controls', 'ank-google-map'); ?></th>
                            <td>
                                <label><input <?php checked($db['map_control_2'], '1') ?> type="checkbox"
                                                                                          name="ank_google_map[map_control_2]">
                                    <?php _e('Disable Zoom Control', 'ank-google-map'); ?></label><br>
                                <label><input <?php checked($db['map_control_3'], '1') ?> type="checkbox"
                                                                                          name="ank_google_map[map_control_3]">
                                    <?php _e('Disable MapType Control', 'ank-google-map'); ?></label><br>
                                <label><input <?php checked($db['map_control_4'], '1') ?> type="checkbox"
                                                                                          name="ank_google_map[map_control_4]">
                                    <?php _e('Disable StreetView Control', 'ank-google-map'); ?></label><br>
                                <label><input <?php checked($db['map_control_5'], '1') ?> type="checkbox"
                                                                                          name="ank_google_map[map_control_5]">
                                    <?php _e('Disable FullScreen Control', 'ank-google-map'); ?></label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Map Type', 'ank-google-map'); ?></th>
                            <td><select name="ank_google_map[map_type]">
                                    <option disabled label="Map type"></option>
                                    <option <?php selected($db['map_type'], '1') ?>
                                        value="1"><?php _e('ROADMAP', 'ank-google-map'); ?>
                                    </option>
                                    <option <?php selected($db['map_type'], '2') ?>
                                        value="2"><?php _e('SATELLITE', 'ank-google-map'); ?>
                                    </option>
                                    <option <?php selected($db['map_type'], '3') ?>
                                        value="3"><?php _e('HYBRID', 'ank-google-map'); ?>
                                    </option>
                                    <option <?php selected($db['map_type'], '4') ?>
                                        value="4"><?php _e('TERRAIN', 'ank-google-map'); ?>
                                    </option>
                                </select></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Map Style', 'ank-google-map'); ?></th>
                            <td>
                                <select id="agm-map-style" name="ank_google_map[map_style]">
                                    <option disabled label="Map style"></option>
                                    <option value="0">None</option>
                                    <?php
                                    foreach ($styles as $item) { ?>
                                        <option
                                            value="<?php echo $item['id'] ?>" <?php selected($db['map_style'], $item['id']) ?>><?php echo ucwords(str_replace('-', ' ', $item['name'])) ?></option>
                                    <?php } ?>
                                </select>
                                <p class="description"><?php _e('Styles taken from', 'ank-google-map') ?> <a
                                        target="_blank" href="https://snazzymaps.com/">snazzymaps</a></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Set Location', 'ank-google-map'); ?></th>
                            <td>
                                <input type="text" size="40" id="agm-search" class="agm-search">
                                <div id="agm-canvas" class="agm-canvas"></div>
                                <p class="description"><?php _e('Right click on map to set that point as new center of map', 'ank-google-map'); ?></p>
                            </td>
                        </tr>
                    </table>
                </section>

                <section id="wpt-marker" class="tab-content">
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php _e('Enable Marker', 'ank-google-map'); ?></th>
                            <td><label>
                                    <input <?php checked($db['marker_on'], '1') ?> type="checkbox"
                                                                                   name="ank_google_map[marker_on]">
                                    <?php _e('Check to enable', 'ank-google-map'); ?></label></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Marker Title', 'ank-google-map'); ?></th>
                            <td><input type="text"
                                       name="ank_google_map[marker_title]"
                                       value="<?php echo esc_attr($db['marker_title']); ?>">
                                <p class="description"><?php _e('Don\'t use html tags here', 'ank-google-map'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Marker Animation', 'ank-google-map'); ?></th>
                            <td><select name="ank_google_map[marker_anim]">
                                    <option disabled label="Marker Animation"></option>
                                    <option <?php selected($db['marker_anim'], '1') ?>
                                        value="1"><?php _e('NONE', 'ank-google-map'); ?>
                                    </option>
                                    <option <?php selected($db['marker_anim'], '2') ?>
                                        value="2"><?php _e('BOUNCE', 'ank-google-map'); ?>
                                    </option>
                                    <option <?php selected($db['marker_anim'], '3') ?>
                                        value="3"><?php _e('DROP', 'ank-google-map'); ?>
                                    </option>
                                </select></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Marker Color', 'ank-google-map'); ?></th>
                            <td><select name="ank_google_map[marker_color]">
                                    <option disabled label="Marker Color"></option>
                                    <option <?php selected($db['marker_color'], '1') ?>
                                        value="1"><?php _e('Default', 'ank-google-map'); ?></option>
                                    <option <?php selected($db['marker_color'], '2') ?>
                                        value="2"><?php _e('Light Red', 'ank-google-map'); ?></option>
                                    <option <?php selected($db['marker_color'], '3') ?>
                                        value="3"><?php _e('Black', 'ank-google-map'); ?>
                                    </option>
                                    <option <?php selected($db['marker_color'], '4') ?>
                                        value="4"><?php _e('Gray', 'ank-google-map'); ?>
                                    </option>
                                    <option <?php selected($db['marker_color'], '5') ?>
                                        value="5"><?php _e('Orange', 'ank-google-map'); ?>
                                    </option>
                                    <option <?php selected($db['marker_color'], '6') ?>
                                        value="6"><?php _e('White', 'ank-google-map'); ?>
                                    </option>
                                    <option <?php selected($db['marker_color'], '7') ?>
                                        value="7"><?php _e('Yellow', 'ank-google-map'); ?>
                                    </option>
                                    <option <?php selected($db['marker_color'], '8') ?>
                                        value="8"><?php _e('Purple', 'ank-google-map'); ?>
                                    </option>
                                    <option <?php selected($db['marker_color'], '9') ?>
                                        value="9"><?php _e('Green', 'ank-google-map'); ?>
                                    </option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Marker File URL', 'ank-google-map'); ?></th>
                            <td>
                                <input type="text" name="ank_google_map[marker_file]"
                                       value="<?php echo esc_url($db['marker_file']); ?>"
                                       placeholder="https://example.com/icon-50.png">
                                <button id="agm-marker-file" type="button" class="button button-secondary wp-hide-pw"
                                        title="<?php _e('Select from Media Library','ank-google-map');?>">
                                    <i class="dashicons dashicons-format-image"></i>
                                </button>
                                <p class="description"><?php _e('Full URL to marker icon image file', 'ank-google-map') ?></p>
                            </td>
                        </tr>
                    </table>
                </section>
                <section id="wpt-info" class="tab-content">
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php _e('Enable Info Window', 'ank-google-map'); ?></th>
                            <td><label><input <?php checked($db['info_on'], '1') ?> type="checkbox"
                                                                                    name="ank_google_map[info_on]">
                                    <?php _e('Check to enable', 'ank-google-map'); ?> </label>
                                <p class="description"><?php _e('Needs marker to be enabled', 'ank-google-map'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Info Window State', 'ank-google-map'); ?></th>
                            <td><label>
                                    <input <?php checked($db['info_state'], '1') ?> type="checkbox"
                                                                                    name="ank_google_map[info_state]">
                                    <?php _e('Shown by default', 'ank-google-map'); ?></label></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Info Window Text', 'ank-google-map'); ?></th>
                            <td>
                                <?php
                                wp_editor($db['info_text'], 'agm-info-editor',
                                    array(
                                        'media_buttons' => false, //disable media uploader
                                        'textarea_name' => 'ank_google_map[info_text]',
                                        'textarea_rows' => 5,
                                        'teeny' => false,
                                        'quicktags' => true
                                    ));
                                ?>
                                <p class="description"><?php _e('HTML allowed', 'ank-google-map'); ?></p>
                            </td>
                        </tr>
                    </table>
                </section>
            </div>
            <?php submit_button() ?>
        </form>
    </div>
    <hr>
    <p>
        Developed with &hearts; by <a target="_blank" href="https://ankurk91.github.io/?utm_source=<?php echo rawurlencode(get_home_url()) ?>&amp;utm_medium=plugin_options_page&amp;utm_campaign=ank-google-map">Ankur Kumar</a> |
        Contribute on <a target="_blank" href="https://github.com/ankurk91/wp-google-map">GitHub</a> |
        ★ Rate this on <a target="_blank"
                          href="https://wordpress.org/support/plugin/ank-google-map/reviews/?filter=5">WordPress</a>
    </p>
    <?php if (defined('WP_DEBUG') && WP_DEBUG == true) {
        echo '<hr><p><h5>Debugging info:</h5><pre>';
        print_r($db);
        echo '</pre></p><hr>';
    } ?>
</div>
