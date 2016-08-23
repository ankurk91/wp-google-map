(function (window, document, $) {
    'use strict';

    //Get requested tab from url
    var requestedTab = window.location.hash.replace('#top#', '');

    /**
     * Cache DOM elements for later use
     */
    var $tabs = $('h2#wpt-tabs'),
        $input = $("form#agm-form").find('input:hidden[name="_wp_http_referer"]'),
        $sections = $('section.tab-content');

    //If there no active tab found , set first tab as active
    if (requestedTab === '' || $('#' + requestedTab).length == 0) requestedTab = $sections.attr('id');
    //Notice: we are not using cached DOM in next line
    $('#' + requestedTab).addClass('active');
    $('#' + requestedTab + '-tab').addClass('nav-tab-active');
    //Set return tab on page load
    setRedirectURL(requestedTab);

    //Bind a click event to all tabs
    $tabs.find('a.nav-tab').on('click.agm', (function (e) {
        e.stopPropagation();
        //Hide all tabs
        $tabs.find('a.nav-tab').removeClass('nav-tab-active');
        $sections.removeClass('active');
        //Activate only clicked tab
        var id = $(this).attr('id').replace('-tab', '');
        $('#' + id).addClass('active');
        $(this).addClass('nav-tab-active');
        //Set return tab url
        setRedirectURL(id);
    }));

    /**
     * Set redirect url into form's input:hidden
     * Note: Using hardcoded plugin option page slug
     * @param url String
     */
    function setRedirectURL(url) {
        var split = $input.val().split('?', 1);
        //Update the tab id in last while keeping base url same
        $input.val(split[0] + '?page=agm_settings#top#' + url);
    }


    // Google Map related stuff start
    var opt = window._agmOpt, map;

    function getById(a) {
        return document.querySelector('#' + a) || document.getElementById(a);
    }

    function loadGoogleMap() {
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
        map = new google.maps.Map(mapCanvas, mapOptions);

        var agmLat = $('#agm-lat'),
            agmLng = $('#agm-lng'),
            agmZoom = $('#agm-zoom'),
            agmZoomVal = $('#agm-zoom-val');

        var marker = new google.maps.Marker({
            draggable: true,
            position: center,
            map: map,
            title: 'Current Location'
        });

        google.maps.event.addListener(map, 'rightclick', function (event) {
            agmLat.val(event.latLng.lat());
            agmLng.val(event.latLng.lng());
            marker.setTitle('Selected Location');
            marker.setPosition(event.latLng);
        });

        google.maps.event.addListener(marker, 'dragend', function (event) {
            agmLat.val(event.latLng.lat());
            agmLng.val(event.latLng.lng());
        });

        google.maps.event.addListener(map, 'zoom_changed', function () {
            agmZoom.val(map.getZoom());
            agmZoomVal.html(map.getZoom());
        });

        google.maps.event.addListener(map, 'center_changed', function () {
            var location = map.getCenter();
            agmLat.val(location.lat());
            agmLng.val(location.lng());
        });

        google.maps.event.addListener(map, "idle", function () {
            google.maps.event.trigger(map, 'resize');
        });

        /* Zoom slider control*/
        agmZoom.on('input click', function () {
            agmZoomVal.html(this.value);
            map.setZoom(parseInt(agmZoom.val()));
        });

        /* Auto-complete feature */
        var locSearch = new google.maps.places.Autocomplete(getById('agm-search'));
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
    var mapCanvas = getById("agm-canvas");
    if (typeof google == "object" && google.maps) {
        google.maps.event.addDomListener(window, "load", loadGoogleMap)
    }
    else {
        mapCanvas.innerHTML = '<h4 style="text-align: center;color: #ba060b">Failed to load Google Map.<br>Refresh this page and try again.<br>Check your internet connection as well.</h4>'
    }

    //Workaround to fix Map not loaded properly when canvas is hidden initially
    $('#wpt-loc-tab').on('click.agm', function () {
        try {
            google.maps.event.trigger(map, 'resize');
        } catch (e) {
            console.error('Google map not loaded yet');
        }

    });

    /**
     * Prevent form submission when user press enter key in auto-complete
     */
    $("#agm-search").on('keydown', function (e) {
        if (e.keyCode == 13 || e.which == 13) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
    });

    /**
     * Load color picker, but be fail safe
     */
    try {
        $('#agm-border-color').wpColorPicker();
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