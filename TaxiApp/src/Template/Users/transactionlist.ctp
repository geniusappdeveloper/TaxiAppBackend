<?php 

  $this->extend('/Layout/main'); ?>
 <?php //print_r($users); ?>

	
 
 
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Transaction List</h3>
            </div>
			 <div class="pull-right">
			<?php /*echo $this->Html->link("Add Fare",
							array('controller'=>'fares','action'=>'add_fare'),
							array('class'=>'btn btn-default btn-flat','title'=>'Add Fare')
						);   */ ?>
          </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example1" class="table table-bordered table-striped">
                
				<thead>
                <tr>
                  <th>ID </th>
                  <th>User Name</th>
                  <th>Driver Name</th>
				   <th>Source</th>
				    <th>Destination</th>
				    <th>Amount</th>
					 <th>Surcharge</th>
					  <th>Total Amount</th>
					 <th> Transaction status</th>
					 <th>Date </th>
					 <th>Option</th>
					 
                </tr>
                </thead>
                
				<tbody>
           <?php //print_r($transaction);  ?>
       <?php foreach ($transaction as $transaction): ?>
    <tr>
	
        <td><?= h($transaction->id) ?> </td>
        <td><?php $user = $transaction->user;  echo $user->first_name; ?> </td>
		<td><?php $driver= $transaction->driver; echo $driver->first_name;  ?> </td>
		<td><?php  $pickup= $transaction->pickup; echo $pickup->source_location; ?> </td>
		<td><?php  $pickup= $transaction->pickup; echo $pickup->dest_location; ?></td>
		<td><?= h($transaction->amount) ?> </td>
		<td><?= h($transaction->with_surcharge) ?> </td>
		<td><?= h($transaction->total_amount) ?> </td>
		
		<td><?php if($transaction->transaction_status ='C'){ echo 'Paid';}else{ echo 'Unpaid';} ?> </td>
		<td><?= h($transaction->created) ?> </td>
		<td>
			<?php
							/*echo $this->Html->link("",
							array('controller'=>'transactions','action'=>'editFare',$transaction->id),
							array('class'=>'fa fa-edit btn btn-primary btn-xs','title'=>'View')
						);*/
							?>
							<?php 
							echo $this->Html->link("",
							array('controller'=>'transactions','action'=>'delete',$transaction->id),
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
