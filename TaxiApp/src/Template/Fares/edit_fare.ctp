<?php   $this->extend('/Layout/main'); ?>
 <?php //print_r($users); ?>

	
			<?php echo $this->Form->create($fare,array('url'=>['action' => 'edit_fare'],'method'=>'POST',"class"=>"login",'enctype' => 'multipart/form-data')); ?>
	
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
						<?php echo $this->Form->input("category",array("type"=>"text", "class"=>"inp-form","label"=>false,"div"=>false));	 ?>	
					</td>
				</tr>
				<tr>
					<th valign="top">Per Mile Charge:  <span class="star">*</span></th>
					<td>
						<?php echo $this->Form->input("per_mile_charge",array("type"=>"text","class"=>"inp-form","label"=>false,"div"=>false));	 ?>
					</td>
				</tr>	
				<tr>
					<th valign="top">Per Minute Waiting Charge:  <span class="star">*</span></th>
					<td>
						<?php echo $this->Form->input("per_minute_waiting_charge",array("type"=>"text","class"=>"inp-form","label"=>false,"div"=>false));	 ?>
					</td>
				</tr>	
				<tr>
					<th valign="top">Surcharge:  <span class="star">*</span></th>
					<td>
						<?php echo $this->Form->input("surcharge",array("type"=>"text","class"=>"inp-form","label"=>false,"div"=>false));	 ?>
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

<?= $this->Paginator->prev('« Previous').''.$this->Paginator->next('Next »') ?>
