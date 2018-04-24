<?php  ($UserType =='S')? $this->extend('/Layout/super_admin'):$this->extend('/Layout/main'); ?>
 <?php //print_r($users); ?>
<div id="flash" class="message-info success">

    <?= $this->Flash->render(); ?>
</div>
	
			<?php echo $this->Form->create('User',array('url'=>['action' => 'add_admin'],'method'=>'POST',"class"=>"login",'enctype' => 'multipart/form-data')); ?>
 
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Add Admin</h3>
            </div>
			
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example1" class="table table-bordered table-striped">
				<tr> 
					<th valign="top">UserName:  <span class="star">*</span></th>
					<td>
						<?php echo $this->Form->input("first_name",array("type"=>"text","class"=>"inp-form","label"=>false,"div"=>false,'error' => true));	 ?>
						<?php  if($error) {  if(isset($error['first_name'])){ ?>					
                                    <span class="help-block">
                                        <strong class="text-danger"><?php echo  array_values($error['first_name'])[0]; ?></strong>
                                    </span>
						<?php  } } ?>
						
					</td>
				</tr>
				<tr>
					<th valign="top">Email:  <span class="star">*</span></th>
					<td>
						<?php echo $this->Form->input("email",array("type"=>"email","class"=>"inp-form","label"=>false,"div"=>false,'error' => true));	 ?>
						<?php  if($error) {  if(isset($error['email'])){ ?>
					
                                    <span class="help-block">
                                        <strong class="text-danger"><?php echo  array_values($error['email'])[0]; ?></strong>
                                    </span>
						<?php  } } ?>
					</td>
				</tr>	
				<tr>
					<th valign="top">Password:  <span class="star">*</span></th>
					<td>
						<?php echo $this->Form->input("password",array("type"=>"password","class"=>"inp-form","label"=>false,"div"=>false,'error' => true));	 ?>
						<?php  if($error) {  if(isset($error['password'])){ ?>
					
                                    <span class="help-block">
                                        <strong class="text-danger"><?php echo  array_values($error['password'])[0]; ?></strong>
                                    </span>
						<?php  } } ?>
					</td>
				</tr>	
					<?php echo $this->Form->input("user_type",array("type"=>"hidden","class"=>"inp-form",'value'=>'A',"label"=>false,"div"=>false,'error' => true));	 ?>
				<tr>
				<th>&nbsp;</th>
				<td valign="top">
				<?php  
				echo $this->Form->submit('Save',array('class'=>"form-submit",'div'=>false))."&nbsp;&nbsp;&nbsp;"; 
				//echo $this->Form->button('Cancel',array('type'=>'button','class'=>"form-reset",'div'=>false,'onclick'=>"location.href='".BASE_URL."admin/events/list'")); 
				?>
				</td>
				<td></td>
			</tr>	
				 </table>
				</div>
	<?php echo $this->Form->end(); ?>

    
</table>
