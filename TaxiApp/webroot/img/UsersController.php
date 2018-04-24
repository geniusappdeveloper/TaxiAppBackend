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
        'limit' => 5,
        'order' => [
            'Users.id' => 'asc'
        ]
    ];
	
	 public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
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
							
							$des=$_SERVER['DOCUMENT_ROOT']."/TaxiApp/webroot/img/profile_images";
							print_r($des); 
							$tmp_name=$_FILES['profile_pic']['tmp_name'];
							$time=time();
							$image=$time.$_FILES['profile_pic']['name'];
							$userData['profile_pic']=$image;
							move_uploaded_file($tmp_name,$des.$image);	
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
				$this->redirect(['action' => 'home']);
			} else {
				$this->Flash->error(__('Invalid Email or password, try again'));
			}
		}
	}
	
   public function dashboard(){
		$this->set("title", "Dashboard");
		 $this->setSession();
		 $this->Flash->success('Welcome to the Dashboard');
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
	 $this->render('users_list');

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
					$this->Flash->success("User $message Successfully.");
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
	


}




