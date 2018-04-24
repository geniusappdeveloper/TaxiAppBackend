<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Taxi App | Log In</title>

  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <?php echo $this->Html->css('bower_components/bootstrap/dist/css/bootstrap.min.css');?>
  <?php echo $this->Html->css('bower_components/font-awesome/css/font-awesome.min.css');?> 
  <?php echo $this->Html->css('bower_components/Ionicons/css/ionicons.min.css');?>
  <?php echo $this->Html->css('dist/css/AdminLTE.min.css');?> 
  <?php echo $this->Html->css('plugins/iCheck/square/blue.css');?>

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

</head>
<body class="hold-transition login-page">
  <div class="login-box">
    <div class="login-logo">
      <a href="main.ctp"><b>Taxi</b>App</a>
    </div>

    <div class="login-box-body">
      <p class="login-box-msg">Sign in to start your session</p>
	  <div id="flash" class="message-info success">
			<?= $this->Flash->render(); ?>
		</div>
      <form action="" method="post">
        <div class="form-group has-feedback">
          <input type="email" name="email" class="form-control" placeholder="Email">
          <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback">
          <input type="password" name="password" class="form-control" placeholder="Password">
          <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        </div>
        <div class="row">
          <div class="col-xs-8">
           
          </div>

          <div class="col-xs-4 social-auth-links text-center">
            <button type="submit" class="btn btn-primary btn-block btn-flat text-center">Sign In</button>
          </div>

        </div>
      </form>

      <!--div class="social-auth-links text-center">
        <p>- OR -</p>
        <a href="#" class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-facebook"></i> Sign in using
        Facebook</a>
        <a href="#" class="btn btn-block btn-social btn-google btn-flat"><i class="fa fa-google-plus"></i> Sign in using
        Google+</a>
      </div-->

      

    </div>
  </div>

  <?php echo $this->Html->script('/css/bower_components/jquery/dist/jquery.min.js');?>
  <?php echo $this->Html->script('/css/bower_components/bootstrap/dist/js/bootstrap.min.js');?> 
  <?php echo $this->Html->script('/css/plugins/iCheck/icheck.min.js');?> 
  <?php echo $this->Html->script(array('jquery.backstretch.min.js')); ?>
	  
    <script>
        $.backstretch("<?php echo $this->request->webroot;?>img/car.jpg", {speed: 200});
    </script>
  <script>
    $(function () {
      $('input').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%' /* optional */
      });
    });
  </script>
</body>
</html>
