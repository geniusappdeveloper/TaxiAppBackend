<?php   $this->extend('/Layout/main'); ?>

<?php
$data = array(); 
$data4 = array(); 
foreach($totalrides as $rides){
	$data[] =array('latitude' => $rides['latitude'],'longitude' => $rides['longitude']); 
	//$data4[] =array('latitude' => $rides['latitude'],'longitude' => $rides['longitude'],'address1'=>$rides['address']); 
}		
	?>
	
	
				
<!DOCTYPE html>
<html> 
<head> 

  <meta http-equiv="content-type" content="text/html; charset=UTF-8" /> 
  <title>Google Maps Multiple Markers</title> 
 
		  
</head> 
<body>

<div id="floating-panel">
      <input onclick="clearMarkers();" type=button value="Hide Markers">
      <input onclick="showMarkers();" type=button value="Show All Markers">
      <input onclick="deleteMK();" type=button value="Delete Markers">
    </div>
  <div id="map" style="width: 50%; height: 400px; float: left;"></div>
  
  <div style="padding-left: 20px;width: 45%; height: auto; float: left;">
  <ul class="list-group" id="locations">
  <?php foreach($totalrides as $d){ //print_r($d); ?>
  
	<li class="list-group-item">  Address: <?= $d['address']; ?> latitude: <?= $d['latitude'];  ?> longitude: <?= $d['longitude'];  ?>  <?php echo $this->Html->link("Delete",
							array('controller'=>'geofancings','action'=>'delete',$d['id']),
							array('title'=>'Delete')
						);  ?></li>	
	 
  <?php } ?>
  <li>  </li>
  </ul>
  </div>
</body>
<script type="text/javascript">
 var data2 = '<?php print_r(json_encode($data)); ?>';
	var data3 = JSON.parse(data2);
//console.log(data3);
      var map;
     var markers = []; 

	
      function initMap() {
        var haightAshbury;
		 
		var Resultdata =[];
        map = new google.maps.Map(document.getElementById('map'), {
          zoom: 15,
          center: new google.maps.LatLng(17.5046, -88.1962),
          mapTypeId: 'terrain'
        });

		for (var i = 0; i < data3.length; i++) {
     		
      marker = new google.maps.Marker({
        position: new google.maps.LatLng(data3[i].latitude,data3[i].longitude),
        map: map
      });
     // console.log(data3); 
	  
      google.maps.event.addListener(marker, 'click', (function(marker, i) {
        return function() {
          infowindow.setContent(data3[i]);
          infowindow.open(map, marker);
        }
      })(marker, i));
	  
	  var circle = new google.maps.Circle({
  map: map,
  radius: 200,    // metres
  fillColor: '#AA0000'
});
circle.bindTo('center', marker, 'position');

    }
        // This event listener will call addMarker() when the map is clicked.
           map.addListener('click', function(event) {
           addMarker(event.latLng);
		 // var l = (this).position.lat;
		 // alert(l);
		   var geocoder = geocoder = new google.maps.Geocoder();
            geocoder.geocode({ 'latLng': event.latLng }, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    if (results[1]) {
					   // console.log(results[1].formatted_address);
                       // alert("Location: " + results[1].formatted_address);
					    
						// Resultdata.push({'address':results[1].formatted_address,'latitude':event.latLng.lat(),'longitude':event.latLng.lng()});
						 $.ajax({
						  type: "POST",
						  url: "http://18.218.130.74/TaxiApp/geofancings/save_data",
						  data: { data: {'address':results[1].formatted_address,'latitude':event.latLng.lat(),'longitude':event.latLng.lng()}},
						  success: function(data){
						 var d = JSON.parse(data);
					     var x = document.createElement('li');
						 x.setAttribute('class', 'list-group-item');
						 var t = document.createTextNode(results[1].formatted_address + ' ,  latitude :' + event.latLng.lat() + ' , longitude : ' + event.latLng.lng());
						 x.appendChild(t);
						 var a = document.createElement('a');
						 console.log(d.id);
						 a.setAttribute('href' , '/TaxiApp/geofancings/delete/'+ d.id);
						 a.innerHTML = 'Delete';
						 x.appendChild(a);
						 document.getElementById('locations').appendChild(x);
							console.log(d);
						 // alert('success');
						  }
						});
                    }
					
                }
            });
        });
        
        // Adds a marker at the center of the map.
        addMarker(haightAshbury);
      }

      // Adds a marker to the map and push to the array.
      function addMarker(location) {
        var marker = new google.maps.Marker({
          position: location,
          map: map
        });
			  var circle = new google.maps.Circle({
					map: map,
					radius: 200,    // metres
					fillColor: '#AA0000'
				});
		circle.bindTo('center', marker, 'position');
        markers.push(marker);
		console.log(marker);
      }

      // Sets the map on all markers in the array.
      function setMapOnAll(map) {
        for (var i = 0; i < markers.length; i++) {
          markers[i].setMap(map);
        }
      }

      // Removes the markers from the map, but keeps them in the array.
      function clearMarkers() {
        setMapOnAll(null);
      }

      // Shows any markers currently in the array.
      function showMarkers() {
        setMapOnAll(map);
      }

      // Deletes all markers in the array by removing references to them.
      function deleteMarkers() {
        clearMarkers();
        markers = [];
      }
  function bindInfoWindow(marker, map, infoWindow, html, deleta) {

  google.maps.event.addListener(marker, 'click', function() {
    infoWindow.setContent(html);
    infoWindow.open(map, marker);


  });
   google.maps.event.addListener(marker, 'rightclick', function() 
       {    
           marker.setVisible(false) 
              alert(deleta)
              deleteMK(deleta)
    });


}

function deleteMK(deleta)
    {
        alert("vai");
         var url2 = "http://18.218.130.74/TaxiApp/geofancings/save_data";   
    downloadUrl2(url2, function(data3, responseCode) 

    {
        if (responseText == 200 && data3.length <= 1) 
        {
            document.getElementById("message").innerHTML = "Deletado";
            window.location.reload()
        }
    });     
    }

    var string2 = JSON.stringify(data3)
//Função ajax que salva os marcadores no mapa
function downloadUrl2(url2, callback) 
{
    var request2 = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    request2.open('POST',url2);
    request2.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
    request2.send('command=delete&marker='+string2)
    request2.onreadystatechange = function()
    {
        if(request2.readyState==4) 
        {   
            //infowindow.close();
            alert('Deletado')

        }
    }
}

   
  </script>

</html>