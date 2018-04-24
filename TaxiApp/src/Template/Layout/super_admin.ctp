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
  
    <ul class="sidebar-menu" data-widget="tree">
      <!-- <li class="header">MAIN NAVIGATION</li> -->
       
	  <!-- Dashboard -->
	   <li class="">
			 <a href="/TaxiApp/users/dashboard"> <i class="fa fa-dashboard"></i><span> <span> DashBoard </span>
		</span></a>
		</li>
		
	  
	  <!-- User Mangements -->
	  <?php $class=''; $menu_open=''; if($title == 'Admin List'){ $menu_open = "menu-open"; $class= "active"; } ?>
	 <li class="treeview">
        <a >
          <i class="fa fa-th-list"></i> <span>Admins</span><span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu <?= $class; ?>">
          <li class ="<?= $class; ?>">  <?php echo $this->Html->link(
                                 ' Admin List ',
                                  ['controller' => 'users', 'action' => 'admin_list', '_full' => true]
)                                  ; ?> </li>
          
        </ul>
      </li>
	  
	   <!-- Categoy --> 
	  
	  <li class="treeview">
        <a href="#">
          <i class="fa fa-dashboard"></i> <span>Categories</span><span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li>  <?php echo $this->Html->link(
						 'Categories List ',
						  ['controller' => 'categories', 'action' => 'category_list', '_full' => true]
)                                  ;  ?> </li>
         
        </ul>
      </li>
	  
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
</body>


</html>
