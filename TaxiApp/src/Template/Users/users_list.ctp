<?php  ($UserType =='S')? $this->extend('/Layout/super_admin'):$this->extend('/Layout/main'); ?>
<div id="flash" class="message-info success">

    <?= $this->Flash->render(); ?>
</div>
 
 
          <div class="box">
            <div class="box-header">
              <h3 class="box-title"><?= $title ?> </h3>		
				<?php  if($UserType =='S'){
				 
						echo $this->Html->link("Add Admin",
							array('controller'=>'users','action'=>'add_admin'),
							array('class'=>'btn btn-primary d','style'=>"float:right",'title'=>'Add Admin')
						);   
				 } ?>			  
            </div>
			
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example1" class="table table-bordered table-striped">
                
				<thead>
                <tr>
                  <th>ID </th>
                  <th>First Name</th>
                  <th>Last Name</th>
                  <th>Email</th>
                  <th>Phone Number</th>
				  <th>Image</th>
				<!--  <th>Approval Status</th> -->
				  <th>Option</th>
                </tr>
                </thead>
                
				<tbody>
           
       <?php foreach ($users as $user): ?>
		<tr>
			<td><?= h($user->id) ?> </td>
			<td><?= h($user->first_name) ?> </td>
			<td><?= h($user->last_name) ?> </td>
			<td><?= h($user->email) ?> </td>
			<td><?= h($user->phone_number) ?> </td>
			<td><?php if(!empty($user->profile_pic)){ ?>
	
	<img src="/taxi/webroot/img/profile_images/<?php echo $user->profile_pic; ?>" alt="Brownies" height = '30'  width = '40' />
    <!--img src="/taxi/webroot/img/profile_images/<?php echo $user->profile_pic; ?>" alt="Brownies" height = '30'  width = '40' /--> 
	<?php
 }else{  echo $this->Html->image('default.jpg', ['alt' => 'Profile pic' , 'height'=> '30' ,'width' => '40']);
        
		} ?> </td>
		<!--	<td>
					
						<?php /* if($user->is_approved=='Y'){ ?>
							<span><?php 
								echo $this->Html->link("Approved",
								array('controller'=>'users','action'=>'approved',$user->id),
								array('class'=>'label label-success label-mini','alt'=>'Approved')
							); ?> </span>
							<!--span class="label label-success label-mini">Approved</span-->
						<?php } else { ?><span><?php 
								echo $this->Html->link("Not Approved",
								array('controller'=>'users','action'=>'approved',$user->id),
								array('class'=>'label label-warning label-mini','alt'=>'Not Approved')
							); ?> </span><?php } */ ?>
			</td>-->
			<td>
			<?php
							echo $this->Html->link("",
							array('controller'=>'users','action'=>'view',$user->id),
							array('class'=>'fa fa-eye btn btn-primary btn-xs','title'=>'View')
						);
							?>
							<?php 
							echo $this->Html->link("",
							array('controller'=>'users','action'=>'delete',$user->id),
							array('class'=>'btn btn-danger delete btn-xs fa fa-trash-o ','title'=>'Delete')
						);   
			?>
			</td>
			
		</tr>
    <?php endforeach; ?>
                <tbody>
				 </table>
				</div>
 

    
</table>
<?php
      echo "<div class='center'><ul class='pagination' style='margin:20px auto;'>";
      echo $this->Paginator->prev(  __('Previous'), array('tag' => 'li', 'currentTag' => 'a', 'currentClass' => 'disabled'), null, array('class' => 'prev disabled'));
      echo $this->Paginator->numbers(array('separator' => '','tag' => 'li', 'currentTag' => 'a', 'currentClass' => 'active')); 
          //echo $this->Paginator->first(2);
      echo $this->Paginator->next(__('Next'), array('tag' => 'li', 'currentTag' => 'a', 'currentClass' => 'disabled'), null, array('class' => 'next disabled'));
      echo "</div></ul>";
      ?>

