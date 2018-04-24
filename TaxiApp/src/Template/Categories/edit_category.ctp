<?php   $this->extend('/Layout/super_admin'); ?>
 <?php //print_r($users); ?>

	
			<?php echo $this->Form->create($category,array('url'=>['action' => 'edit_category'],'method'=>'POST',"class"=>"login",'enctype' => 'multipart/form-data')); ?>
	
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Edit Fare</h3>
            </div>
			
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example1" class="table table-bordered table-striped">
				<tr>
					<th valign="top">Category Name:  <span class="star">*</span></th>
					<td>
						<?php echo $this->Form->input("category_name",array("type"=>"text", "class"=>"inp-form","label"=>false,"div"=>false));	 ?>	
					</td>
				</tr>
				<tr>
					<th valign="top">Category Desciption:  <span class="star">*</span></th>
					<td>
						<?php echo $this->Form->input("description",array("type"=>"textarea", "class"=>"inp-form","label"=>false,"div"=>false));	 ?>	
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

    
</table>
