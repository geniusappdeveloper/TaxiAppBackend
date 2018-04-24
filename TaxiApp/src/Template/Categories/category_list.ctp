<?php   $this->extend('/Layout/super_admin'); ?>
 <?php //print_r($users); ?>

 
 
 
          <div class="box">
            <div class="box-header">
			   <h3 class="box-title"><?= $title ?> </h3>		
				<?php 
						echo $this->Html->link("Add Category",
							array('controller'=>'categories','action'=>'add_category'),
							array('class'=>'btn btn-primary d','style'=>"float:right",'title'=>'Add Category')
						);   
				?>			  
            </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example1" class="table table-bordered table-striped">
                
				<thead>
                <tr>
                  <th>ID </th>
                  <th>Title</th>
                  <th>Description</th>
				   <th>Status</th>
				    <th>Option</th>
                </tr>
                </thead>
                
				<tbody>
           
       <?php foreach ($categories as $category): ?>
    <tr>
        <td><?= h($category->id) ?> </td>
        <td><?= h($category->category_name) ?> </td>
		<td><?= h($category->description) ?> </td>
		<td>
			<?php if($category->status=='Y'){ ?>
							<span><?php 
								echo $this->Html->link("Active",
								array('controller'=>'categories','action'=>'change_status',$category->id),
								array('class'=>'label label-success label-mini','alt'=>'Active')
							); ?> </span>
							<!--span class="label label-success label-mini">Approved</span-->
						<?php } else { ?><span><?php 
								echo $this->Html->link("Deactive",
								array('controller'=>'categories','action'=>'change_status',$category->id),
								array('class'=>'label label-warning label-mini','alt'=>'Deactive')
							); ?> </span><?php } ?>
		</td>
		<td>
			<?php
							echo $this->Html->link("",
							array('controller'=>'categories','action'=>'edit_category',$category->id),
							array('class'=>'fa fa-edit btn btn-primary btn-xs','title'=>'View')
						);
							?>
							<?php 
							echo $this->Html->link("",
							array('controller'=>'categories','action'=>'delete',$category->id),
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
         
      echo $this->Paginator->next(__('Next'), array('tag' => 'li', 'currentTag' => 'a', 'currentClass' => 'disabled'), null, array('class' => 'next disabled'));
      echo "</div></ul>";
      ?>
