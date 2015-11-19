(function (window, document, jQuery, google) {
    'use strict';

    var agm_opt = window._agm_opt;

    function $By_ID(a) {
        return document.querySelector('#' + a) || document.getElementById(a);
    }

    function _loadGoogleMap() {
        var width = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
        var center = new google.maps.LatLng(parseFloat(agm_opt.map.lat), parseFloat(agm_opt.map.lng));
        var map_options = {
            draggable: width > 480,
            center: center,
            streetViewControl: true,
            zoom: parseInt(agm_opt.map.zoom),
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            zoomControl: true,
            zoomControlOptions: {
                position: google.maps.ControlPosition.LEFT_CENTER
            },
            mapTypeControl: true,
            mapTypeControlOptions: {
                style: google.maps.MapTypeControlStyle.DROPDOWN_MENU,
                position: google.maps.ControlPosition.TOP_RIGHT
            }
        };
        var map = new google.maps.Map(map_canvas_div, map_options);

        var agm_lat = jQuery('#agm_lat'),
            agm_lng = jQuery('#agm_lng'),
            agm_zoom = jQuery('#agm_zoom'),
            agm_zoom_pre = jQuery('#agm_zoom_pre');
        var marker = new google.maps.Marker({
            draggable: true,
            position: center,
            map: map,
            title: 'Current Position'
        });

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
        agm_zoom.on('input click', function () {
            agm_zoom_pre.html(this.value);
            map.setZoom(parseInt(agm_zoom.val()));
        });
        /* Auto-complete feature */
        var map_auto = new google.maps.places.Autocomplete($By_ID('agm_autocomplete'));
        google.maps.event.addListener(map_auto, 'place_changed', function () {
            var place = map_auto.getPlace();
            if (place.geometry) {
                map.panTo(place.geometry.location);
                marker.setPosition(place.geometry.location);
                map.setZoom(15);
                marker.setTitle(place.formatted_address);
            }
        });

    }



    /* Prepare to load google map */
    var map_canvas_div = $By_ID("agm_map_canvas");
    if (typeof google == "object" && google.maps) {
        google.maps.event.addDomListener(window, "load", _loadGoogleMap)
    }
    else {
        map_canvas_div.innerHTML = '<h4 style="text-align: center;color: #ba060b">Failed to load Google Map.<br>Refresh this page and try again.<br>Check your internet connection as well.</h4>'
    }

    jQuery(function ($) {
        /**
         * Prevent form submission when user press enter key in auto-complete
         */
        $("#agm_autocomplete").keydown(function (e) {
            if (e.which == 13 || e.which == 13) {
                e.preventDefault();
                e.stopPropagation();
            }
        });
        /**
         * Show a message
         * Info window needs marker to enabled first
         */
        $("#agm_info_on").click(function () {
            if ($(this).is(":checked"))
                $(this).next('label').find('i:not(:visible)').fadeIn();
        });
        /**
         * Check if color picker is available
         */
        if (agm_opt.color_picker == 1) {
            $('#agm_color_field').wpColorPicker();
        }
    });
})(window, document, jQuery, google);