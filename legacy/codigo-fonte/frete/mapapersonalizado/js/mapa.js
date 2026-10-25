var map;
var idInfoBoxAberto;
var infoBox = [];
var markers = [];
var userAdress;

function initialize() {	
	var latlng = new google.maps.LatLng(-12.041395, -52.040106);
	
    var options = {
        zoom: 4,
		center: latlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
        
    };

    map = new google.maps.Map(document.getElementById("mapa"), options);
    
}
google.maps.event.addDomListener(window, 'load');
google.maps.event.addDomListener(window, "resize", function() {
var center = map.getCenter();
google.maps.event.trigger(map, "resize");
map.setCenter(center); 
});
initialize();

function abrirInfoBox(id, marker) {
	if (typeof(idInfoBoxAberto) == 'number' && typeof(infoBox[idInfoBoxAberto]) == 'object') {
		infoBox[idInfoBoxAberto].close();
	}

	infoBox[id].open(map, marker);
	idInfoBoxAberto = id;
}



function carregarPontos() {
	
	
	
		var request = $.ajax({
				 url: "./views/listarTransportadorMapa.php",
				type: "POST",			
				dataType: "json"
				
			
			});
			request.done(function(data) {
		
			var javascript_array = new Array();
		
			for($i=0; $i < data.length; $i++){	
					javascript_array[$i] = data[$i];
					//alert($i);	
				}
	var markers = [];
	var latlngbounds = new google.maps.LatLngBounds();				
					
	var geocoder = new google.maps.Geocoder(); 
				
	for (var i = 0; i < javascript_array.length; i++) {
      endereco = javascript_array[i];
      //alert(endereco);
 
      geocoder.geocode({ 'address': endereco + ', Brasil', 'region': 'BR' }, function (results, status) {
         if (status == google.maps.GeocoderStatus.OK) {
            if (results[0]) {
               var latitude = results[0].geometry.location.lat();
               var longitude = results[0].geometry.location.lng();
 				//alert(latitude + ' ' + longitude);

 				
               var marker = new google.maps.Marker({
                  position: new google.maps.LatLng(latitude, longitude),
                  map: map,
                  icon: 'MapaPersonalizado/img/marcador.png'
                  
                  
               });

			markers.push(marker);
			latlngbounds.extend(marker);
            }
         }
      })
      	
   }
   var markerCluster = new MarkerClusterer(map, markers);
	map.fitBounds(markerCluster);	
			});

	
}

carregarPontos();