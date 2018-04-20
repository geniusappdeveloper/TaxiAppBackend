<!DOCTYPE html>
<html lang="en">
<head>
  <?php echo $this->Html->meta('icon'); ?>
  <?= $this->assign('title', $title); ?>
  <title><?= $this->fetch('title') ?></title>
  <!--   <meta charset="utf-8">
  <title> h($this->fetch('title'))</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="Dashboard">
    <meta name="keyword" content="Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina"> -->
	<script>
 var BASEURL = '<?php echo ""; ?>';
  </script>
 <?php
 echo $this->Html->css(array('style.css','bootstrap.css','/font-awesome/css/font-awesome.css','style-responsive.css'));
 echo $this->Html->script(array('jquery.js','common/functions.js'));
 ?>
 <?=  $this->fetch('css') ?>
 <?= $this->fetch('script') ?>
  </head>
  <body>
	  <div id="login-page">
	  	<div class="container">
      <?= $this->Flash->render() ?>
      <!-- Here's where I want my views to be displayed -->
      <?= $this->fetch('content') ?>
          
	  	</div>
	  </div>
  </body>
  <?php 
	echo $this->Html->script(array('bootstrap.min.js','jquery.backstretch.min.js')); ?>
	  
    <script>
        $.backstretch("<?php echo $this->request->webroot;?>img/car.jpg", {speed: 200});
    </script>
</html>
