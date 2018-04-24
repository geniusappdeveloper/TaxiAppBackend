<?php

namespace App\Controller;
use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Core\App;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\View\Helper\SessionHelper;

class UsersController extends AppController
{
	public $paginate = [
        'limit' => 15,
        'order' => [
            'Users.id' => 'desc'
        ]
    ];
	 public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
        $this->loadComponent('Common');
		$this->loadModel('PickupRequest');
		$this->loadModel('Transaction');
		$this->loadModel('Pickup');
		$this->loadModel('DriverDetail');
		$this->loadModel('ScheduleRequest');
    }
	
	public function profile($id = null)
	{
		
		$session = $this->request->session();
		$this->setSession();
		$this->set("id",$id);
		 $this->set("title", "Edit Profile");
		  $users_Exist = $this->Users->find()->where(['id' => $id])->first(); 
		
		  if($this->request->getData()){
			 
					if(!empty($users_Exist)) {
							$userData = $this->Users->patchEntity($users_Exist, $this->request->getData());
							$des=$_SERVER['DOCUMENT_ROOT']."/TaxiApp/webroot/img/profile_images/";
							$tmp_name=$_FILES['profile_pic']['tmp_name'];
							$time=time();
							$image=$time.$_FILES['profile_pic']['name'];
							$userData['profile_pic']=$image;
							move_uploaded_file($tmp_name,$des.$image);	//Upload image
							$this->Users->save($userData);  //update record
							$user_Exist1 =   $this->Users->find()->where(['id' => $id])->first();
							$user_Exist1->profile_pic = ($user_Exist1->profile_pic)? Router::url('/', true) . "img/profile_images/" . $user_Exist1->profile_pic:Router::url('/', true) . "img/user_default.png" ;
							$session->write('SESSION_ADMIN',[$user_Exist1->id,$user_Exist1->first_name,$user_Exist1->last_name,$user_Exist1->profile_pic,$user_Exist1->user_type,$user_Exist1->email] );
							$this->Flash->success(__('Your Profile has been Updated.'));
							return $this->redirect(['action' => 'dashboard']);
						}
				}
				$this->set('user', $users_Exist);
	}
	
	
	public function changepassword($id = null){
	$session = $this->request->session();	
	$this->setSession();	
	$this->set("id",$id);
	 $users_Exist1 = $this->Users->find()->where(['id' => $id])->first(); 
	 $old = $users_Exist1->password;
	$this->set("title", "Change Password");
	$users_Exist = $this->Users->newEntity();
	if($this->request->getData()){
		if(!empty($users_Exist1)) {
	$userData = $this->Users->patchEntity($users_Exist, $this->request->getData());
							//print_r($userData);
						$oldpassword = md5($userData->oldpassword);
						if($userData->password == $userData->rpassword){
						if($old == $oldpassword ){
							$newpassword =md5($userData->password);
						$this->Users->query()->update()->set(['password' => $newpassword])->where(['id' => $id])->execute();
						//$this->Flash->success("Cetegories $message Successfully.");
						$this->Flash->success(__('Password Changes Successfully'));
						return $this->redirect(['action' => 'dashboard']);
						}else{	
							$this->Flash->error(__('Password Not Matched'));
						}
						}else{
						$this->Flash->error(__('Password and confirm password Not matched'));
						}}}
				$this->set('user', $users_Exist);
	}
	
	public function login()
	{
		$session = $this->request->session();
		if($session->read("SESSION_ADMIN") != ""){
			$this->redirect(array('controller'=>'users','action'=>'login'));
			die;
		}
		if ($this->request->is(['post']))
		{
			$data = $this->request->data;
			$email =$data['email'];
			$password = md5($data['password']);
			$condition = [
				'email' => $email,
				'password'=> $password,
				'user_type'=>'A'
			];
			$user_Exist =   $this->Users->find()->where($condition)->orWhere(['email' => $email,'password'=> $password,'user_type'=>'S'])->first();
			if (!empty($user_Exist)) {	
				$user_Exist->profile_pic = ($user_Exist->profile_pic)? Router::url('/', true) . "img/profile_images/" . $user_Exist->profile_pic:Router::url('/', true) . "img/user_default.png" ;
				$session->write('SESSION_ADMIN',[$user_Exist->id,$user_Exist->first_name,$user_Exist->last_name,$user_Exist->profile_pic,$user_Exist->user_type,$user_Exist->email] );				
				$this->Flash->success(__('Login Successfully'));
				$this->redirect(['action' => 'dashboard']);
			} else {
				$this->Flash->error(__('Invalid Email or password, try again'));
			}
		}
	}
	
   public function dashboard(){
	   
	   $session=$this->request->session()->read('SESSION_ADMIN');

	$this->set('ses',$session);
	if(!empty($session)){
		
	   $session = $this->request->session();

		$this->set("title", "Dashboard");
		 $this->setSession();
		// $this->Flash->success('Welcome to the Dashboard');
		 $rides = $this->PickupRequest->find()->where(['request_process_status' => 'C'])->count();
		 $activeride = $this->PickupRequest->find()->where(['request_process_status' => 'S'])->count();
		 $date = date('Y-m-d');
		$ridestoday1= $this->PickupRequest->find()->where(['request_process_status' => 'C','created LIKE'  => '%' . $date . '%'])->count();
		 $rider = $this->Users->find()->where(['user_type' => 'U'])->count();
		 $driver = $this->Users->find()->where(['user_type' => 'D'])->count();
		 $approvedriver = $this->Users->find()->where(['user_type' => 'D','is_approved'=>'Y'])->count();
		 $onlinedriver = $this->Users->find()->select(['latitude','longitude'])->where(['user_type' => 'D','is_approved'=>'Y','is_online'=>'Y']);
		 $offlinedriver = $this->Users->find()->select(['latitude','longitude'])->where(['user_type' => 'D','is_approved'=>'Y','is_online'=>'N']);
		// print_r(json_encode($onlinedriver));
		  $as= array('totalrides'=>$rides,'activeride'=>$activeride,'driver'=>$driver,'approvedriver'=>$approvedriver,'rider'=>$rider,'ridestoday1'=>$ridestoday1,'onlinedriver'=>$onlinedriver,'offlinedriver'=>$offlinedriver);
		$this->set('totalrides', $as);
	}else{
		$this->Flash->error('First Login here');
			$this->redirect(array('controller'=>'users','action' => 'login'));
	}
		  
    }


	public function home()
	{
		$this->set("title", "Dashboard");
		 $this->setSession();
		$this->Flash->success('Welcome to the Dashboard.');
		
	}
	
	 function logout(){
		$session = $this->request->session();
		$session->destroy("SESSION_ADMIN");
		$this->redirect(array('controller'=>'users','action' => 'login'));
    }


