<?php  $this->extend('/Layout/super_admin'); ?>
 <?php //print_r($users); ?>
<div id="flash" class="message-info success">

    <?= $this->Flash->render(); ?>
</div>
	
			<?php echo $this->Form->create('Category',array('url'=>['action' => 'add_category'],'method'=>'POST',"class"=>"login",'enctype' => 'multipart/form-data')); ?>
 
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Add Category</h3>
            </div>
			
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example1" class="table table-bordered table-striped">
				<tr> 
					<th valign="top">Category Name:  <span class="star">*</span></th>
					<td>
						<?php echo $this->Form->input("category_name",array("type"=>"text","class"=>"inp-form","label"=>false,"div"=>false,'error' => true));	 ?>
						<?php  if(isset($error)) {  if(isset($error['category_name'])){ ?>					
                                    <span class="help-block">
                                        <strong class="text-danger"><?php echo  array_values($error['category_name'])[0]; ?></strong>
                                    </span>
						<?php  } } ?>
						
					</td>
				</tr>
				<tr>
					<th valign="top">Description:  <span class="star">*</span></th>
					<td>
						<?php echo $this->Form->input("description",array("type"=>"textarea","class"=>"inp-form","label"=>false,"div"=>false,'error' => true));	 ?>
						<?php  if(isset($error)){  if(isset($error['description'])){ ?>
					
                                    <span class="help-block">
                                        <strong class="text-danger"><?php echo  array_values($error['description'])[0]; ?></strong>
                                    </span>
						<?php  } } ?>
					</td>
				</tr>
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
