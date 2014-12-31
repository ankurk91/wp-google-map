function $By_ID(a){
    return document.querySelector('#'+a)||document.getElementById(a);
}
function Load_agm_Map() {
    var center = new google.maps.LatLng(agm_opt.map_Lat,agm_opt.map_Lng);
    var opt = { overviewMapControl: true, center: center,streetViewControl: false, zoom: agm_opt.map_zoom, mapTypeId: google.maps.MapTypeId.ROADMAP};
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
    /* Auto-complete feature */
    var map_auto = new google.maps.places.Autocomplete($By_ID('agm_autocomplete'));
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

/* Prepare to load google map */
var agm_map = $By_ID("agm_map_canvas");
if (typeof google == "object") {
    google.maps.event.addDomListener(window, "load", Load_agm_Map)
}
else {
    agm_map.innerHTML = '<h4 style="text-align: center;color: #ba060b">Failed to load Google Map.<br>Refresh this page and try again.<br>Check your internet connection as well.</h4>'
}

jQuery(function ($) {
    /* Prevent form submission when user press enter key in auto-complete */
    $("#agm_autocomplete").keydown(function (e) {
        if (e.which == 13 ||e.which==13) {
            e.preventDefault();
            e.stopPropagation();
        }
    });
    $("#agm_info_on").click(function(){
        if($(this).is(":checked"))
      $(this).next('label').find('i:not(:visible)').fadeIn();
    });
    /*check if color picker is available*/
    if (agm_opt.color_picker) { $('#agm_color_field').wpColorPicker(); }
    /*save screen options*/
    $('#screen-options-wrap').find('div.agm_meta_box').find('input').change(function(){
        var params = $(this).parents('div.agm_meta_box').find('input').serialize();
        var results= $("#agm_meta_ajax_result");
        results.stop(true,true).fadeOut(0);
        $.ajax({
            url:agm_opt.ajax_url,
            data:params,
            success:function(d,s){
                if(d=='1'&&s=='success'){
                    results.html('&#10004; Settings has been saved, <a href="#" onclick="window.location.reload()">Reload</a> page to see changes.').fadeIn(function(){
                        results.delay('10000').fadeOut(0);
                    });
                }
            },error:function(a,b){
                var err='Error Saving Options';
                if (a.status === 0) {
                    err="Could not connect to server ! Try Again..";
                }else{
                  if (b === "timeout") {
                      err="Connection Timeout ! Try Again..";
                   }
                    else {
                      err="Unknown error.";
                   }
                }
                results.html('&#10008; '+err).show();
            }
    });
    });
});
