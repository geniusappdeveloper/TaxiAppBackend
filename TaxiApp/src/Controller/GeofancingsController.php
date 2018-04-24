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
use Cake\Http\Response;
use Cake\Http\ServerRequest;
class GeofancingsController extends AppController
{
	
	public $paginate = [
        'limit' => 5,
        'order' => [
            'Users.id' => 'asc'
        ]
    ];
	
	 public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
		$this->loadModel('PickupRequest');
		$this->loadModel('Transaction');
    }
	
	public function profile($id = null)
	{
		$session = $this->request->session();
		//print_r($session->read("SESSION_ADMIN"));
	//	print_r($session->read("SESSION_ADMIN.id"));
	 $this->setSession();
		//$id = $session->read("SESSION_ADMIN.id");
		//print_r($id);
		//$id='1';
		$this->set("id",$id);
		 $this->set("title", "Edit Profile");
		  $users_Exist = $this->Users->find()->where(['id' => $id])->first(); 
		
		  if($this->request->getData()){
			 
					if(!empty($users_Exist)) {
							$userData = $this->Users->patchEntity($users_Exist, $this->request->getData());
							// print_r($userData);die;
							
							$des=$_SERVER['DOCUMENT_ROOT']."/TaxiApp/webroot/img/profile_images/";
							//print_r($des); 
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
			$this->redirect(array('controller'=>'users','action'=>'home'));
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
			$user_Exist =   $this->Users->find()->where($condition)->first();
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
		  
    }


	public function lists()
	{
		$session=$this->request->session()->read('SESSION_ADMIN');
			$this->set('ses',$session);
			if(!empty($session)){
		$this->set("title", "Dashboard");
		 $this->setSession();
		
		
		$geocodeObject = json_decode(file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address=Belize&key=AIzaSyBeEd1XX_ch8gUBQnauEm8HKa-SCQt4OvY'), true);
		
		$latitude = $geocodeObject['results'][0]['geometry']['location']['lat'];
		$longitude = $geocodeObject['results'][0]['geometry']['location']['lng'];
		
		$responseStyle = 'short'; // the length of the response
		$citySize = 'cities15000'; // the minimal number of citizens a city must have
		$radius = 300; // the radius in KM
		$maxRows = 30; // the maximum number of rows to retrieve
		$username = '{mandeepsingh}'; // the username of your GeoNames account

			// get nearby cities based on range as array from The GeoNames API
		$nearbyCities = json_decode(file_get_contents('http://api.geonames.org/findNearbyPlaceNameJSON?lat='.$latitude.'&lng='.$longitude.'&style='.$responseStyle.'&cities='.$citySize.'&radius='.$radius.'&maxRows='.$maxRows.'&username=mandeepsingh', true));

		$data1 = $nearbyCities;

		$data = $this->Geofancings->find()->select(['latitude','longitude','id','address'])->where(['deleted' => 'N']);
	
		$this->set('totalrides', $data);
	}else{
			$this->Flash->error('First Login here');
			$this->redirect(array('controller'=>'users','action' => 'login'));
		}
	}
	
	 function logout(){
		$session = $this->request->session();
		$session->destroy("SESSION_ADMIN");
		$this->redirect(array('controller'=>'users','action' => 'login'));
    }


public function usersList(){
	   $this->setSession();
	   $this->set("title", "Users List");
	   $query = $this->Users->find()->where(['is_deleted' => 'N', 'user_type' => 'U']);
	  $users = $this->paginate($this->Users->find()->where(['is_deleted' => 'N', 'user_type' => 'U']));
      $this->set('users', $users);
}

public function driverList(){
	  $this->setSession();
	 $this->set("title", "Drivers List");
	 $query = $this->Users->find()->where(['is_deleted' => 'N', 'user_type' => 'D']);
	 $users = $this->paginate( $this->Users->find()->where(['is_deleted' => 'N', 'user_type' => 'D']));
	// $this->paginate = array($query ,'limit' => '5');
     $this->set('users', $users);
	 //$this->render('users_list');

}


public function add()

{

	$user = $this->Users->newEntity();

	if($this->request->is('post')) {

		$this->Users->patchEntity($user,$this->request->data);

		if($this->Users->save($user)){

			$this->Flash->success(__('Your account has been registered .'));

			return $this->redirect(['action' => 'login']);

		}

		$this->Flash->error(__('Unable to register your account.'));

	}

	$this->set('user',$user);

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
				$this->Geofancings->query()->update()->set(['deleted' => 'Y'])->where(["id in ('".$deleteString."')"])->execute();
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
		 }
		 return true;
	}
	
	
	public function view($id = null){
          $this->setSession();
	      $this->set("title", "Users List");
	      $user = $this->Users->find()->where(['is_deleted' => 'N' , 'id' => $id])->first();
		  $this->set('user',$user);
		  
	}
	
	public function saveData()
	{
		$user = $this->Geofancings->newEntity();
		//if($this->request->is('post')) {

		$this->Geofancings->patchEntity($user,$this->request->data['data']);
		if($this->Geofancings->save($user)){
			$result = $this->Geofancings->save($user);
			
			$this->response->body($result);
			return $this->response;
			//$data1 = array('status'=> '1' , 'id' => $result->id);
			
		//return $this->response->withBody($data1);
		}
		    // return Response::withBody(array('status'=> '1' , 'id' => $result->id));
				// $response = $response->withType('application/json')
				// ->withStringBody(json_encode(['status' => '1','id'=>$result->id]));
			// print_r($response);
			// return $response;
			//return $this->response->body(array('status'=> '1' , 'id' => $result->id));
			 //$this->response->type('json');  // this will convert your response to json
        /*$this->response->body([
                    'status' => '1',
                    'id' => $result->id,
                   
                ]);   // Set your response in body
        $this->response->send(); */ // It will send your response
         // At the end stop the response
			//$this->Flash->success(__('Your account has been registered .'));

		//}
	   //}
		

	}
	
	

}




