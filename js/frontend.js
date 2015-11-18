(function (window, document, google) {
    'use strict';

    /**
     * If options not found then return early
     */
    if (typeof window._agm_opt === 'undefined') {
        return;
    }
    var opt = window._agm_opt;


    function _loadGoogleMap() {
        var width = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
        var center = new google.maps.LatLng(parseFloat(opt.map.lat), parseFloat(opt.map.lng));

        var map_options = {
            panControl: !opt.controls.panControl,
            zoomControl: !opt.controls.zoomControl,
            mapTypeControl: !opt.controls.mapTypeControl,
            streetViewControl: !opt.controls.streetViewControl,
            overviewMapControl: !opt.controls.overviewMapControl,
            scrollwheel: !opt.mobile.scrollwheel,
            draggable: true,
            center: center,
            zoom: parseInt(opt.map.zoom),
            mapTypeId: google.maps.MapTypeId[opt.map.type],
            mapTypeControlOptions: {
                style: google.maps.MapTypeControlStyle.DROPDOWN_MENU,
                position: google.maps.ControlPosition.TOP_RIGHT
            },
            zoomControlOptions: {
                position: google.maps.ControlPosition.LEFT_CENTER
            }
        };
        var map = new google.maps.Map(map_canvas_div, map_options);

        if (opt.mobile.draggable) {
            map.setOptions({draggable: (width > 480)});
        }


        /**
         * If marker is enabled
         */
        if (opt.marker.enabled === 1) {
            var marker = new google.maps.Marker({
                position: center,
                map: map,
                title: opt.marker.title
            });
            if (opt.marker.animation !== 'NONE') {
                marker.setAnimation(google.maps.Animation[opt.marker.animation])
            }
            if (opt.marker.color !== false) {
                marker.setIcon(opt.marker.color);
            }

            /**
             * Info window needs marker to be enabled first
             */
            if (opt.info_window.enabled === 1) {
                var infoWindow = new google.maps.InfoWindow({content: opt.info_window.text});
                /**
                 * Clicking on map will close info-window
                 */
                google.maps.event.addListener(map, 'click', function () {
                    infoWindow.close();
                });
            }
        }

        if (opt.marker.enabled === 1 && opt.info_window.enabled === 1) {
            /**
             * Clicking on marker will show info-window
             */
            google.maps.event.addListener(marker, "click", function () {
                infoWindow.open(map, marker);
                marker.setAnimation(null);
            });
            /**
             * If info window enabled by default
             */
            if (opt.info_window.state === 1) {
                window.setTimeout(function () {
                    infoWindow.open(map, marker);
                    marker.setAnimation(null);
                }, 2000);
            }

        }


        var timeout;
        /**
         * Resize event handling, make map more responsive
         * Center map after 300 ms
         */
        google.maps.event.addDomListener(window, 'resize', function () {
            if (timeout) {
                clearTimeout(timeout);
            }
            timeout = window.setTimeout(function () {
                map.setCenter(center);
            }, 300);
        });
    }


    var map_canvas_div = document.getElementById("agm_map_canvas");
    if (typeof map_canvas_div !== 'undefined') {
        if (typeof google == "object" && google.maps) {
            google.maps.event.addDomListener(window, "load", _loadGoogleMap)
        }
        else {
            map_canvas_div.innerHTML = '<p style="text-align: center">Failed to load Google Map.<br>Please try again.</p>';
            map_canvas_div.style.height = "auto";
        }
    }


})(window, document, google);