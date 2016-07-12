(function (window, document, $) {
    'use strict';

    var opt = window._agm_opt;

    function $getById(a) {
        return document.querySelector('#' + a) || document.getElementById(a);
    }

    function _loadGoogleMap() {
        var width = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
        var center = new google.maps.LatLng(parseFloat(opt.map.lat), parseFloat(opt.map.lng));

        var mapOptions = {
            draggable: (width > 480) || !isTouchDevice(),
            center: center,
            streetViewControl: true,
            zoom: parseInt(opt.map.zoom),
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
        var map = new google.maps.Map(mapCanvas, mapOptions);

        var agm_lat = $('#agm_lat'),
            agm_lng = $('#agm_lng'),
            agm_zoom = $('#agm_zoom'),
            agm_zoom_pre = $('#agm_zoom_pre');

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
        var locSearch = new google.maps.places.Autocomplete($getById('agm_autocomplete'));
        google.maps.event.addListener(locSearch, 'place_changed', function () {
            var place = locSearch.getPlace();
            if (place.geometry) {
                map.panTo(place.geometry.location);
                marker.setPosition(place.geometry.location);
                map.setZoom(15);
                marker.setTitle(place.formatted_address);
            }
        });

    }


    /* Prepare to load google map */
    var mapCanvas = $getById("agm_map_canvas");
    if (typeof google == "object" && google.maps) {
        google.maps.event.addDomListener(window, "load", _loadGoogleMap)
    }
    else {
        mapCanvas.innerHTML = '<h4 style="text-align: center;color: #ba060b">Failed to load Google Map.<br>Refresh this page and try again.<br>Check your internet connection as well.</h4>'
    }


    /**
     * Prevent form submission when user press enter key in auto-complete
     */
    $("#agm_autocomplete").on('keydown', function (e) {
        if (e.keyCode == 13 || e.which == 13) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
    });
    /**
     * Show a message
     * Info window needs marker to enabled first
     */
    $("#agm_info_on").on('click', function () {
        if ($(this).is(":checked"))
            $(this).next('label').find('i:not(:visible)').fadeIn(0);
    });
    /**
     * Load color picker, but be fail safe
     */
    try {
        $('#agm_color_field').wpColorPicker();
    } catch (e) {
        console.error('WP Color Picker not loaded');
    }

    /**
     * Detect if touch enabled device
     * @source http://stackoverflow.com/questions/4817029/whats-the-best-way-to-detect-a-touch-screen-device-using-javascript
     * @returns {boolean|*}
     */
    function isTouchDevice() {
        return 'ontouchstart' in window        // works on most browsers
            || navigator.maxTouchPoints;       // works on IE10/11 and Surface
    }

})(window, document, jQuery);