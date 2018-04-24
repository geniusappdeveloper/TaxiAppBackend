<?php  ($UserType =='S')? $this->extend('/Layout/super_admin'):$this->extend('/Layout/main'); ?>

<div id="flash" class="message-info success">

    <?php $data = json_encode($totalrides);//$this->Flash->render(); 
	//print_r($data);
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
	<?php if($UserType =='A'){ ?>
	
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
		<div class="col-lg-3 col-xs-6"></div>
			<div class="col-lg-3 col-xs-6"></div>
        <!-- ./col -->
		<div id="map"style="width: 51%; height: 300px; float: left;">   </div>
      </div>
	   
	<?php } ?>
	

    
	 
    <script>

      function initMap() {
         var data = '<?php print_r($data); ?>';
	var data1 = JSON.parse(data);
	var online = data1.onlinedriver;
	var offline = data1.offlinedriver;

    var map = new google.maps.Map(document.getElementById('map'), {
      zoom: 15,
      center: new google.maps.LatLng(17.5046, -88.1962),
      mapTypeId: google.maps.MapTypeId.ROADMAP
    });
 var iconURLPrefix = 'http://maps.google.com/mapfiles/ms/icons/';

    var icons = [
      iconURLPrefix + 'red-dot.png',
      iconURLPrefix + 'green-dot.png',
      iconURLPrefix + 'blue-dot.png',
      iconURLPrefix + 'orange-dot.png',
      iconURLPrefix + 'purple-dot.png',
      iconURLPrefix + 'pink-dot.png',      
      iconURLPrefix + 'yellow-dot.png'
    ]
	console.log(icons[2]);
    var infowindow = new google.maps.InfoWindow();

    var marker;

    for (var i = 0; i < online.length; i++) {
    // console.log(online);		
      marker = new google.maps.Marker({
        position: new google.maps.LatLng(online[i].latitude,online[i].longitude),
        map: map
      });
       
      google.maps.event.addListener(marker, 'click', (function(marker, i) {
        return function() {
          infowindow.setContent(online[i]);
          infowindow.open(map, marker);
        }
      })(marker, i));
    }
	
	 for (var i = 0; i < offline.length; i++) {
  //   console.log(offline);		
      marker = new google.maps.Marker({
        position: new google.maps.LatLng(offline[i].latitude,offline[i].longitude),
        map: map,
	    icon: icons[2]
      });
       
      google.maps.event.addListener(marker, 'click', (function(marker, i) {
        return function() {
          infowindow.setContent(offline[i]);
          infowindow.open(map, marker);
        }
      })(marker, i));
    }
      }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBeEd1XX_ch8gUBQnauEm8HKa-SCQt4OvY&callback=initMap" 
          async defer>
		  </script>
  