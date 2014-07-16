function map_init() {
  //finding variables
  jQuery('.widget_google_maps_quick_widget').each(function(){
    var el = jQuery(this);
    var lat, lon, msg, cnt, cnt_id, title;
    cnt = el.find('.gmap_area');
    lat = parseFloat(el.find('.gmap_data .latitude').text());
    lon = parseFloat(el.find('.gmap_data .longitude').text());
    msg = el.find('.gmap_data .msg').html();
    title = el.find('.gmap_data .title').html();
    //getting container id to convert cnt into a regular html object
    cnt_id = cnt.attr('id');
    cnt = document.getElementById(cnt_id);
    //preparing map
    var myLatlng = new google.maps.LatLng(lat, lon);
    var mapOptions = {
      center: myLatlng,
      zoom: 10,
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      mapTypeControl: false,
      scrollwheel: false
      //navigationControl: false,
      //scaleControl: false,
      //draggable: false,
    };
    var map = new google.maps.Map(cnt, mapOptions);
    var marker = new google.maps.Marker({
      position: myLatlng,
      animation: google.maps.Animation.DROP,
      title: title
    });
    var contentString = msg;
    var infowindow = new google.maps.InfoWindow({
      content: contentString
    });
    google.maps.event.addListener(marker, "click", function(){infowindow.open(map,marker)});
    setTimeout(function(){marker.setMap(map);},1800);
    setTimeout(function(){infowindow.open(map,marker);},2500);
  });
}
/**
 * Creating google map script reference dinamically and only once 
 * @param {Object} apiKey - the Google maps API key
 */
function loadGMapLib(apiKey) {
  if(jQuery('#google-maps-quick-widget').length > 0){
    map_init();
  } else {
    var script = document.createElement("script");
    script.id = "google-maps-quick-widget";
    script.type = "text/javascript";
    script.src = "http://maps.googleapis.com/maps/api/js?key="+apiKey+"&sensor=true&callback=map_init";
    document.body.appendChild(script);
  }
}
//Parsing every widget instance
jQuery(window).load(function(){
  jQuery('.widget_google_maps_quick_widget').each(function(){
    var el = jQuery(this);
    var api = el.find('.gmap_data .api').text();
    loadGMapLib(api);
  });
});