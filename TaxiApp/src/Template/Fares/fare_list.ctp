<?php   $this->extend('/Layout/main'); ?>
 <?php //print_r($users); ?>

	
 
 
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Category List</h3>
            </div>
			 <div class="pull-right">
			<?php echo $this->Html->link("Add Fare",
							array('controller'=>'fares','action'=>'add_fare'),
							array('class'=>'btn btn-default btn-flat','title'=>'Add Fare')
						);    ?>
          </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example1" class="table table-bordered table-striped">
                
				<thead>
                <tr>
                  <th>ID </th>
                  <th>Category</th>
                  <th>Per Mile Charge</th>
				   <th>Per Minute Waiting Charge</th>
				    <th>Surcharge</th>
				    <th>Option</th>
                </tr>
                </thead>
                
				<tbody>
           
       <?php foreach ($fares as $fare): ?>
    <tr>
        <td><?= h($fare->id) ?> </td>
        <td><?= h($fare->category) ?> </td>
		<td><?= h($fare->per_mile_charge) ?> </td>
		<td><?= h($fare->per_minute_waiting_charge) ?> </td>
		<td><?= h($fare->surcharge) ?> </td>
		<td>
			<?php
							echo $this->Html->link("",
							array('controller'=>'fares','action'=>'editFare',$fare->id),
							array('class'=>'fa fa-edit btn btn-primary btn-xs','title'=>'View')
						);
							?>
							<?php 
							echo $this->Html->link("",
							array('controller'=>'fares','action'=>'delete',$fare->id),
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

<?= $this->Paginator->prev('« Previous').''.$this->Paginator->next('Next »') ?>
