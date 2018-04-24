<?php   $this->extend('/Layout/main'); ?>

<div id="flash" class="message-info success">

    <?php $data = json_encode($totalrides);//$this->Flash->render(); 
	//print_r($totalrides['onlinedriver']['latitude']);
	?>
	
</div>

 <section class="content-header">
      <h1>
        Dashboard
        <small>Control panel</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Dashboard</li>
      </ol>
    </section>
	<?php  ?>
  <div class="row">
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-aqua">
            <div class="inner">
              <h3><?php echo $totalrides['totalrides']; ?></h3>

              
			  <p>Total Rides </p> 
			  
            </div>
            <div class="icon">
              <i class="fa fa-car"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
		<div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-blue">
            <div class="inner">
              <h3><?php echo $totalrides['ridestoday1']; ?></h3>

              
			  <p>Total Rides Today</p>
			  
            </div>
            <div class="icon">
              <i class="fa fa-car"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
		
		<div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-aqua">
            <div class="inner">
              <h3><?php echo $totalrides['activeride']; ?></h3>

              
			   <p>Active Rides </p> 
			  
            </div>
            <div class="icon">
              <i class="ion ion-android-car"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
		
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-green">
            <div class="inner">
              <h3><?php echo $totalrides['driver']; ?><sup style="font-size: 20px"></sup></h3>

              <p>Drivers</p>
            </div>
            <div class="icon">
              <i class="fa fa-taxi"></i>
            </div>
            <a href="driver-list" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-yellow">
            <div class="inner">
              <h3><?php echo $totalrides['rider']; ?></h3>

              <p>Riders</p>
            </div>
            <div class="icon">
              <i class="fa fa-user"></i>
            </div>
            <a href="users-list" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-blue">
            <div class="inner">
              <h3><?php echo $totalrides['approvedriver']; ?></h3>

              <p>Driver For Approved</p>
            </div>
            <div class="icon">
              <i class="fa fa-car"></i>
            </div>
            <a href="driver-list" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
      </div>
	  
	  <!DOCTYPE html>
<html> 
<head> 
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" /> 
  <title>Google Maps Multiple Markers</title> 
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBeEd1XX_ch8gUBQnauEm8HKa-SCQt4OvY&callback=myMap" 
          type="text/javascript"></script>
</head> 
<body>
  <div id="map" style="width: 500px; height: 400px;"></div>

  <script type="text/javascript">
  
  var data = '<?php print_r($data); ?>';
	console.log('hello');
	var data1 = JSON.parse(data);
	var data2 = Object.values(data1);
	var online = data2[6];
	var offline = data2[7];
	
	var online1 = Object.values(online);
	console.log(online1);
	console.log(offline);
    var locations = [
      ['', 17.5078, -88.1958, 2],
      ['', 17.5046, -88.1962, 1],
      
    ];

    var map = new google.maps.Map(document.getElementById('map'), {
      zoom: 15,
      center: new google.maps.LatLng(17.5046, -88.1962),
      mapTypeId: google.maps.MapTypeId.ROADMAP
    });

    var infowindow = new google.maps.InfoWindow();

    var marker, i;
	

    for (i = 0; i < locations.length; i++) {  
      marker = new google.maps.Marker({
        position: new google.maps.LatLng(locations[i][1], locations[i][2]),
        map: map
      });

      google.maps.event.addListener(marker, 'click', (function(marker, i) {
        return function() {
          infowindow.setContent(locations[i][0]);
          infowindow.open(map, marker);
        }
      })(marker, i));
    }
  </script>
</body>
</html>
	<?php  ?>