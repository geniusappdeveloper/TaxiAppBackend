<?php  ($UserType =='S')? $this->extend('/Layout/super_admin'):$this->extend('/Layout/main'); ?>
<style>
.side { 
  width: 30%;
  float: left;
  height: 500px;
}
.main { 
width: 70%;
float: left;
height: 700px;
}
.content-wrapper {
  min-height: 800px !important;
}
.doc {
    width: 160px;
}
.modal {
    background: rgba(0,0,0,0.3);
}
.modal {
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    z-index: 1050;
    display: none;
    overflow: hidden;
    -webkit-overflow-scrolling: touch;
    outline: 0;
}
.fade {
    opacity: 0;
    -webkit-transition: opacity .15s linear;
    -o-transition: opacity .15s linear;
    transition: opacity .15s linear;
}
</style>	

<div class="header"><?= 'User Detail' ?>
</div> 
<div class="side">

<?php if(!empty($user->profile_pic)){ ?>
<img src="/taxi/webroot/img/profile_images/<?php echo $user->profile_pic; ?>" alt="Brownies" height = '200'  width = '80%' />
   
   <?php
 }else{  echo $this->Html->image('default.jpg', ['alt' => 'Brownies' , 'height'=> '200' ,'width' => '80%']);
        } ?>
</div>

<div class="main">

<table class="table table-bordered table-hover">

<tr>
<th> FirstName </th>
<td> <?=  $user->first_name ?> </td>
</tr>

<tr>
<th> Lastname </th>
<td> <?=  $user->last_name ?>  </td>
</tr>
<tr>
<th> Email </th>
<td> <?=  $user->email ?>  </td>
</tr>
<tr>
<th> Phone No. </th>
<td>  <?=  $user->phone_number ?>  </td>
</tr>
<tr>
<th> Status </th>
<td>  <?php if($user->is_approved=='Y'){ ?>
							<span>  <a class="label label-success label-mini">Approved </a> </span>
							<!--span class="label label-success label-mini">Approved</span-->
						<?php } else { ?><span> <a class="label label-success label-mini">Not Approved </a>
								</span><?php } ?>  </td>
</tr>
<?php if($user->user_type =='D'){?>
<tr>
<th> City </th>
<td>  <?=  $driver->city ?>  </td>
</tr>
<tr>
<th> Brand </th>
<td>  <?=  $driver->brand ?>  </td>
</tr>
<tr>
<th> Year </th>
<td>  <?=  $driver->year ?>  </td>
</tr>
<tr>
<th> Color </th>
<td>  <?=  $driver->color ?>  </td>
</tr>
<tr>
<th> Interior Color </th>
<td>  <?=  $driver->interior_color ?>  </td>
</tr>
<tr>
<th> License Number </th>
<td>  <?=  $driver->license_number ?>  </td>
</tr>
<tr>
<th>Vehicle Type</th>
<td>  <?=  $driver->vehicle_type ?>  </td>
</tr>
<tr>
<th>Issued On </th>
<td>  <?=  $driver->issued_on ?>  </td>
</tr>
<tr>
<th>Expiry Date </th>
<td>  <?=  $driver->expiry_date ?>  </td>
</tr>

<?php } ?>
</table>

<?php if($user->user_type =='D'){?>
<div class="col-lg-3 col-md-3  col-xs-6" >
<p>License Document </p>	
<?php if(!empty($driver->license_doc)){ ?>
	<img class="doc" src="/taxi/webroot/img/driver_info/<?php echo $driver->license_doc; ?>" alt="License Document " data-toggle="modal" data-target="#modal"/>
   
	<?php
	}else{  echo $this->Html->image('default.jpg', ['alt' => 'license_doc','class'=>'doc']);
        } ?>
</div>

<div class="col-lg-3 col-md-3  col-xs-6">
<p>Insurance Document </p>	
<?php if(!empty($driver->insurance_doc)){ ?>
	<img class="doc"  src="/taxi/webroot/img/driver_info/<?php echo $driver->insurance_doc; ?>" alt="Insurance Document"  data-toggle="modal" data-target="#modal"/>
   
	<?php
	}else{  echo $this->Html->image('default.jpg', ['alt' => 'insurance_doc','class'=>'doc']);
        } ?>
</div>


<div class="col-lg-3 col-md-3  col-xs-6">
<p>Permit Document </p>		
<?php if(!empty($driver->prmit_doc)){ ?>
	<img class="doc"  src="/taxi/webroot/img/driver_info/<?php echo $driver->prmit_doc; ?>" alt="Permit Document" data-toggle="modal" data-target="#modal"/>
   
	<?php
	}else{  echo $this->Html->image('default.jpg', ['alt' => 'prmit_doc','class'=>'doc']);
        } ?>
</div>
<div class="col-lg-3 col-md-3 col-xs-6">
<p>VehicleRegistration Doc </p>		
<?php if(!empty($driver->vehicle_registration_doc)){ ?>
	<img class="doc"  src="/taxi/webroot/img/driver_info/<?php echo $driver->vehicle_registration_doc; ?>" alt="VehicleRegistration Doc"  data-toggle="modal" data-target="#modal"/>
   
	<?php
	}else{  echo $this->Html->image('default.jpg', ['alt' => 'vehicle_registration_doc','class'=>'doc']);
        } ?>
</div>
<?php } ?>
<!-- Main Close -->
</div>

 <div class="modal fade" id="modal" style="display: none;">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span></button>
                <h4 class="modal-title"></h4>
              </div>
              <div class="modal-body">
                <p><img  id="doc_image" src="" alt="Permit Document"/></p>
              </div>
              <div class="modal-footer">
              </div>
            </div>
            <!-- /.modal-content -->
         </div> 
          <!-- /.modal-dialog -->
 </div>
<!--<div class="alert alert-danger">amit kumar</div>-->
