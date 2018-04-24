
<?php  use Cake\I18n\Time; ($UserType =='S')? $this->extend('/Layout/super_admin'):$this->extend('/Layout/main'); ?>
<div id="flash" class="message-info success">

    <?= $this->Flash->render(); ?>
</div>
 <?php  
 
 // foreach($drivers as $key => $value){
	 // $drivers_id=$drivers->driver_id;
	 // print_r($drivers_id);
 // print_r($key);
 // print_r($value);

 
 // }die;?>
 
          <div class="box">
            <div class="box-header">
              <h3 class="box-title"><?= $title ?> </h3>		
						  
            </div>
			
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example1" class="table table-bordered table-striped">
                
				<thead>
                <tr>
                  <th>ID </th>
                  <th>User Name</th>
                  <th>Source Location</th>
                  <th>Destination Location</th>
                  <th>Category</th>
				  <th>Paymont Mode</th>
				  <th>Date</th>
				  <th>Assign</th>
				    <th>Request Status</th>
                </tr>
                </thead>
                
				<tbody>
           
       <?php foreach ($users as $user):?>
	   
		<tr>
			<td> <div id= "request_id" request_id="<?php echo $user->id; ?>" ><?= h($user->id) ?> </div></td>
			<td><div id= "user_id" user_id="<?php echo $user->user_id; ?>" ><?= h($user->user_id) ?> </div> </td>
			<td><?= h($user->source_location) ?> </td>
			<td><?= h($user->dest_location) ?> </td>
			<td><?= h($user->category) ?> </td>
			<td><?php if($user->payment_mode =='CC'){ echo "Card"; }else{ echo" " ;}?>
			</td>
			<td><?php
			$date=date_format($user->date_time,"Y-m-d H:i:s");
			echo $date; ?>  </td>
			<td>
			<span>
			<?php $time = strtotime($user->decline_time);
				$current = strtotime(new Time());
				if($current > $time ){
					$status ="X";
				}else{
					$status = 'A';
				}	
			?>
			<?php if($user->driver_id !="" && $status =='X'){?>
			<select  class="leave_response" name="leave"  id="leave" request_id="<?php echo $user->id; ?>" user_id ="<?php echo $user->user_id; ?>"  onchange='hideselect(this.value)'; disabled="<?php echo $disable; ?>">
			<?php }else{ ?>
			<select  class="leave_response" name="leave"  id="leave" request_id="<?php echo $user->id; ?>" user_id ="<?php echo $user->user_id; ?>"  onchange='hideselect(this.value)'; >
			<?php } ?>
			<option value =""> Select a Driver </option>
			<?php foreach($drivers as $key =>$value){ ?> 
			
			<option value="<?= $key ?>"
			<?php
                if ($key == $user->driver_id){
                    echo 'selected="selected"';
                }
            ?>  >
			
			<?=  $value ?></option>
			
			<?php $keys = $key; } ?>
			</select></span>
			</td>
			<td><?php if($user->request_status=='A') echo "Accepted"; else if($user->request_status=='D') echo 'Decline';else echo "Pending"; ?> </td>
			<!--td>
			
			<span><?php 
								/* echo $this->Html->link("assign",
								array('controller'=>'users','action'=>'assign',$user->id,$user->user_id,$keys),
								array('class'=>'label label-success label-mini','alt'=>'assign')
							); */ ?> </span>
			
			</td-->
			
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script>
$('.leave_response').on('change', function() {
    var responseId = $(this).val();
	var request_id = $(this).attr('request_id');
	var user_id = $(this).attr('user_id');
	
	var time ='<?php $date = date_format($user->date_time,"Y-m-d H:i:s");
			echo $date;?>';
	var data =[];
	$.ajax({
						  type: "POST",
						  url: "http://18.218.130.74/TaxiApp/users/assign",
						  data: { data: {'driver_id':responseId,'user_id':user_id,'request_id':request_id,'date_time':time,'request_status':'A'}},
						  success: function(data){ 
							alert('success'); 
							location.reload();
						  }
						});
	
});
</script>
<script>
/*var $select = $("select");
$select.on("change", function() {
    var selected = [];  
    $.each($select, function(index, select) {           
        if (select.value !== "") { selected.push(select.value); }
    });         
   $("option").prop("disabled", false);         
   for (var index in selected) { $('option[value="'+selected[index]+'"]').prop("disabled", true); }
}); */
</script>