public function usersList(){
	$session=$this->request->session()->read('SESSION_ADMIN');
	$this->set('ses',$session);
	if(!empty($session)){
	  $q= $this->setSession();

	   $this->set("title", "Users List");
	   $query = $this->Users->find()->where(['is_deleted' => 'N', 'user_type' => 'U']);
	  $users = $this->paginate($this->Users->find()->where(['is_deleted' => 'N', 'user_type' => 'U']));
      $this->set('users', $users);
	}else{
		$this->Flash->error('First Login here');
			$this->redirect(array('controller'=>'users','action' => 'login'));
	}
}

public function adminList(){
	$session=$this->request->session()->read('SESSION_ADMIN');

	$this->set('ses',$session);
	if(!empty($session)){
	   $this->setSession();
	   $this->set("title", "Admin List");
	   $query = $this->Users->find()->where(['is_deleted' => 'N', 'user_type' => 'A']);
	  $users = $this->paginate($this->Users->find()->where(['is_deleted' => 'N', 'user_type' => 'A']));
      $this->set('users', $users);
	  return $this -> render('users_list');
	}else{
		$this->Flash->error('First Login here');
			$this->redirect(array('controller'=>'users','action' => 'login'));
	}
}

		public function driverList(){
			

			$session=$this->request->session()->read('SESSION_ADMIN');
			$this->set('ses',$session);
			$driverdetails=[];
			if(!empty($session)){
			  $this->setSession();
			 $this->set("title", "Drivers List");
			 $query = $this->Users->find()->where(['is_deleted' => 'N', 'user_type' => 'D']);
			 $users = $this->paginate($this->Users->find()->where(['is_deleted' => 'N', 'user_type' => 'D']));
				if($users){
					foreach($users as $n=> $user){
						//$driverdetails = $user;
						//$driver = $this->DriverDetail->find()->where(['user_id' => $user['id']])->first()->toArray();
						//$driverdetails['DriverDetail'] =$driver;
					}
				}
			// $this->paginate = array($query ,'limit' => '5');
			 $this->set('users', $users);
			 //$this->render('users_list');
			}else{
			$this->Flash->error('First Login here');
			$this->redirect(array('controller'=>'users','action' => 'login'));
	}

		}
		
		public function addAdmin(){
			$session=$this->request->session()->read('SESSION_ADMIN');
			$this->set('ses',$session);
			if(!empty($session)){
			 $this->setSession();
			  $this->set("title", "Add Admin");
			$article = $this->Users->newEntity();
				if ($this->request->is('post')) {
					$article = $this->Users->patchEntity($article, $this->request->getData());
					if ($this->Users->save($article)) {
						$this->Flash->success(__('Admin has been added successfully'));
						$this->set('error','');
						return $this->redirect(['action' => 'admin-list']);
					}else{
					//$this->Flash->error(__('Unable to add admin.'));
					$error = $article->errors();
					$this->set('error', $error);
				}
				$this->set('article', $article);
			}
			$this->set('error', $article->errors());
			}else{
			$this->Flash->error('First Login here');
			$this->redirect(array('controller'=>'users','action' => 'login'));
			}
		}


		public function delete(){
			$session = $this->request->session();
			if(isset($this->request->params['form']['IDs'])){
				$deleteString = implode("','",$this->request->params['form']['IDs']);
			}elseif(isset($this->request->params['pass'][0]) ){
				$deleteString = $this->request->params['pass'][0];
			}else{
				
				$this->redirect('users_list');
			}
			if(!empty($deleteString)){
				//$this->Users->deleteAll("id in ('".$deleteString."')");
				$this->Users->query()->update()->set(['is_deleted' => 'Y'])->where(["id in ('".$deleteString."')"])->execute();
				$this->Flash->success('User(s) Deleted Successfully.');
				$this->redirect($this->referer());
			}
		}
		
		
	  public function approved(){ 
				if(isset($this->request->params['pass'][0]) ){
					$id = $this->request->params['pass'][0];
				}
				 $User_Exist = $this->Users->find()->where(['id' => $id])->first();
				if($User_Exist){
					$status = $User_Exist->is_approved =='Y'?'N':'Y';
					$message = $User_Exist->is_approved =='Y'?'Not Approved':'Approved';
					$this->Users->query()->update()->set(['is_approved' => $status])->where(['id' => $id])->execute();
					$this->Flash->success("Driver $message Successfully.");
					$this->redirect($this->referer());
				}
             $this->redirect($this->referer()); 			
		}

	public function setSession(){
		$session = $this->request->session();
			$userSession = $session->read("SESSION_ADMIN");
			if($userSession){
				  $this->set('AdminID',$userSession[0]);
					$this->set('Admin',$userSession[1]);
					$this->set('LastName',$userSession[2]);
					$this->set('AdminImage',$userSession[3]);
					$this->set('UserType',$userSession[4]);
		 }
		 return true;
	}
	
	
	public function view($id = null){
			$session=$this->request->session()->read('SESSION_ADMIN');
			$this->set('ses',$session);
			if(!empty($session)){
			 $this->setSession();	
			  $this->set("title", "Users List");
			  $user = $this->Users->find()->where(['is_deleted' => 'N' , 'id' => $id])->first();
			  $driver = $this->DriverDetail->find()->where(['user_id' => $id])->first();
			  $this->set('user',$user);
			  $this->set('driver',$driver);
			}else{
				$this->Flash->error('First Login here');
				$this->redirect(array('controller'=>'users','action' => 'login'));
			}
	}
	public function scheduleList(){
		$session=$this->request->session()->read('SESSION_ADMIN');
			$this->set('ses',$session);
			if(!empty($session)){
			$this->setSession();	
			 $this->set("title", "Schedule List");	
			 $query = $this->Pickup->find()->where(['ride_status' => 'S']);
			 $pickups= $this->Pickup->find()->where(['ride_status' => 'S'])->order(['Pickup.id' => 'DESC']);
			 $users = $this->paginate( $this->Pickup->find()->where(['ride_status' => 'S']));
			 $users= [];
			 foreach($pickups as $n =>$pickup){
			$users[$n] = $pickup;
			$users[$n]['driver_id']  ='';
			  $get_driver =$this->PickupRequest->find()->where(['request_id'=>$pickup['id']])->order(['id' => 'DESC'])->first();
			  if($get_driver){
			  $driver_id = $get_driver->driver_id;
				$users[$n]['driver_id'] = $driver_id;
				$users[$n]['request_status'] = $get_driver->request_status;
			  }
			 }
			 
			 $driverss = $this->Users->find()->select(['id','first_name','last_name'])->where(['is_approved' => 'Y','is_online' => 'Y','is_deleted' => 'N','user_type'=>"D"]);
			 
			 foreach($driverss as $driverss){
				 
				$driver_id=$driverss['id'];
				 $driver_name=$driverss['first_name']." ".$driverss['last_name'];
				
				
				$drivers[$driver_id]=ucwords($driver_name);	
			 }
			 
			$this->set('users', $users);
			$this->set('driver_id', $driver_id);
			$this->set('drivers', $drivers);
			
			}else{
		$this->Flash->error('First Login here');
			$this->redirect(array('controller'=>'users','action' => 'login'));
	}
		
	}
	public function assign()
	{
		$user = $this->PickupRequest->newEntity();
	$article = $this->PickupRequest->newEntity();
	if ($this->PickupRequest->save($this->PickupRequest->patchEntity($article, $this->request->data['data']))) {
		$user_Exist =   $this->Users->find()->select(['id','email','first_name','last_name','phone_number','country_code','user_type','profile_pic','latitude','longitude','device_token','device_type'])->where(['id' => $this->request->data['data']['driver_id'],'user_type'=>'D'])->first();
				if($user_Exist){
						$message = array(
									'message'=>"Assign you a new schedule ride",
									'noti_type'=>'SR'
									);
							$device_token[] = $user_Exist['device_token'];		
					if($user_Exist['device_type'] == 'A'){
							$notification = $this->Common->android_send_notification($device_token,$message, 'D');
					}else{
							$notification =  $this->Common->iphone_send_notification($device_token,$message, 'D');
					}
				}
				$this->Flash->success(__('Schedule ride driver has been assigned successfully'));
						$this->set('error','');
						}
						
		die();
		
			$this->response->body();
			//return $this->response;
			
}	

	
	public function assign1($ride_id,$user_id,$driver_id){
		$data['request_id']=$ride_id;
		$data['user_id']=$user_id;
		$data['driver_id']=$driver_id;
	    $article = $this->ScheduleRequest->newEntity();
		$session=$this->request->session()->read('SESSION_ADMIN');
			$this->set('ses',$session);
			if(!empty($session)){
			$this->setSession();	
			 $this->set("title", "Schedule List");	
			//$article = ;
			// print_r($article); die;
			if ($this->ScheduleRequest->save($this->ScheduleRequest->patchEntity($article, $data))) {
				$this->Flash->success(__('Schedule ride driver has been assigned successfully'));
						$this->set('error','');
			}
			$this->redirect($this->referer());
			}else{
		$this->Flash->error('First Login here');
			$this->redirect(array('controller'=>'users','action' => 'login'));
	}
		
	}
	
	
	


}




