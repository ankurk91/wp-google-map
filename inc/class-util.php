<?php
namespace Ankur\Plugins\Ank_Google_Map;

/**
 * Class Util
 * @package Ankur\Plugins\Ank_Google_Map
 */
class Util
{
    function __construct()
    {
        //
    }

    /**
     * Read styles.json file
     * @param bool $json Should return json content
     * @return mixed|string|void
     */
    public function get_styles($json = false)
    {
        $file = plugin_dir_path(AGM_BASE_FILE) . 'styles.json';
        $contents = file_get_contents($file);
        if ($json) {
            return $contents;
        }
        return json_decode($contents, true);
    }

    /**
     * Get style property by id
     * @param $id int
     * @return array
     */
    public function get_style_by_id($id)
    {
        $styles = $this->get_styles();

        $found = array_filter($styles, function ($s) use ($id) {
            return ($s['id'] == $id);
        });

        if (is_array($found) && count($found)) {
            return current($found)['style'];
        }

        return array();
    }


    /**
     * We depends on Google server for maker images
     * @link http://ex-ample.blogspot.in/2011/08/all-url-of-markers-used-by-google-maps.html
     */
    public function get_marker_url($id)
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