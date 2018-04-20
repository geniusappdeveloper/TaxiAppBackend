<?php 
echo $this->Form->create('Company',array('controller' => 'users','url'=>'/login','method'=>'POST','onsubmit' => '',"class"=>"form-login")); ?>

		        <h2 class="form-login-heading">sign in now</h2>
		        <div class="login-wrap">
				
				<?php echo $this->Form->input("email",array("class"=>"form-control","label"=>false,'placeholder'=>"Email","div"=>false,'autofocus'=>true,'required'=>true));
               ?><br>
			   <?php echo $this->Form->input("password",array("class"=>"form-control","label"=>false,"div"=>false,'required'=>true,'type'=>'password','placeholder'=>"Password")); ?>		           
		            <label class="checkbox">
		                <span class="pull-right">
		                    <a data-toggle="modal" href="login.html#myModal"> Forgot Password?</a>
		
		                </span>
		            </label>
		            <button class="btn btn-theme btn-block"  type="submit"><i class="fa fa-lock"></i> SIGN IN</button>
		            <hr>
		              <label class="checkbox">
		                <span class="pull-right">
		                    <!--a data-toggle="modal" href="login.html#myModal2"> Register</a-->
		
		                </span>
		            </label>
		           
		
		        </div>
		<?php echo $this->Form->end(); ?>
		<?php echo $this->Form->create('Company',array('controller' => 'users','url'=>'/forgot_password','method'=>'POST','onsubmit' => '',"class"=>"form-login")); ?>
		          <!-- Modal -->
		          <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal" class="modal fade">
		              <div class="modal-dialog">
		                  <div class="modal-content">
		                      <div class="modal-header">
		                          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		                          <h4 class="modal-title">Forgot Password ?</h4>
		                      </div>
		                      <div class="modal-body">
		                          <p>Enter your e-mail address below to reset your password.</p>
		                          <input type="email" name="email" required=true placeholder="Email" autocomplete="off" class="form-control placeholder-no-fix">
		
		                      </div>
		                      <div class="modal-footer">
							   <?php
			echo $this->Form->submit('Submit',array('controller' => 'companies','url'=>'/forgot_password','class'=>'btn btn-default')); ?><br>
			<?php
			echo $this->Form->button('Cancel',array('type'=>'button','class'=>'btn btn-default','div'=>false,'onclick'=>"location.href='".BASE_URL."admin/users/login'")); 
			?>
							  
		                          
		                          <?php 
								  echo $this->Form->end();
								  ?>
		                      </div>
		                  </div>
		              </div>
		          </div>
		          <!-- modal -->
		
	 </form> 	

	 <?php echo $this->Form->create('Company',array('controller' => 'companies','url'=>'/register','method'=>'POST','onsubmit' => '',"class"=>"form-login",'enctype'=>'multipart/form-data','class'=>'form-horizontal style-form')); ?>


		           <!-- Modal -->
		          <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal2" class="modal fade">
		              <div class="modal-dialog">
		                  <div class="modal-content">
		                      <div class="modal-header">
		                          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		                          <h4 class="modal-title">Company Register</h4>
		                      </div><br>
	                 <?php echo $this->Form->input('name',array("class"=>"form-control",'placeholder'=>"Name","div"=>false,'autofocus'=>true,'required'=>true,'label'=>false,'style'=>"width:300px;margin-left:150px",'minlength' => 3,'maxlength' => 30));
                           ?><br>
		                      <?php echo $this->Form->input('email1',array("class"=>"form-control","label"=>"Email",'placeholder'=>"Email",'type'=>'email',"div"=>false,'autofocus'=>true,'style'=>"width:300px;margin-left:150px",'label'=>false,'required'=>true,'minlength' => 3,'maxlength' => 30));
                           ?><br>
							    <?php echo $this->Form->input('password1',array("class"=>"form-control","label"=>"Password",'placeholder'=>"Password",'type'=>'password',"div"=>false,'style'=>"width:300px;margin-left:150px",'label'=>false,'autofocus'=>true,'required'=>true));
                           ?><br>
							  <?php echo $this->Form->input('phone',array("class"=>"form-control","label"=>"Phone",'placeholder'=>"Phone",'type'=>'text',"div"=>false,'autofocus'=>true,'style'=>"width:300px;margin-left:150px",'label'=>false,'required'=>true));
                           ?><br>
							   <?php 
							   
							   //echo $this->Form->input('image',array("class"=>"form-control","label"=>"image",'placeholder'=>"Image",'type'=>'file',"div"=>false,'autofocus'=>true,'style'=>"width:300px;margin-left:150px",'label'=>false,'required'=>true));
							   // echo $this->Form->input('image',array('type'=>'file','label'=>'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Image','required'=>true));
                                   echo $this->Form->input('image',array("class"=>"form-control","label"=>"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Image",'type'=>'file','style'=>"width:300px;margin-left:150px",'required'=>true));


         
							   
                           ?><br>
						    <?php echo $this->Form->input('paypal_id',array("class"=>"form-control","label"=>"Paypal ID",'placeholder'=>"Paypal ID",'type'=>'text',"div"=>false,'autofocus'=>true,'style'=>"width:300px;margin-left:150px",'label'=>false,'required'=>true));
                           ?><br>
						   
						   <?php echo $this->Form->input('price_per_miles',array("class"=>"form-control","label"=>"Price Per Miles",'placeholder'=>"Price Per Miles",'type'=>'text',"div"=>false,'autofocus'=>true,'style'=>"width:300px;margin-left:150px",'label'=>false,'required'=>true));
                           ?><br>
						  
		                      <div class="modal-footer">
							  
							  <?php
			echo $this->Form->submit('Submit',array('controller' => 'companies','url'=>'/register','class'=>'btn btn-default')); ?><br>
			<?= $this->Form->button('Cancel',array('type'=>'button','class'=>'btn btn-default','div'=>false,'onclick'=>"location.href='".BASE_URL."admin/users/login'")); ?>     
		      </div>
		                  </div>
		              </div>
		          </div>
		          <!-- modal -->
			
	 </form>
	 <?php $this->end(); ?>