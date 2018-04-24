<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>TAXI APP</title>
  <!-- Tell the browser to be responsive to screen width -->

  <?php echo $this->Html->css(array('bower_components/bootstrap/dist/css/bootstrap.css' , 'bower_components/font-awesome/css/font-awesome.min.css' ,'bower_components/Ionicons/css/ionicons.min.css' , 'bower_components/jvectormap/jquery-jvectormap.css' , 'dist/css/AdminLTE.min.css' , 'dist/css/skins/_all-skins.min.css','home.css' ));?>

  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <?php echo $this->Html->css(array('bower_components/bootstrap/dist/css/bootstrap.css' , 'bower_components/font-awesome/css/font-awesome.min.css' ,'bower_components/Ionicons/css/ionicons.min.css' , 'bower_components/jvectormap/jquery-jvectormap.css' , 'dist/css/AdminLTE.min.css' , 'dist/css/skins/_all-skins.min.css' ));?>

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  
</head>
<body class="hold-transition skin-blue sidebar-mini">
  <div class="wrapper">

    <header class="main-header">

      <a href="" class="logo">
        <span class="logo-mini"><b>T</b>XI</span>

        <span class="logo-lg"><b>TAXI</b>APP</span>
      </a>
	<?php ?>
      <nav class="navbar navbar-static-top">
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
          <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">
          <ul class="nav navbar-nav">

          
    <li class="dropdown user user-menu">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown">
         <img src="<?php echo $AdminImage; ?>" class="user-image" alt="User Image"> 
        <span class="hidden-xs"><?= h($Admin); ?></span>
      </a>
      <ul class="dropdown-menu">
        
        <li class="user-header">
          <img src="<?php echo $AdminImage; ?>" class="img-circle" alt="User Image">

          <p>
           <?php echo $Admin ." " . $LastName ; ?>
            <small></small>
          </p>
        </li>
       
        <li class="user-footer">
          
		   <div class="pull-right">
           
			<?php echo $this->Html->link("Change Password",
							array('controller'=>'users','action'=>'changepassword',$AdminID),
							array('class'=>'btn btn-default btn-flat','title'=>'Profile')
						);    ?>
          </div>
		 
		  <div class="pull-left">
           
			<?php echo $this->Html->link("Profile",
							array('controller'=>'users','action'=>'profile',$AdminID),
							array('class'=>'btn btn-default btn-flat','title'=>'Profile')
						);    ?>
          </div>
          <div class="pull-right">
			<?php echo $this->Html->link("Sign out",
							array('controller'=>'users','action'=>'logout',$AdminID),
							array('class'=>'btn btn-default btn-flat','title'=>'Log Out')
						);    ?>
          </div>
        </li>
      </ul>
    </li>
   
    <!-- <li>
      <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
    </li> -->
  </ul>
</div>
</nav>
</header>

