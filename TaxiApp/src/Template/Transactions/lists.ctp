<?php   $this->extend('/Layout/main'); ?>

<?php $data = json_encode($totalrides); 
  $data = $totalrides; 
// print_r($data);
//$this->Flash->render(); 

	
	?>
	
		<div class="form-group">
                  <label for="inputPassword3" class="col-sm-2 control-label ">City Name*</label>

                  <div class="col-sm-10">
                   <select id="myselect" name="country_code" class="form-control form-control_1 col-md-4 p-l-0">
                    <option value="+93">Belize City</option>
                    <option value="+355">San Ignacio</option>
                    <option value="+213">Orange Walk</option>
                    <option value="+1-684">Belmopan</option>
                    <option value="+376">Dangriga</option>
                    <option value="+244">Corozal</option>
                    <option value="+1264">San Pedro Town</option>
                    <option value="+672">Benque Viejo el Carmen</option>
                    <option value="+1268">Punta Gorda </option>
                    <option value="+54">Placencia </option>
					</select>
                  </div>
                </div>
				
<!DOCTYPE html>
<html> 
<head> 
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" /> 
  <title>Google Maps Multiple Markers</title> 
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBeEd1XX_ch8gUBQnauEm8HKa-SCQt4OvY" 
          type="text/javascript"></script>
</head> 
<body>
  <div id="map" style="width: 500px; height: 400px;"></div>

  <script type="text/javascript">
   // var data = '<?php print_r($data); ?>';
   // console.log(data);
	// var data1 = JSON.parse(data);
	// console.log(data1);
	// var data2 = parseFloat(data1);
	// console.log(data2);
    var map = new google.maps.Map(document.getElementById('map'), {
      zoom: 15,
      center: new google.maps.LatLng(17.5046, -88.1962),
      mapTypeId: google.maps.MapTypeId.ROADMAP
    });

   var triangleCoords = 
   [{"lat":30.70555,"lng":76.45223},{"lat":30.7566,"lng":76.68656},{"lat":30.716556,"lng":76.6856456}];
console.log(triangleCoords);
        // Construct the polygon.
        var bermudaTriangle = new google.maps.Polygon({
          paths: triangleCoords,
          strokeColor: '#FF0000',
          strokeOpacity: 0.8,
          strokeWeight: 2,
          fillColor: '#FF0000',
          fillOpacity: 0.35
        });
        bermudaTriangle.setMap(map);
  </script>
</body>
</html>