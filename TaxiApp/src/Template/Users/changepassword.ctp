<?php  ($UserType =='S')? $this->extend('/Layout/super_admin'):$this->extend('/Layout/main'); ?>
 <?php // print_r($user); ?>

	
			<?php echo $this->Form->create($user,array('url'=>['action' => 'changepassword/',$id],'method'=>'POST',"class"=>"login",'enctype' => 'multipart/form-data')); ?>
	
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Change Password</h3>
            </div>
			
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example1" class="table table-bordered table-striped">
				<tr>
					<th valign="top">Old Password:  <span class="star">*</span></th>
					<td>
						<?php echo $this->Form->input("oldpassword",array("type"=>"password", "class"=>"inp-form","label"=>false,"div"=>false));	 ?>	
					</td>
				</tr>
				<tr>
					<th valign="top">New Password:  <span class="star">*</span></th>
					<td>
						<?php echo $this->Form->input("password",array("type"=>"password","class"=>"inp-form","label"=>false,"div"=>false));	 ?>
					</td>
				</tr>	
				<tr>
					<th valign="top">Confirm Password:  <span class="star">*</span></th>
					<td>
						<?php echo $this->Form->input("rpassword",array("type"=>"password","class"=>"inp-form","label"=>false,"div"=>false));	 ?>
					</td>
				</tr>		
				<tr>
				<th>&nbsp;</th>
				<td valign="top">
				<?php  
				echo $this->Form->hidden("id");
				echo $this->Form->submit('Save',array('class'=>"form-submit",'div'=>false))."&nbsp;&nbsp;&nbsp;"; 
				//echo $this->Form->button('Cancel',array('type'=>'button','class'=>"form-reset",'div'=>false,'onclick'=>"location.href='".BASE_URL."admin/events/list'")); 
				?>
				</td>
				<td></td>
			</tr>	
				 </table>
				</div>
	<?php
	
	echo $this->Form->end(); ?>

    