<aside class="main-sidebar">
  <section class="sidebar">
    
    <div class="user-panel">
      <div class="pull-left image">
         <img src="<?php echo $AdminImage; ?>" class="img-circle" alt="Admin Image">
      </div>
      <div class="pull-left info">
        <p><?php echo $Admin; ?></p>
        <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
      </div>
    </div>
    
    <!-- <form action="#" method="get" class="sidebar-form">
      <div class="input-group">
        <input type="text" name="q" class="form-control" placeholder="Search...">
        <span class="input-group-btn">
          <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
          </button>
        </span>
      </div>
    </form> -->
    <ul class="sidebar-menu" data-widget="tree">
      <!-- <li class="header">MAIN NAVIGATION</li> -->
       
	  <!-- Dashboard -->
	   <li class="">
			 <a href="/TaxiApp/users/dashboard"> <i class="fa fa-dashboard"></i><span> <span> DashBoard </span>
		</span></a>
		</li>
		
		
		
        <!--a href="/dashboard">
          <i class="fa fa-dashboard"></i> <span> DashBoard </span><span class="pull-right-container">
          </span>
        </a-->
      <!--  <ul class="treeview-menu">
          <li class="active"><a href="index.html"><i class="fa fa-circle-o"></i> </a></li>
          <li><a href="index2.html"><i class="fa fa-circle-o"></i> </a></li>
        </ul> -->
      
	  
	  <!-- User Mangements -->
	  <?php $class=''; $menu_open=''; if($title == 'Passenger List'){ $menu_open = "menu-open"; $class= "active"; } ?>
	 <li class="treeview">
        <a >
          <i class="fa fa-th-list"></i> <span>Passenger</span><span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu <?= $class; ?>">
          <li class ="<?= $class; ?>">  <?php echo $this->Html->link(
                                 ' Passenger List ',
                                  ['controller' => 'users', 'action' => 'users_list', '_full' => true]
)                                  ; ?> </li>
          
        </ul>
      </li>
	  
	    <!-- Driver Mangements -->
	  <?php $class=''; $menu_open=''; if($title == 'Driver List'){ $menu_open = "menu-open"; $class= "active"; } ?>
	 <li class="treeview">
        <a >
          <i class="fa fa-th-list"></i> <span>Driver</span><span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu <?= $class; ?>">
          
          <li class = "<?= $class; ?>"> 
		   <?php echo $this->Html->link(
                                 ' Driver List ',
                                  ['controller' => 'users', 'action' => 'driver_list', '_full' => true]
)                                  ; ?></li>
        </ul>
      </li>
	  
	  
	  <!-- Categoy --> 
	  
	  <!--li class="treeview">
        <a href="#">
          <i class="fa fa-dashboard"></i> <span>Categories</span><span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li>  <?php echo $this->Html->link(
						 'Categories List ',
						  ['controller' => 'categories', 'action' => 'category_list', '_full' => true]
)                                  ; ?> </li>
         
        </ul>
      </li-->
	  
	  <!-- Fare Mangement -->
	  
	 <!-- <li class="treeview">
        <a href="#">
          <i class="fa fa-dashboard"></i> <span>Fare Mangement </span><span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
           <li>  <?php /*echo $this->Html->link(
						 'Fares List ',
						  ['controller' => 'fares', 'action' => 'fare_list', '_full' => true]
					); */ ?> </li>
        </ul>
      </li> -->
	  
	  
	  <!-- Transaction -->
	  
	  <li class="treeview">
        <a href="#">
          <i class="fa fa-dashboard"></i> <span>Transaction</span><span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li><?php echo $this->Html->link(
						 'Transaction List ',
						  ['controller' => 'transactions', 'action' => 'transactionlist', '_full' => true]
					); ?></li>
        
        </ul>
      </li>
  <li class="treeview">
        <a href="#">
          <i class="fa fa-dashboard"></i> <span>Geofancing</span><span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li><?php echo $this->Html->link(
						 'Geofancing ',
						  ['controller' => 'geofancings', 'action' => 'lists', '_full' => true]
					); ?></li>
        
        </ul>
      </li>


    </aside>

    <div class="content-wrapper">
      <section class="content-header">
        <?= $this->fetch('content') ?>
      </section>
    </div>
    <footer class="main-footer">
      <div class="pull-right hidden-xs">
        <b>Version</b> 2.4.0
      </div>
      <strong>Copyright &copy; 2017-2019 <a href="https://adminlte.io">Taxi App</a>.</strong> All rights
      reserved.
    </footer>

    <div class="control-sidebar-bg"></div>
  </div>

  <!-- jQuery 3 -->
  <?php echo $this->Html->script(array('/css/bower_components/jquery/dist/jquery.min.js' ,'/css/bower_components/bootstrap/dist/js/bootstrap.min.js', '/css/bower_components/fastclick/lib/fastclick.js', '/css/dist/js/adminlte.min.js','/css/bower_components/jquery-sparkline/dist/jquery.sparkline.min.js','/css/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js','/css/plugins/jvectormap/jquery-jvectormap-world-mill-en.js',
  '/css/bower_components/jquery-slimscroll/jquery.slimscroll.min.js','/css/bower_components/chart.js/Chart.js','/css/dist/js/pages/dashboard2.js', '/css/dist/js/demo.js'));?>
<script type="text/javascript">


      var map;
      var markers = [];

      function initMap() {
        var haightAshbury = {lat: 17.5046, lng: -88.1962};

        map = new google.maps.Map(document.getElementById('map'), {
          zoom: 12,
          center: haightAshbury,
          mapTypeId: 'terrain'
        });

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
					     var x = document.createElement('li');
						 
						 var t = document.createTextNode( results[1].formatted_address + ' lat:' + event.latLng.lat() + ' , longitude: ' + event.latLng.lng());
						 x.appendChild(t);
						 document.getElementById('locations').appendChild(x);
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
    var map = new google.maps.Map(document.getElementById('map'), {
      zoom: 15,
      center: new google.maps.LatLng(17.5046, -88.1962),
      mapTypeId: google.maps.MapTypeId.ROADMAP
    });

   
  </script>
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBeEd1XX_ch8gUBQnauEm8HKa-SCQt4OvY&callback=initMap" 
          async defer></script>
</body>


</html>
