 <!DOCTYPE html>
<html lang="en">
  <head>
  <title><?= h($this->fetch('title')) ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="Dashboard">
    <meta name="keyword" content="Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">
	<!--script>
	 var BASE_URL = '<?php echo BASE_URL; ?>';
	</script-->
 <?php
echo $this->fetch('meta');
echo $this->fetch('css');
echo $this->fetch('script');
  
 ?>
   
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
</html>
