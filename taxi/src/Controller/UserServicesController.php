<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Core\Configure;
use Cake\Core\App;
use Cake\Event\Event;
use Cake\Validation\Validator;
use App\Model\Table\UsersTable;
use App\Model\Entity\User;
use Cake\Mailer\Email;
use Cake\Routing\Router;
use Cake\Datasource\ConnectionManager;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
use Cake\I18n\Time;
/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link http://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class UserServicesController extends AppController
{

    var $name = "Users";
    /*
     *
     * Specifies helpers classes used in the view pages
     * @access public
     */
    public $helpers = array();

    /**
     * Specifies components classes used
     * @access public
     */
    var $components = array('RequestHandler', 'Email', 'Common');
   // var $paginate = array();
   
     public $paginate = [
        'limit' => 5
    ];

    #_________________________________________________________________________#

    /**
     * @Date: 12-jan-2018
     * @Method : beforeFilter
     * @Purpose: This function is called before any other function.
     * @Param: none
     * @Return: none 
     * */
    public function beforeFilter(Event $event){
        parent::beforeFilter($event);
        $action = (string) $this->request->action;
        $this->data = $this->request['data'];
        if ($this->isAction($action)) {
            $method = $this->request['data'];
            $this->$action();
        } else {
            $result = array('status' => '0', 'message' => "something wrong");
            echo json_encode($result);
            die;
        }
    }
    public function initialize()
            {
                parent::initialize();
                $this->loadComponent('Upload');
                $this->loadComponent('Common');
                $this->loadModel('Users');
				$this->loadModel('Pickup');
				$this->loadModel('PickupRequest');
				$this->loadModel('Charge');
				$this->loadModel('Feedback');
				$this->loadModel('Transaction');
				$this->loadModel('Account');
				$this->loadModel('DriverDetail');
				$this->loadModel('Category');
				 $this->loadComponent('Paginator');
                //$this->loadComponent('Flash'); // Include the FlashComponent
            }
    #_________________________________________________________________________#

    function index() {
        
    }

    #_________________________________________________________________________#

    /**
     * @Date: 13 jan 2018
     * @Method : register
     * @Purpose: This page will render home page
     * @Param: none
     * @Return: none 
     * */
    function register() {
        $saveArray = $this->data;
		  /* $result = array('status' => '1', 'message' => "Registered successfully.", 'result' => $saveArray);
		 echo json_encode($result); 
		die; */ 
        $saveArray['access_token'] = uniqid().rand(10000000, 99999999).time();
        $saveArray['unique_code'] = rand('1111','9999');
        $this->loadModel('Users');
        $user   =   $this->Users->newEntity();  
        $user   =   $this->Users->patchEntity($user, $saveArray); 
        if (empty($user->errors())) {
            $this->loadComponent('Twilio');
			$sms = 'Welcome to Kungo App!! Your Verification code is '.$saveArray['unique_code'];
            $response = $this->Twilio->sms("12243343502", $saveArray['country_code'].$saveArray['phone_number'],$sms); //$from, $to, $message //+1 224-334-3502
            $user['password'] = md5($saveArray['password']);			
            $result = $this->Users->save($user);			
            $id = $result->id;
			if($saveArray['user_type']=='D'){
				$details   =   $this->DriverDetail->newEntity();  
				$driver =[
				'user_id' => $id
				];
				$details =$this->DriverDetail->patchEntity($details, $driver); 
				$result = $this->DriverDetail->save($details);
			}
            if (!empty($id)) {
                $data =[
                    'user_id'=>$id,
                    'first_name'=>$saveArray['first_name'],
                    'last_name'=>$saveArray['last_name'],
                    'email'=>$saveArray['email'],
                    'country_code'=>$saveArray['country_code'],
                    'phone_number'=>$saveArray['phone_number'],
                    'user_type'=>$saveArray['user_type'],
                    'unique_code'=>$saveArray['unique_code'],
					'is_online' =>$result->is_online,
					'is_approved' =>$result->is_approved,
					'card_exist' =>'N'
                ];
                $result = array('status' => '1', 'message' => "Registered successfully.", 'result' => $data);
            } else {
                $result = array('status' => '0', 'message' => "Unable to signup.");
            }
        } else {
            $errors = $user->errors();
			$error_message =[];$show_message= "";
			if($errors){
				foreach($errors as $n=> $error){
				 $message = array_values($error);
				 $error_message[] = $message[0];
				}
				if($error_message){
					$show_message = implode(",",$error_message);
				}
			}
            $result = array('status' => '0', 'message' => $show_message);
        }
        echo json_encode($result,200); 
        die;
    }
    #_________________________________________________________________________#

    /**
     * @Date: 19 jan 2018
     * @Method : login
     * @Purpose: This function is used to login user
     * @Param: none
     * @Return: none 
     * */
    function login() {
        $saveArray = $this->data;  
        $this->loadModel('Users');
        if (isset($saveArray['country_code']) && isset($saveArray['phone_number']) && isset($saveArray['password']) && !empty($saveArray['country_code']) && !empty($saveArray['phone_number']) && !empty($saveArray['password'])) {
            $password = md5(trim($saveArray['password']));
            $condition = [
                'country_code' =>utf8_decode(trim($saveArray['country_code'])),
                'phone_number'=>trim($saveArray['phone_number']),
                'user_type'=>trim($saveArray['user_type']),
                'password'=>$password,
                'is_active' => 'Y',
                'is_deleted' => 'N'
            ];
            $user_Exist =   $this->Users->find()->select(['id','first_name','last_name','email','profile_pic','phone_number','country_code','user_type','is_verified','is_online','is_approved','unique_code'])->where($condition)->first();
            if (!empty($user_Exist)) {
				$card_exist = $this->Account->find()->where(['user_id'=>$user_Exist['id']])->first();
				 $user_Exist['card_exist'] = !empty($card_exist)?'Y':'N';
				 $user_Exist['profile_pic'] = ($user_Exist['profile_pic'])? Router::url('/', true) . "img/profile_images/" . $user_Exist['profile_pic']:Router::url('/', true) . "img/user_default.png" ;
                $result = array('status' => '1', 'message' => 'Success', 'result' =>$user_Exist);
            } else {
                $result = array('status' => '0', 'message' => 'Phone Number or Password is incorrect.');
            }
        } else {
            $result = array('status' => '0', 'message' =>'Fields are required.');
        }
        echo json_encode($result,200);
        die;
    }

    #_________________________________________________________________________#

    /**
     * @Date: 19 jan 2018
     * @Method : Social With Login
     * @Purpose: This function is used to login user
     * @Param: none
     * @Return: none 
     * */
    function SocialWithLogin() {
        $saveArray = $this->data;  
        $this->loadModel('Users');
        if(!empty($saveArray['first_name']) && !empty($saveArray['email']) && !empty($saveArray['unique_id']) && !empty($saveArray['login_type'])){
			$user_Exist =   $this->Users->find()->select(['id','first_name','last_name','email','profile_pic','phone_number','country_code','user_type','unique_id','login_type','is_verified','is_online','is_approved'])->where(['unique_id'=>$saveArray['unique_id']])->first();
			if($user_Exist){
				if($user_Exist['profile_pic']){
				$filename= "http://18.218.130.74/taxi/img/profile_image/".$user_Exist['profile_pic'];
				$file_headers = @get_headers($filename);
				if($file_headers[0] != 'HTTP/1.1 404 Not Found') {
					$user_Exist['profile_pic'] = Router::url('/', true) . "img/profile_images/" . $user_Exist['profile_pic'];
				}else{
					$user_Exist['profile_pic'] = $user_Exist['profile_pic'] ;
				}
				}else{
					$user_Exist['profile_pic'] = Router::url('/', true) . "img/user_default.png" ;
				}
				$card_exist = $this->Account->find()->where(['user_id'=>$user_Exist['id']])->first();
				$user_Exist['card_exist'] = !empty($card_exist)?'Y':'N';
				$user_Exist['country_code'] = ($user_Exist['country_code'])?$user_Exist['country_code']:'';
				$user_Exist['phone_number'] = ($user_Exist['phone_number'])?$user_Exist['phone_number']:'';
                $result = array('status' => '1', 'message' => 'Success', 'result' =>$user_Exist);
			
            } else {
				$saveArray = [
					'email' => $saveArray['email'],
					'first_name'=>$saveArray['first_name'],
					'last_name'=>isset($saveArray['last_name'])?$saveArray['last_name']:'',
					'unique_id'=>$saveArray['unique_id'],
					'profile_pic'=>$saveArray['profile_pic'],
					'login_type'=>$saveArray['login_type'],
					'is_active' => 'Y',
					'is_deleted' => 'N'
				];
                $user   =   $this->Users->newEntity();  
				$user   =   $this->Users->patchEntity($user, $saveArray); 
				 if (empty($user->errors())) {
						$result = $this->Users->save($user);
						 $id = $result->id;
						if (!empty($id)) {
						$data =[
							'id'=>$id,
							'first_name'=>$saveArray['first_name'],
							'last_name'=>$result->last_name,
							'email'=>$saveArray['email'],
							'country_code'=>($result->country_code)?$result->country_code:'',
							'phone_number'=>($result->phone_number)?$result->phone_number:'',
							'user_type'=>$result->user_type,
							'profile_pic'=>$result->profile_pic,
							'login_type'=>$result->login_type,
							'unique_id'=>$result->unique_id,
							'is_verified'=>$result->is_verified,
							'is_online' =>$result->is_online,
							'is_approved' =>$result->is_approved,
							'card_exist' =>'N'
						];
						 $result = array('status' => '1', 'message' => 'Success', 'result' =>$data);
					}
				 }else{
					  $errors = $user->errors();
						$error_message =[];$show_message= "";
						if($errors){
							foreach($errors as $n=> $error){
							 $message = array_values($error);
							 $error_message[] = $message[0];
							}
							if($error_message){
								$show_message = implode(",",$error_message);
							}
						}
						$result = array('status' => '0', 'message' => $show_message,'otp'=>'');
				}
			}
        } else {
            $result = array('status' => '0', 'message' =>'Fields are required.');
        }
        echo json_encode($result);
        die;
    }
    #_________________________________________________________________________#

    /**
     * @Date: 14 jan 2018
     * @Method : login
     * @Purpose: This function is used to verify user code
     * @Param: none
     * @Return: none 
     * */
	  #_________________________________________________________________________#
    function verifyCode() {
        $saveArray = $this->data;  
        $this->loadModel('Users');
        if (isset($saveArray['user_id']) && isset($saveArray['unique_code'])  && !empty($saveArray['user_id'])  && !empty($saveArray['unique_code'])) {
            $condition = [
                'id' => $saveArray['user_id'],
                'unique_code'=>$saveArray['unique_code']
            ];
            $user_Exist =   $this->Users->find()->select(['id','first_name','last_name','phone_number','country_code','user_type'])->where($condition)->first();
            if (!empty($user_Exist)) {
                $this->Users->query()->update()
                ->set(['is_verified' => 'Y'])
                ->where($condition)
                ->execute();
                $result = array('status' => '1', 'message' => 'Success', 'result' =>$user_Exist);
            } else {
                $result = array('status' => '0', 'message' => 'Invalid verification Code.');
            }
        } else {
            $result = array('status' => '0', 'message' =>'Fields are required.');
        }
        echo json_encode($result);
        die;
    }

#_________________________________________________________________________#

    /**
     * @Date: 14 jan 2018
     * @Method : login
     * @Purpose: This function is used to verify user code
     * @Param: none
     * @Return: none 
     * */
    function ResendCode() {
        $saveArray = $this->data;  
        $this->loadModel('Users');
        if (isset($saveArray['user_id'])  && !empty($saveArray['user_id']) ) {
            $userData['unique_code'] = rand('1111','9999');
            $condition = [
                'id' => $saveArray['user_id']
            ];
            $user_Exist =  $this->Users->get($saveArray['user_id']); //get data using id
            if (!empty($user_Exist)) {
				$this->loadComponent('Twilio');
				$sms = 'Welcome to Kungo App!! Your Verification code is '.$userData['unique_code'];
				$response = $this->Twilio->sms("12243343502", $user_Exist['country_code'].$user_Exist['phone_number'],$sms); //$from, $to, $message //+1 224-334-3502
                $result = $this->Users->patchEntity($user_Exist, $userData);
                $this->Users->save($result);  //update record
               /*  $query = $this->Users->query();
                $result = $this->Users->query()->update()
                ->set(['unique_code' =>  $saveArray['unique_code']])
               // ->select(['id','first_name','last_name','phone_number','country_code','user_type','unique_code'])
                ->where($condition)
                ->execute(); 
                pr($result); */
                $result = array('status' => '1', 'message' => 'Verification Code sent successfully.', 'result' =>$userData);
            } else {
                $result = array('status' => '0', 'message' => 'User Does not exist.');
            }
        } else {
            $result = array('status' => '0', 'message' =>'Fields are required.');
        }
        echo json_encode($result);
        die;
    }
	
	 #_________________________________________________________________________#

    /**
     * @Date: 28 April 2018
     * @Method : Change PAssword
     * @Purpose: This function is used to verify user code
     * @Param: none
     * @Return: none 
     * */
	  #_________________________________________________________________________#
	    function ChangePassword() {
        $saveArray = $this->data;  
        $this->loadModel('Users');
		if (isset($saveArray['user_id'])  && !empty($saveArray['user_id']) && !empty($saveArray['old_password']) && !empty($saveArray['password']) && !empty($saveArray['c_password'])) {
            $user_Exist =  $this->Users->get($saveArray['user_id']); //get data using id
        if (!empty($user_Exist)) {
			 $old = $user_Exist->password;
			$newpassword = md5($saveArray['password']);
			if($saveArray['password'] == $saveArray['c_password']){
				if(md5($saveArray['old_password']) == $old ){
				if($old != $newpassword ){
				$this->Users->query()->update()->set(['password' => $newpassword])->where(['id' => $saveArray['user_id']])->execute();
				//$this->Flash->success("Cetegories $message Successfully.");
				 $result = array('status' => '1', 'message' => 'Password updated successfully.');
				}else{
					 $result = array('status' => '0', 'message' => 'New password is same as existing password.');	
				}
			}else{
					 $result = array('status' => '0', 'message' => 'Invalid existing Password');	
				}	
			}else{
					 $result = array('status' => '0', 'message' => 'Password and Confirm Password does not match.');	
				}		
			} else {
                $result = array('status' => '0', 'message' => 'User Does not exist.');
            }
        } else {
            $result = array('status' => '0', 'message' =>'Fields are required.');
        }
		echo json_encode($result);
        die;
		}
	  
	  
	#_________________________________________________________________________#

    /**
     * @Date: 14 jan 2018
     * @Method : login
     * @Purpose: This function is used toget Device TOken
     * @Param: none
     * @Return: none 
     * */

    function GetDeviceToken() {
        $saveArray = $this->data;  
        $this->loadModel('Users');
        if (isset($saveArray['user_id'])  && !empty($saveArray['user_id'])) {
            $user_Exist =  $this->Users->get($saveArray['user_id']); //get data using id
        if (!empty($user_Exist)) {
                unset($saveArray['user_id']);
                $result = $this->Users->patchEntity($user_Exist, $saveArray);
                $this->Users->save($result);  //update record
                $result = array('status' => '1', 'message' => 'Device token updated successfully.');
            } else {
                $result = array('status' => '0', 'message' => 'User Does not exist.');
            }
        } else {
            $result = array('status' => '0', 'message' =>'Fields are required.');
        }
        echo json_encode($result);
        die;
    }
	
	 #_________________________________________________________________________#

    /**
     * @Date: 18 jan 2018
     * @Method : get category
     * @Purpose: This function get category 
     * @Param: none
     * @Return: none 
     * */
    #_____________________________________________________________________________#

    function GetCategory() {
        $saveArray = $this->data;  
        if (isset($saveArray['user_id'])  && !empty($saveArray['user_id']) ) {
            $condition = [
                'id' => $saveArray['user_id']
            ];
        $user_Exist =   $this->Users->find()->select(['id','email','first_name','last_name','phone_number','country_code','user_type','profile_pic','car_number','make','model','social_security_number','license_info','is_online','is_approved','car_capacity'])->where($condition)->first(); 
        if (!empty($user_Exist)) {
				$query = $this->Category->find()->select(['id','category_name','description'])->where(['status'=>'Y','is_deleted'=>'N']);
				$cat_exist = $query->all();
                $result = array('status' => '1', 'message' => 'Category detail.','result'=>$cat_exist);
            } else {
                $result = array('status' => '0', 'message' => 'User Does not exist.');
            }
        } else {
            $result = array('status' => '0', 'message' =>'Fields are required.');
        }
        echo json_encode($result);
        die;

    }

	#_________________________________________________________________________#

    /**
     * @Date: 19 jan 2018
     * @Method : login
     * @Purpose: This function Forget Password is used to send password to phone number 
     * @Param: none
     * @Return: none 
     * */

    function ForgetPassword() {
        $saveArray = $this->data;       
        if (isset($saveArray['email'])  && !empty($saveArray['email']) ) {
            $condition = [
                'email' => $saveArray['email']
            ];
        $user_Exist =   $this->Users->find()->select(['id','email','first_name','last_name','phone_number','country_code','user_type'])->where($condition)->first(); 
        if (!empty($user_Exist)) {
                $password = rand('11111111','99999999');
                $userData['password'] = md5($password);
                $message = "Dear <span style='color:#666666'>" . $user_Exist['first_name'] ." ".$user_Exist['last_name'] . "</span>,
                <br/><br/>Your password has been reset successfully.<br/><br/>Please find the following login details:<br/><br/>
                Email: " . $user_Exist['email'] . "<br/>Password: " . $password . "<br/>Mobile No: " . $user_Exist['phone_number'] . "<br/>
                <br/>Thanks, <br/>Support Team";
                 $email = new Email();
				$email->transport('default');
				try {
                $email->from(['m.geniusappdeveloper@gmail.com' => 'Taxi App'])
                ->to($saveArray['email'])
				 ->emailFormat('html')
                ->subject('Forgot your Password!')
                ->send($message);

                $result = $this->Users->patchEntity($user_Exist, $userData);
                $this->Users->save($result);  //update record
				} catch (Exception $e) {
					echo 'Exception : ',  $e->getMessage(), "\n";
				}
                $result = array('status' => '1', 'message' => 'Password sent successfully.','result'=>$password);
            } else {
                $result = array('status' => '0', 'message' => 'User Does not exist.');
            }
        } else {
            $result = array('status' => '0', 'message' =>'Fields are required.');
        }
        echo json_encode($result);
        die;
    }

    #_________________________________________________________________________#

    /**
     * @Date: 18 jan 2018
     * @Method : login
     * @Purpose: This function Forget Password is used to send password to phone number 
     * @Param: none
     * @Return: none 
     * */
    #_____________________________________________________________________________#

    function GetProfile() {
        $saveArray = $this->data;  
        $this->loadModel('Users');
        if (isset($saveArray['user_id'])  && !empty($saveArray['user_id']) ) {
            $condition = [
                'id' => $saveArray['user_id']
            ];
        $user_Exist =   $this->Users->find()->select(['Users.id','Users.email','Users.first_name','Users.last_name','Users.phone_number','Users.country_code','Users.user_type','Users.profile_pic','Users.car_number','Users.make','Users.model','Users.social_security_number','Users.license_info','Users.is_pool','Users.is_online','Users.is_approved','Users.car_capacity','Users.is_verified'])->where($condition)->first(); 
        if (!empty($user_Exist)) {
			if($user_Exist['user_type']=='D'){
				$driver_details =   $this->DriverDetail->find()->select(['city','category_id','brand','model','year','color','interior_color','license_doc','license_number','vehicle_type','issued_on','expiry_date','insurance_doc','prmit_doc','vehicle_registration_doc','police_doc','social_security_doc'])->where(['user_id'=>$saveArray['user_id']])->first();
				if($driver_details ){
				    $user_Exist['city'] = !empty($driver_details['city'])?$driver_details['city']:'';
				    $user_Exist['brand'] = !empty($driver_details['brand'])?$driver_details['brand']:'';
				    $user_Exist['model'] = !empty($driver_details['model'])?$driver_details['model']:"";
				    $user_Exist['year'] = !empty($driver_details['year'])?$driver_details['year']:'';
				    $user_Exist['color'] = !empty($driver_details['color'])?$driver_details['color']:'';
				    $user_Exist['interior_color'] = !empty($driver_details['interior_color'])?$driver_details['interior_color']:"";
				    $user_Exist['license_number'] = !empty($driver_details['license_number'])?$driver_details['license_number']:"";
				    $user_Exist['vehicle_type'] = !empty($driver_details['vehicle_type'])?$driver_details['vehicle_type']:'';
				    $user_Exist['issued_on'] = !empty($driver_details['issued_on'])?date('Y-m-d',strtotime($driver_details['issued_on'])):'';
				    $user_Exist['expiry_date'] = !empty($driver_details['expiry_date'])?date('Y-m-d',strtotime($driver_details['expiry_date'])):'';
					$user_Exist['license_doc'] = ($driver_details['license_doc'])? Router::url('/', true) . "img/driver_info/" . $driver_details['license_doc']:"";
					$user_Exist['insurance_doc'] = ($driver_details['insurance_doc'])? Router::url('/', true) . "img/driver_info/" . $driver_details['insurance_doc']:"";
					$user_Exist['prmit_doc'] = ($driver_details['prmit_doc'])? Router::url('/', true) . "img/driver_info/" . $driver_details['prmit_doc']:"";
					$user_Exist['vehicle_registration_doc'] = ($driver_details['vehicle_registration_doc'])? Router::url('/', true) . "img/driver_info/" . $driver_details['vehicle_registration_doc']:"";
					$user_Exist['police_doc'] = ($driver_details['police_doc'])? Router::url('/', true) . "img/driver_info/" . $driver_details['police_doc']:"";
					$user_Exist['social_security_doc'] = ($driver_details['social_security_doc'])? Router::url('/', true) . "img/driver_info/" . $driver_details['social_security_doc']:"";
					
				}
			}
            $user_Exist['profile_pic'] = ($user_Exist['profile_pic'])? Router::url('/', true) . "img/profile_images/" . $user_Exist['profile_pic']:Router::url('/', true) . "img/user_default.png" ;
			$user_Exist['car_capacity'] = ($user_Exist['car_capacity'])?$user_Exist['car_capacity']:"";
			$user_Exist['license_info'] = ($user_Exist['license_info'])? Router::url('/', true) . "img/driver_info/" . $user_Exist['license_info']:"";
             if($user_Exist['is_verified'] == "Y"){
                       $user_Exist['otp']="Yes";
                     }else{
                        $user_Exist['otp']="";
                     }

                $result = array('status' => '1', 'message' => 'Profile detail.','result'=>$user_Exist);
            } else {
                $result = array('status' => '0', 'message' => 'User Does not exist.');
            }
        } else {
            $result = array('status' => '0', 'message' =>'Fields are required.');
        }
        echo json_encode($result);
        die;

    }
	#_________________________________________________________________________#

    /**
     * @Date: 14 jan 2018
     * @Method : Edit Profile
     * @Purpose: This function Forget Password is used to edit profile
     * @Param: none
     * @Return: none 
     * */
    #_____________________________________________________________________________#
	
    function EditProfile(){
        $saveArray = $this->data;  
        $this->loadModel('Users');
		$saveArray['unique_code']='';
        if (isset($saveArray['user_id'])  && !empty($saveArray['user_id']) ) {
		$user_Exist = $this->Users->find()->select(['id','email','first_name','last_name','phone_number','country_code','user_type'])->where(['id' => $saveArray['user_id']])->first(); 
        if (!empty($user_Exist)) {
				$destination = WWW_ROOT . 'img/profile_images/'; 
				$condition = [
					'id' => $saveArray['user_id']
				];
				if(isset($saveArray['email']) && !empty($saveArray['email'])){
				
					$email_Exist =   $this->Users->find()->select(['id','email','first_name','last_name','phone_number','country_code','user_type','profile_pic','latitude','longitude'])->where(['email' => $saveArray['email'],'id !=' => $saveArray['user_id']])->first();
					if($email_Exist){
						$result = array('status' => '0', 'message' => 'Email already Exist.');
						echo json_encode($result);die;
					}
				}
				if(isset($saveArray['phone_number']) && !empty($saveArray['phone_number'])){
					$phone_Exist =   $this->Users->find()->select(['id','email','first_name','last_name','phone_number','country_code','user_type','profile_pic','latitude','longitude'])->where(['phone_number' => $saveArray['phone_number'],'id !=' => $saveArray['user_id']])->first();					
					if($phone_Exist){
						$result = array('status' => '0', 'message' => 'Phone number already Exist.');
						echo json_encode($result);die;
					}else{
						$this->loadComponent('Twilio');
						$saveArray['unique_code'] = rand('1111','9999');
						$sms = 'Welcome to Kungo App!! Your Verification code is '.$saveArray['unique_code'];
						$response = $this->Twilio->sms("+12243343502", $saveArray['country_code'].$saveArray['phone_number'],$sms); //$from, $to, $message //+1 224-334-3502
						//dump($response);
					}
				}	
				
				if(isset($_FILES['image']) && !empty($_FILES['image'])){
				$saveArray['profile_pic'] = $this->uploadPic('',$_FILES['image'],$saveArray['user_id'], $destination);
				}
				if(isset($_FILES['license_info']) && !empty($_FILES['license_info'])){
				$saveArray['license_info'] = $this->uploadPic('',$_FILES['license_info'],$saveArray['user_id'], WWW_ROOT . 'img/driver_info/');
				}
			   /*  $this->Users->query()->update()
				->set(['profile_pic' => $filename])
				->where($condition)
				->execute(); */
				if(isset($saveArray['car_capacity']) && !empty($saveArray['car_capacity'])){
					$saveArray['available_seats']= 4;
				}
				$userData = $this->Users->patchEntity($user_Exist, $saveArray);
				$this->Users->save($userData);  //update record
				$saveArray['profile_pic'] = isset($saveArray['profile_pic'])?(!empty($saveArray['profile_pic'])? Router::url('/', true) . "img/profile_images/" . $saveArray['profile_pic']:""):"";
				$result = array('status' => '1', 'message' => 'Profile Updated successfully.','otp'=>$saveArray['unique_code'],'image'=>$saveArray['profile_pic']);
			}else{
					$result = array('status' => '0', 'message' =>'User Not Found.');
				}
        }else{
            $result = array('status' => '0', 'message' =>'Fields are required.');
        }
        echo json_encode($result);
        die;
    }
	#_________________________________________________________________________#

    /**
     * @Date: 14 jan 2018
     * @Method : fareEstimation
     * @Purpose: This function Forget Password is used to fare Estimation
     * @Param: none
     * @Return: none 
     * */
    #_____________________________________________________________________________#
    function fareEstimation() {
        $saveArray = $this->data;  
		$result=[];
        $this->loadModel('Users');
        if (isset($saveArray['user_id'])  && !empty($saveArray['user_id']) && isset($saveArray['source_lat'])  && !empty($saveArray['source_lat']) && isset($saveArray['source_lng'])  && !empty($saveArray['source_lng']) && isset($saveArray['dest_lat'])  && !empty($saveArray['dest_lat']) && isset($saveArray['dest_lng'])  && !empty($saveArray['dest_lng'])) {
            $source_latitude=$saveArray['source_lat'];
            $source_longitude=$saveArray['source_lng'];
            $dest_latitude=$saveArray['dest_lat'];
            $dest_longitude=$saveArray['dest_lng'];
            $arr = $this->Common->GetDrivingDistance(array('lat' => $source_latitude, 'long' => $source_longitude), array('lat' => $dest_latitude, 'long' => $dest_longitude));
			//Personal , Plus ,pool [later] , Premier [later]
			$charge_ac_to_cat =['Personal'=> 5,'Plus'=> 6];	
			$query = $this->Category->find()->where(['status' => 'Y','is_deleted'=>'N']);
			$charge_ac_to_cat = $query->all();	
			$peronal_charge_per_miles = 5;
			$plus_charge_per_miles = 6;
			foreach($charge_ac_to_cat as $n=>$cat_carge){
			 $fare_est = ($arr['distance_in_miles'] * $cat_carge['per_mile']);			 
			 $result[$cat_carge['category_name']] = "$".round($fare_est, 2);			 
			}
			$result['time_in_min'] = ceil($arr['durantion_in_min']);
			$result['source_lat'] =  $saveArray['source_lat'];
			$result['source_lng'] =  $saveArray['source_lng'];
			$result['dest_lat'] =  $saveArray['dest_lat'];
			$result['dest_lng'] =  $saveArray['dest_lng'];
            $result = array('status' => '1', 'message' => 'Fare estimation.','result'=>$result);
        }else {
            $result = array('status' => '0', 'message' =>'Fields are required.');
        }
        echo json_encode($result);
        die; 
    }
	
	 #_________________________________________________________________________#

    /**
     * @Date: 14 jan 2018
     * @Method : get_near_driver
     * @Purpose: This function Forget Password is used to get near driver
     * @Param: none
     * @Return: none 
     * */
    #_____________________________________________________________________________#
	
    function GetNearDriver() {
        $saveArray = $this->data;
         if (isset($saveArray['user_id'])  && !empty($saveArray['user_id'])){ 
			$condition = [
                'id' => $saveArray['user_id']
            ];
			$user_Exist =   $this->Users->find()->select(['id','email','first_name','last_name','phone_number','country_code','user_type','profile_pic','latitude','longitude'])->where($condition)->first();
            if (!empty($user_Exist)) {			
				$latitude = $user_Exist['latitude'];
                $longitude = $user_Exist['longitude'];
				$id = $user_Exist['id'];
				$distance= 2;
				$conn = ConnectionManager::get('default');			
				$query = "SELECT id,first_name,last_name,latitude,longitude, SQRT( POW( 69.1 * (latitude - $latitude ) , 2 ) + POW( 69.1 * ( $longitude - longitude ) * COS(latitude / 57.3 ) , 2 ) ) AS distance FROM users where $id NOT IN (id) && user_type='D' && is_online='Y' && is_verified='Y' && is_Active='Y' && is_approved='Y' && availability_status='Y' && is_deleted='N' && latitude !='' && longitude != '' HAVING distance < $distance";				
				 $result=$conn->execute($query)->fetchAll('assoc');
				$result = array('status' => '1', 'message' => 'Near Drivers.','result'=>$result);
				}else{
					$result = array('status' => '0', 'message' =>'User Not Found.');
				}
			}else {
            $result = array('status' => '0', 'message' =>'User Id is required.');
			}
		 echo json_encode($result);
        die; 		
	}
	
	#_________________________________________________________________________#		
    /**
     * @Date: 14 jan 2018
     * @Method : Send_Request
     * @Purpose: This function Forget Password is used to send request to near driver
     * @Param: none
     * @Return: none 
     * */
    #_____________________________________________________________________________#
	function SendRequest() {
			$saveArray = $this->data;
			if (isset($saveArray['user_id'])  && !empty($saveArray['user_id']) && isset($saveArray['source_lat'])  && !empty($saveArray['source_lat']) && isset($saveArray['source_lng'])  && !empty($saveArray['source_lng']) && isset($saveArray['dest_lat'])  && !empty($saveArray['dest_lat']) && isset($saveArray['dest_lng'])  && !empty($saveArray['dest_lng'])) { 
				$user_Exist =   $this->Users->find()->select(['id','email','first_name','last_name','phone_number','country_code','user_type','profile_pic','latitude','longitude','device_token','device_type'])->where(['id' => $saveArray['user_id'],'user_type'=>'U'])->first();
				if (!empty($user_Exist)) {
				     $id = $saveArray['user_id'];
					$latitude = $saveArray['source_lat'];
					$longitude = $saveArray['source_lng'];
					$distance= 20;
					$conn = ConnectionManager::get('default');			
					$query = "SELECT id,first_name,last_name,device_token,device_type,latitude,longitude,user_type, SQRT( POW( 69.1 * (latitude - $latitude ) , 2 ) + POW( 69.1 * ( $longitude - longitude ) * COS(latitude / 57.3 ) , 2 ) ) AS distance FROM users where $id NOT IN (id) && user_type='D' && is_online='Y' && is_verified='Y' && is_Active='Y' && is_approved='Y' && availability_status='Y' && is_deleted='N' && latitude !='' && longitude != '' HAVING distance < $distance";	
					$drivers_found=$conn->execute($query)->fetchAll('assoc');
					$driver_count = count($drivers_found);
					if(isset($saveArray['date_time'])  && !empty($saveArray['date_time'])){
						$saveArray['ride_status']='S';
						$requestride_date =  new Time($saveArray['date_time']);
						$current_date = Time::now();
						//$twohourlater_date = date('Y-m-d H:i:s',strtotime('+2 hours', $request_date->getTimestamp()));						
						$interval = $requestride_date->diff($current_date);
						$hours = $interval->format('%h');
						$day = $interval->format('%d');
						if($day >=0 && $hours > 2){
						$saveArray['decline_time'] = date('Y-m-d H:i:s',strtotime('+2 hours', $current_date->getTimestamp()));
						}else{
						 $saveArray['decline_time'] = date('Y-m-d H:i:s',strtotime('+15 minutes', $current_date->getTimestamp()));	
						}
						//dump($interval);
						//dump($saveArray['decline_time']);
						//dump($current_date);die;
						$data=$this->Pickup->newEntity($saveArray);
						$pickup = $this->Pickup->save($data);
						if($pickup){
						$result = array('status' => '1', 'message' => 'Success','request_id' => $pickup['id']);
						echo json_encode($result);die;	
						}else{
							$result = array('status' => '0', 'message' => 'Failed');
						echo json_encode($result);die;	
						}
						
					}
					
					if($drivers_found){					
						$data=$this->Pickup->newEntity($saveArray);
						$pickup = $this->Pickup->save($data);
						if($pickup){						
							$count =0;$total_distance=00.00;
							$total_fare =00.00;
							$surcharge =00.00;
							$minutes =0;
							if(isset($saveArray['category_id'])){		
									$cat_carge = $this->Category->find()->where(['status' => 'Y','is_deleted'=>'N','id'=>$saveArray['category_id']])->first();
									$arr = $this->Common->GetDrivingDistance(array('lat' => $saveArray['source_lat'], 'long' => $saveArray['source_lng']), array('lat' => $saveArray['dest_lat'], 'long' => $saveArray['dest_lng']));
									$minutes = round($arr['durantion_in_min']);
									//Personal , Plus ,pool [later] , Premier [later]
										$fare_est = ($arr['distance_in_miles'] * $cat_carge['per_mile']);									
										$fare = round($fare_est, 2);
										$charge_data = $this->Charge->find()->where(['id' =>1])->first();
										$surcharge = $fare * $charge_data['value'] /100;
										$surcharge = round($surcharge, 2);	
										$total_fare = round($fare + $surcharge,2);
										$total_distance =round($arr['distance_in_miles'],2);
								}
							foreach($drivers_found as $n=>$drivers){
								$device_token = '';
								$request['user_id']= $saveArray['user_id'];
								$request['driver_id']= $drivers['id'];
								$request['request_id']= $pickup['id'];
								$request_data= $this->PickupRequest->newEntity($request);
								$this->PickupRequest->save($request_data);
								//user information details
								$username = $user_Exist['first_name'] . " " . $user_Exist['last_name'];
								$request_id= $pickup['id'];
								$user_image = ($user_Exist['profile_pic'])? Router::url('/', true) . "img/profile_images/" . $user_Exist['profile_pic']:Router::url('/', true) . "img/user_default.png";
								$mobile =  $user_Exist['country_code'] . " " . $user_Exist['phone_number'];
								$message = array(
									'message'=>" Recieved new request ",
									'source_latitude' => $latitude, 
									'source_longitude' => $longitude,
									'source_addresss'=>$saveArray['source_location'],
									'request_id' => $request_id,
									'image' => $user_image, 
									'username' => $username,
									'mobile' => $mobile,
									'destination_latitude' => $saveArray['dest_lat'],
									'destination_longitude' => $saveArray['dest_lng'],
									'destination_addresss' => $saveArray['dest_location'],
									'seatings' => "",										
									'noti_to' =>"Driver",
									'duration'=>$minutes,
									'surcharge'=>$surcharge,
									'charges'=>$total_fare,
									'request_status'=>'Arrived',
									'total_distance'=>$total_distance,
									'user_id'=>$saveArray['user_id'],
									'noti_type' =>"PR"
								);
								$device_token[] = $drivers['device_token'];
								 if($drivers['device_type'] == 'A'){							 
									$notification = $this->Common->android_send_notification($device_token,$message, $drivers['user_type']);
								 }else{
									$notification =  $this->Common->iphone_send_notification($device_token,$message, $drivers['user_type']);
								 }
								 $count++;
							}
						}
					 //$result = array('status' => '1', 'message' => 'Request sent','request_id' => $pickup['id']);
					//check the Request Status
					//$time =  strtotime(date("m/d/Y h:i:s a", time()+10;
					//$later_time =  strtotime(date("m/d/Y h:i:s a", time())); $later_time ==  $time)
					 $a = 1;
					 sleep(5);
					 $chkReqStatus = $this->PickupRequest->find()->select(['id','request_status'])->where(['request_id' =>$pickup['id'],'request_status'=>'A'])->first();
                    if (empty($chkReqStatus)) {
                        $message = array('message' => 'No Driver, please retry.','noti_to' =>"User",'noti_type' =>"ND"); 
						$device_token[]= $user_Exist['device_token'];
						 /* if($user_Exist['device_type']=='A'){
							$this->Common->android_send_notification($device_token, $message, $user_Exist['user_type']);
						 }else{
							$this->Common->iphone_send_notification($device_token,$message,$user_Exist['user_type']); 
						 } */
						 $this->PickupRequest->query()->update()->set(['request_status' => 'D','declined_by' => 'U'])->where(['request_id' => $pickup['id']])->execute();
                        $result = array('status' => '1', 'message' => 'No Driver, please retry.', 'request_id' => $pickup['id']);	
						echo json_encode($result);die;					
                    } else {
                        $message = array('message' => 'Request Accepted, Driver is on the way!!','noti_to' =>"User",'noti_type' =>"RA");
						$device_token[]= $user_Exist['device_token'];
						if($user_Exist['device_type']=='A'){
							//$this->Common->android_send_notification($device_token, $message, $user_Exist['user_type']);
						 }else{
							//$this->Common->iphone_send_notification($device_token, $message, $user_Exist['user_type']); 
						 }
                        $result = array('status' => '1', 'message' => 'Request Accepted, Driver is on the way!!','request_id' => $pickup['id']);
						echo json_encode($result);die;		
                    } 
					//End check request status section
					}else{
						$result = array('status' => '0', 'message' =>'No near by Taxi found, please request in few minutes.','request_id' =>'');
						echo json_encode($result);die;
					}
				}else{
					$result = array('status' => '0', 'message' =>'User Not Found.');
				}
			 }else{
				$result = array('status' => '0', 'message' =>'All fields are required.');
			}			 
			echo json_encode($result);
			die; 
	}
	
	 #_________________________________________________________________________#
	  /**
     * @Date: 18-jan-2018
     * @Method : User Check Response
     * @Purpose: This function is used to check the response of Request by User
     * @Param: none
     * @Return: none 
     * */

    function CheckRequestStatus() {
		 $saveArray = $this->data;
		 if (isset($saveArray['user_id'])  && !empty($saveArray['user_id']) ) { 
				$user_Exist =   $this->Users->find()->select(['id','email','first_name','last_name','phone_number','country_code','user_type','profile_pic','latitude','longitude'])->where(['id' => $saveArray['user_id']])->first();
				if (!empty($user_Exist)) {
						$getRecentRequest = $this->Pickup->find()->where(['user_id' => $saveArray['user_id'],'id'=>$saveArray['request_id']])->order(['id' => 'DESC'])->first(); 
						if (!empty($getRecentRequest)) {
						$getRequestStatus = $this->PickupRequest->find()->select(['PickupRequest.id','PickupRequest.user_id','PickupRequest.request_id','PickupRequest.driver_id','PickupRequest.request_status','PickupRequest.request_process_status','Pickup.category_id','Pickup.payment_mode','Pickup.source_lat','Pickup.source_lng','Pickup.dest_lat','Pickup.dest_lng','Pickup.dest_location','PickupRequest.declined_by','Users.first_name','Users.last_name','Users.latitude','Users.longitude','Driver.first_name','Driver.last_name','Driver.country_code','Driver.phone_number','Driver.profile_pic','Driver.latitude','Driver.longitude','Driver.car_number'])->where(['PickupRequest.request_id' => $saveArray['request_id'],'PickupRequest.user_id' => $saveArray['user_id'],'PickupRequest.request_status'=>'A'])->orWhere(['PickupRequest.request_id' => $saveArray['request_id'],'PickupRequest.user_id' => $saveArray['user_id'],'PickupRequest.request_status' =>'D'])->contain(['Users','Driver','Pickup'])->first();
						$query = $this->Feedback->find()->where(['driver_id'=>$getRequestStatus['driver_id']]);
						$feedback_exist = $query->toArray();
						$averagerating=0;
						if($feedback_exist){
							$totalrating=0;
							foreach($feedback_exist as $feedback){
								$totalrating += $feedback['rating'] ;
							}
							$averagerating = $totalrating/count($feedback_exist);
						}
						if($getRequestStatus){
							if($getRequestStatus['request_status'] == 'A'){
								$Details['message']='';
								if($getRequestStatus['request_process_status'] =='S'){
								$Details['request_process_status']='Started';
								} else if ($getRequestStatus['request_process_status'] == 'C') {
									$Details['request_process_status']='Completed';
								} else if ($getRequestStatus['request_process_status'] == 'A') {
									$Details['request_process_status']='Arrived';
									
								}else {
									$arr = $this->Common->GetDrivingDistance(array('lat' => $getRequestStatus['driver']['latitude'], 'long' => $getRequestStatus['driver']['longitude']), array('lat' =>$getRequestStatus['user']['latitude'], 'long' => $getRequestStatus['user']['longitude']));
									$minutes = round($arr['durantion_in_min']);
									$show_message = "Your booking has been confirmed. Driver will pick-up you in $minutes minutes.";
									$Details['request_process_status'] = 'Not Started yet';
									$Details['message']= $show_message;
								}
							}
							if($getRequestStatus['request_status'] == 'D'){
								$Details['message']='';
								$Details['request_process_status'] = 'declined';
							}
							$Details['request_status'] = $getRequestStatus['request_status'];
							$Details['driver_latitude'] = $getRequestStatus['driver']['latitude'];
                            $Details['driver_longitude'] =$getRequestStatus['driver']['longitude'];
                            $Details['pickup_latitude'] = $getRequestStatus['pickup']['source_lat'];
                            $Details['pickup_longitude'] = $getRequestStatus['pickup']['source_lng'];
							$Details['dest_lat'] = $getRequestStatus['pickup']['dest_lat'];
                            $Details['dest_lng'] = $getRequestStatus['pickup']['dest_lng'];
                            $Details['dest_location'] = $getRequestStatus['pickup']['dest_location'];
                            $Details['username'] = $getRequestStatus['user']['first_name'].' '.$getRequestStatus['user']['last_name'];
                            $Details['request_id'] = $getRequestStatus['request_id'];
                            // $Details['fare_km'] = $val['latitude'];
                           //  $Details['with_surcharge'] = $val['latitude'];
                            //$Details['time_mnt'] = $val['latitude'];							
                            $Details['driver_id'] = $getRequestStatus['driver_id'];
                            $Details['rating'] = $averagerating;
                            $Details['driver_name'] = $getRequestStatus['driver']['first_name'].' '.$getRequestStatus['driver']['last_name'];
                            $Details['driver_mobile'] = $getRequestStatus['driver']['country_code'].$getRequestStatus['driver']['phone_number'];
                            $Details['car_number'] = $getRequestStatus['driver']['car_number'];
							$Details['driver_profile_pic'] = ($getRequestStatus['driver']['profile_pic'])? Router::url('/', true) . "img/profile_images/" . $getRequestStatus['driver']['profile_pic']:Router::url('/', true) . "img/user_default.png" ;
							$result = array('status' => '1', 'message' => 'Your Request accepted.', 'result' => $Details);	
							echo json_encode($result);die; 	
							}else{
								$result = array('status' => '1', 'message' => 'No near by driver Found.', 'result' =>'pending');	
							}
						}else{
							$result = array('status' => '0', 'message' =>'No Request For Pickup.');
						}
					}else{
					$result = array('status' => '0', 'message' =>'User Not Found.');
					}
				}else{
					$result = array('status' => '0', 'message' =>'User Id is required.');
				}
		echo json_encode($result);
		die; 		
	}
	
	 #_________________________________________________________________________#
	  /**
     * @Date: 18-jan-2018
     * @Method : Driver Check Response
     * @Purpose: This function is used to check the response of Request by User
     * @Param: none
     * @Return: none 
     * */

    function CheckDriverRequestStatus() {
		 $saveArray = $this->data;
		 if (isset($saveArray['user_id'])  && !empty($saveArray['user_id']) ) { 
				$user_Exist =   $this->Users->find()->select(['id','email','first_name','last_name','phone_number','country_code','user_type','profile_pic','latitude','longitude'])->where(['id' => $saveArray['user_id']])->first();
				if (!empty($user_Exist)) {
						$getRecentRequest = $this->Pickup->find()->where(['id'=>$saveArray['request_id']])->order(['id' => 'DESC'])->first(); 
						if (!empty($getRecentRequest)) {
						$getRequestStatus = $this->PickupRequest->find()->select(['PickupRequest.id','PickupRequest.user_id','PickupRequest.request_id','PickupRequest.driver_id','PickupRequest.request_status','PickupRequest.request_process_status','Pickup.category_id','Pickup.payment_mode','Pickup.source_lat','Pickup.source_lng','Pickup.dest_lat','Pickup.dest_lng','PickupRequest.declined_by','Users.first_name','Users.last_name','Driver.first_name','Driver.last_name','Driver.country_code','Driver.phone_number','Driver.profile_pic','Driver.latitude','Driver.longitude','Driver.car_number'])->where(['PickupRequest.request_id' => $saveArray['request_id'],'PickupRequest.driver_id' => $saveArray['user_id']])->contain(['Users','Driver','Pickup'])->first();
						//dump($getRequestStatus);
						if($getRequestStatus){
							if($getRequestStatus['request_status'] == 'A'){							
								if($getRequestStatus['request_process_status'] =='S'){
								$Details['request_process_status']='Started';
								} else if ($getRequestStatus['request_process_status'] == 'C') {
									$Details['request_process_status']='Completed';
								} else if ($getRequestStatus['request_process_status'] == 'A') {
									$Details['request_process_status']='Arrived';
								}else {
									$Details['request_process_status'] = 'Not Started yet';
								}
							}
							if($getRequestStatus['request_status'] == 'D'){
								$Details['request_process_status'] = 'declined';
							}
							if($getRequestStatus['request_status'] == 'P'){
								$Details['request_process_status'] = 'pending';
							}
							$Details['request_status'] = $getRequestStatus['request_status'];
							$Details['driver_latitude'] = $getRequestStatus['driver']['latitude'];
                            $Details['driver_longitude'] =$getRequestStatus['driver']['longitude'];
                            $Details['pickup_latitude'] = $getRequestStatus['pickup']['source_lat'];
                            $Details['pickup_longitude'] = $getRequestStatus['pickup']['source_lng'];
							$Details['dest_lat'] = $getRequestStatus['pickup']['dest_lat'];
                            $Details['dest_lng'] = $getRequestStatus['pickup']['dest_lng'];
                            $Details['username'] = $getRequestStatus['user']['first_name'].' '.$getRequestStatus['user']['last_name'];
                            $Details['request_id'] = $getRequestStatus['request_id'];
                            // $Details['fare_km'] = $val['latitude'];
                           //  $Details['with_surcharge'] = $val['latitude'];
                            //$Details['time_mnt'] = $val['latitude'];							
                            $Details['driver_id'] = $getRequestStatus['driver_id'];
                            $Details['driver_name'] = $getRequestStatus['driver']['first_name'].' '.$getRequestStatus['driver']['last_name'];
                            $Details['driver_mobile'] = $getRequestStatus['driver']['country_code'].$getRequestStatus['driver']['phone_number'];
                            $Details['car_number'] = $getRequestStatus['driver']['car_number'];
							$Details['driver_profile_pic'] = ($getRequestStatus['driver']['profile_pic'])? Router::url('/', true) . "img/profile_images/" . $getRequestStatus['driver']['profile_pic']:Router::url('/', true) . "img/user_default.png" ;
							$result = array('status' => '1', 'message' => 'Your Request accepted.', 'result' => $Details);	
							echo json_encode($result);die; 	
							}else{
								$result = array('status' => '1', 'message' => 'No near by driver Found.', 'result' =>'pending');	
							}
						}else{
							$result = array('status' => '0', 'message' =>'No Request For Pickup.');
						}
					}else{
					$result = array('status' => '0', 'message' =>'User Not Found.');
					}
				}else{
					$result = array('status' => '0', 'message' =>'User Id is required.');
				}
		echo json_encode($result);
		die; 		
	}
	
	#_________________________________________________________________________#
    /**
     * @Date: 19-Jan-2018
     * @Method : Request Action
     * @Purpose: This function is used to give accept/decline Request
     * @Param: none
     * @Return: none 
     * */
	#_________________________________________________________________________#
	
    function DriverRequestAction() {
        $saveArray = $this->data;
        if(isset($saveArray['request_id']) && !empty($saveArray['request_id']) && isset($saveArray['driver_id']) && !empty($saveArray['driver_id']) && isset($saveArray['request_status']) && !empty($saveArray['request_status'])) {
           $request_exist = $this->Pickup->find()->where(['Pickup.id' => $saveArray['request_id']])->select(['Pickup.category_id','Pickup.payment_mode','Pickup.source_lat','Pickup.source_lng','Pickup.dest_lat','Pickup.dest_lng','Users.first_name','Users.last_name','Users.phone_number','Users.country_code','Users.profile_pic','Pickup.seatings','Pickup.ride_status','Users.latitude','Users.longitude'])->contain(['Users'])->first();
            if (!empty($request_exist)) {               
				$getRequestStatus = $this->PickupRequest->find()->select(['PickupRequest.id','PickupRequest.user_id','PickupRequest.request_id','PickupRequest.driver_id','PickupRequest.request_status','PickupRequest.request_process_status','Pickup.category_id','Pickup.payment_mode','Pickup.source_lat','Pickup.source_lng','Pickup.dest_lat','Pickup.dest_lng','PickupRequest.declined_by','Users.first_name','Users.last_name','Users.device_token','Users.device_type','Users.user_type','Users.latitude','Users.longitude','Driver.first_name','Driver.last_name','Driver.country_code','Driver.phone_number','Driver.profile_pic','Driver.latitude','Driver.longitude','Driver.car_number','Driver.available_seats','Driver.car_capacity'])->where(['PickupRequest.request_id' => $saveArray['request_id'], 'driver_id' => $saveArray['driver_id']])->contain(['Users','Driver','Pickup'])->first();
                if (!empty($getRequestStatus)) {
						if($getRequestStatus['request_status'] == 'D' && $getRequestStatus['declined_by'] == 'U'){
							$result = array('status' => '0', 'message' => 'Request Declined.');
							echo json_encode($result);  die;
						}
						 if ($saveArray['request_status'] == 'A'){
							 if ($getRequestStatus['request_status'] == 'A'){
								$result = array('status' => '0', 'message' => 'Requested Accepted By Someone Else.');
								echo json_encode($result);  die;
							 }else{
								$this->PickupRequest->query()->update()->set(['request_status' => 'A'])->where(['request_id' => $saveArray['request_id'], 'driver_id' => $saveArray['driver_id']])->execute();
								if($request_exist['ride_status']=='P'){
									$available_seats = $getRequestStatus['driver']['available_seats']-$request_exist['seatings'];
									$this->Users->query()->update()->set(['available_seats' => $available_seats])->where(['id' => $saveArray['driver_id']])->execute();
								}else{
									$this->Users->query()->update()->set(['availability_status' => 'N'])->where(['id' => $saveArray['driver_id']])->execute();
								}
								$driver_pic = ($getRequestStatus['driver']['profile_pic'])? Router::url('/', true) . "img/profile_images/" . $getRequestStatus['driver']['profile_pic']:Router::url('/', true) . "img/user_default.png" ;
								$arr = $this->Common->GetDrivingDistance(array('lat' => $getRequestStatus['driver']['latitude'], 'long' => $getRequestStatus['driver']['longitude']), array('lat' =>$getRequestStatus['user']['latitude'], 'long' => $getRequestStatus['user']['longitude']));
								$minutes = round($arr['durantion_in_min']);
								$show_message = "Your booking has been confirmed. Driver will pick-up you in $minutes minutes.";
								 $message = array(
										'message'=>$show_message,
										'request_id' => $getRequestStatus['request_id'],
										'driver_id' => $getRequestStatus['driver_id'],
										'driver_name' =>$getRequestStatus['driver']['first_name'].$getRequestStatus['driver']['last_name'],
										'driver_image' => $driver_pic,
										'driver_latitude' => $getRequestStatus['driver']['latitude'], 
										'driver_longitude' => $getRequestStatus['driver']['latitude'], 
										'pickup_latitude' => $getRequestStatus['pickup']['source_lat'],
										'pickup_longitude' => $getRequestStatus['pickup']['source_lng'],
										'dest_latitude' => $getRequestStatus['pickup']['dest_lat'],
										'dest_longitude' => $getRequestStatus['pickup']['dest_lng'],	
										'driver_mobile' => $getRequestStatus['driver']['phone_number'], 
										'car_number' => $getRequestStatus['driver']['car_number'],
										'rating' => 4,
										'type' => 'Accept',
										'noti_to'=>"User",
										'noti_type'=>'A'
									);
									$device_token[]=$getRequestStatus['user']['device_token'];
									if($request_exist['ride_status'] != 'S'){
									if($getRequestStatus['user']['device_type'] == 'A'){
										$notification = $this->Common->android_send_notification($device_token,$message, $getRequestStatus['user']['user_type']);
									 }else{
										$notification =  $this->Common->iphone_send_notification($device_token,$message, $getRequestStatus['user']['user_type']);
									 }
									}
									 $result = array('status' => '1', 'message' => 'Requested Accepted.','request_status'=>$saveArray['request_status']); 
									 echo json_encode($result);  die;
								}
						 }else{
							$this->PickupRequest->query()->update()->set(['request_status' => 'D','declined_by' =>'D'])->where(['request_id' => $saveArray['request_id'], 'driver_id' => $saveArray['driver_id']])->execute();
							$this->Users->query()->update()->set(['availability_status' => 'Y'])->where(['id' => $saveArray['driver_id']])->execute();
							$driver_pic = ($getRequestStatus['driver']['profile_pic'])? Router::url('/', true) . "img/profile_images/" . $getRequestStatus['driver']['profile_pic']:Router::url('/', true) . "img/user_default.png" ;
							$message = array(
										'message'=>"Request Declined",
										'request_id' => $getRequestStatus['request_id'],
										'driver_id' => $getRequestStatus['driver_id'],
										'driver_name' =>$getRequestStatus['driver']['first_name'].$getRequestStatus['driver']['last_name'],
										'driver_image' => $driver_pic,
										'driver_latitude' => $getRequestStatus['driver']['latitude'], 
										'driver_longitude' => $getRequestStatus['driver']['latitude'], 
										'pickup_latitude' => $getRequestStatus['pickup']['source_lat'],
										'pickup_longitude' => $getRequestStatus['pickup']['source_lng'], 
										'driver_mobile' => $getRequestStatus['driver']['phone_number'], 
										'car_number' => $getRequestStatus['driver']['car_number'],
										'rating' => 4,
										'type' => 'Decline',
										'noti_to'=>"User",
										'noti_type'=>'D'
									);
									$device_token[]=$getRequestStatus['user']['device_token'];
									if($request_exist['ride_status'] != 'S'){
									if($getRequestStatus['user']['device_type'] == 'A'){
										$notification = $this->Common->android_send_notification($device_token,$message, $getRequestStatus['user']['user_type']);
									 }else{
										$notification =  $this->Common->iphone_send_notification($device_token,$message, $getRequestStatus['user']['user_type']);
									 }
									}
							$result = array('status' => '1', 'message' => 'Requested Declined.','request_status'=>$saveArray['request_status']);
							 echo json_encode($result);  die;
						 }
                } else {
                    $result = array('status' => '0', 'message' => 'Request Declined by User.');
                }
            } else {
                $result = array('status' => '0', 'message' => 'Request Not Found.');
            }
		}else {
                $result = array('status' => '0', 'message' => 'All Fields Required.');
          }		
     	echo json_encode($result);
        die;
		}
		
	
	#_________________________________________________________________________# 
    /**
     * @Date: 19-jan-2018
     * @Method : Pickup Request List
     * @Purpose: This function is used to show the request list for both User and Driver
     * @Param: none
     * @Return: none 
     * */
	#_________________________________________________________________________# 
	
    function PickupRequestList() {
        $saveArray = $this->data;
        if (isset($saveArray['request_id']) && !empty($saveArray['request_id'])) {
			$request_exist = $this->Pickup->find()->where(['Pickup.id' => $saveArray['request_id']])->select(['Pickup.category_id','Pickup.payment_mode','Pickup.source_lat','Pickup.source_lng','Pickup.dest_lat','Pickup.dest_lng','Users.first_name','Users.last_name','Users.phone_number','Users.country_code','Users.profile_pic','Users.latitude','Users.longitude'])->contain(['Users'])->first(); 
            if (!empty($request_exist)) {
				$userDetail['category'] = $request_exist['category'];
				$userDetail['payment_mode'] = $request_exist['payment_mode'];
				$userDetail['source_lat'] = $request_exist['source_lat'];
				$userDetail['source_lng'] = $request_exist['source_lng'];
				$userDetail['dest_lat'] = $request_exist['dest_lat'];
				$userDetail['dest_lng'] = $request_exist['dest_lng'];
				$userDetail['first_name'] = $request_exist['user']['first_name'];
				$userDetail['last_name'] = $request_exist['user']['last_name'];
				$userDetail['country_code'] = $request_exist['user']['country_code'];
				$userDetail['phone_number'] = $request_exist['user']['phone_number'];
				$userDetail['user_profile_pic'] = ($request_exist['user']['profile_pic'])? Router::url('/', true) . "img/profile_images/" . $request_exist['user']['profile_pic']:Router::url('/', true) . "img/user_default.png" ;
                $result = array('status' => '1', 'result' => $userDetail);				
            } else {
                $result = array('status' => '0', 'message' => 'Request not found.');
            }
        } else {
            $result = array('status' => '0', 'message' => 'Please Enter Request ID.');
        }
    	echo json_encode($result);
        die;
	}
	
	#_________________________________________________________________________# 
    /**
     * @Date: 20-jan-2018
     * @Method : Ride Request Process
     * @Purpose: This function is used to Ride requset Process
     * @Param: none
     * @Return: none 
     * */
	#_________________________________________________________________________# 
	function RideRequestProcess() {
			$saveArray = $this->data;
			$show_text= "";
			 if(isset($saveArray['request_id']) && !empty($saveArray['request_id']) && isset($saveArray['driver_id']) && !empty($saveArray['driver_id']) && isset($saveArray['request_process_status']) && !empty($saveArray['request_process_status']) ) {
				$user_Exist =   $this->Users->find()->select(['id','email','first_name','last_name','phone_number','country_code','user_type','profile_pic','latitude','longitude'])->where(['id' => $saveArray['driver_id'],'user_type'=>'D','is_verified'=>'Y'])->first();
				if(!empty($user_Exist)){
					$request_exist = $this->Pickup->find()->where(['id' => $saveArray['request_id']])->first(); 
						if (!empty($request_exist)) {
							$getRequestStatus = $this->PickupRequest->find()->select(['PickupRequest.id','PickupRequest.user_id','PickupRequest.request_id','PickupRequest.driver_id','PickupRequest.request_status','PickupRequest.request_process_status','Pickup.category_id','Pickup.payment_mode','Pickup.source_lat','Pickup.source_lng','Pickup.dest_lat','Pickup.dest_lng','Pickup.source_location','Pickup.dest_location','PickupRequest.declined_by','Users.first_name','Users.last_name','Users.device_token','Users.device_type','Users.user_type','Driver.first_name','Driver.last_name','Driver.country_code','Driver.phone_number','Driver.profile_pic','Driver.latitude','Driver.longitude','Driver.car_number','Driver.category_id'])->where(['PickupRequest.request_id' => $saveArray['request_id'], 'driver_id' => $saveArray['driver_id'],'request_status'=>'A'])->contain(['Users','Driver','Pickup'])->first();
							if($getRequestStatus){
								if ($saveArray['request_process_status'] == 'S' || $saveArray['request_process_status'] == 'A') {
									if($saveArray['request_process_status'] == 'S'){										
										$set= ['request_process_status' => $saveArray['request_process_status'],'source_lat' =>$saveArray['source_lat'],'source_lng' =>$saveArray['source_lng'],'wait_time'=>$saveArray['wait_time']];
									}else{
										$set= ['request_process_status' => $saveArray['request_process_status'],'source_lat' =>$saveArray['source_lat'],'source_lng' =>$saveArray['source_lng']];
									}
									$this->PickupRequest->query()->update()->set($set)->where(['request_id' => $saveArray['request_id'], 'driver_id' => $saveArray['driver_id']])->execute();
									$this->Users->query()->update()->set(['availability_status' => 'N'])->where(['id' => $saveArray['driver_id']])->execute();
									if($saveArray['request_process_status'] == 'A'){
										$show_message = "Driver Arrived.";
										$noti_type ='DA';
										$show_text= "Start Ride";
									}else{
										$show_message = "Ride Started.";
										$noti_type ='RS';	
										$show_text= "Ride Completed";										
									}
									$message = array(
										'message' => $show_message,
										'noti_to'=>"User",
										'noti_type'=> $noti_type,
										'driver_id'=>$saveArray['driver_id'],
										'pickup_latitude' => $getRequestStatus['pickup']['source_lat'],
										'pickup_longitude' => $getRequestStatus['pickup']['source_lng'],
										'dest_latitude' => $getRequestStatus['pickup']['dest_lat'],
										'dest_longitude' => $getRequestStatus['pickup']['dest_lng']
									);
									$device_token[]=$getRequestStatus['user']['device_token'];
									if($getRequestStatus['user']['device_type'] == 'A'){
										$notification = $this->Common->android_send_notification($device_token,$message, $getRequestStatus['user']['user_type']);
									 }else{
										$notification =  $this->Common->iphone_send_notification($device_token,$message, $getRequestStatus['user']['user_type']);
									 }
									 $result = array('status' => '1', 'message' => $show_message,'request_process_status'=>$saveArray['request_process_status'],'display_text'=>$show_text); 
									 echo json_encode($result);  die;
								}else if ($saveArray['request_process_status'] == 'C') {
									$this->PickupRequest->query()->update()->set(['request_process_status' => 'C','dest_lat' =>$saveArray['dest_lat'],'dest_lng' =>$saveArray['dest_lng']])->where(['request_id' => $saveArray['request_id'], 'driver_id' => $saveArray['driver_id']])->execute();
									$this->Users->query()->update()->set(['availability_status' => 'Y','available_seats'=>4])->where(['id' => $saveArray['driver_id']])->execute();
									//estimation for fare
									$arr = $this->Common->GetDrivingDistance(array('lat' => $getRequestStatus['pickup']['source_lat'], 'long' => $getRequestStatus['pickup']['source_lng']), array('lat' => $saveArray['dest_lat'], 'long' => $saveArray['dest_lng']));
									$cat_carge = $this->Category->find()->where(['status' => 'Y','is_deleted'=>'N','id'=>$getRequestStatus['pickup']['category_id']])->first();
									$fare_est = ($arr['distance_in_miles'] * $cat_carge['per_mile']);
									$fare = round($fare_est, 2);
										//"$".round($fare, 2);
									$charge = $this->Charge->find()->where(['id' =>1])->first();
									$surcharge = $fare * $charge['value'] /100;
									$surcharge = round($surcharge, 2);
									$duration =  round($arr['durantion_in_min'],2);
									$this->PickupRequest->query()->update()->set(['fare' => $fare,'with_surcharge' =>$surcharge,'duration' =>$duration])->where(['request_id' => $saveArray['request_id'], 'driver_id' => $saveArray['driver_id']])->execute();
									$message = array(
										'message' => 'Ride Completed', 
										'fare' => $fare,
										'with_surcharge'=>$surcharge,
										'total_amount'=> round($fare + $surcharge,2),
										'source_location'=>$getRequestStatus['pickup']['source_location'],
										'dest_location'=>$getRequestStatus['pickup']['dest_location'],
										'driver_id' => $saveArray['driver_id'],
										'distance_in_miles'=>round($arr['distance_in_miles'],2),
										'duration'=>$duration,
										'noti_to'=>"User",
										'noti_type'=>"RC"
									);
									$device_token[]=$getRequestStatus['user']['device_token'];
									if($getRequestStatus['user']['device_type'] == 'A'){
										$notification = $this->Common->android_send_notification($device_token,$message, $getRequestStatus['user']['user_type']);
									 }else{
										$notification =  $this->Common->iphone_send_notification($device_token,$message, $getRequestStatus['user']['user_type']);
									 }
									 $data = [
										'fare' => $fare,
										'with_surcharge'=>$surcharge,
										'total_amount'=> $fare + $surcharge,
										'request_process_status'=>$saveArray['request_process_status'],
										'distance_in_miles'=>round($arr['distance_in_miles'],2),
										'duration'=>$duration
									 ];
									$result = array('status' => '1', 'message' => 'Ride Completed.','request_process_status'=>$saveArray['request_process_status'],'result'=>$data); 
									echo json_encode($result);  die;
									
								}
							}else{
								$result = array('status' => '0', 'message' => 'Request Declined By User.');
							}
						}else{
							$result = array('status' => '0', 'message' => 'Request Not Found.');
						}
				}else{
					$result = array('status' => '0', 'message' => 'Driver Not Found.');
				}
				
			 }else{
				$result = array('status' => '0', 'message' => 'All Fields Required.');
			 }
			echo json_encode($result);
			die;
		}
	#_________________________________________________________________________#

    /**
     * @Date: 22 jan 2018
     * @Method : Cancel Request By User
     * @Purpose: This function is cancel the request by User
     * @Param: none
     * @Return: none 
     * */
    #_____________________________________________________________________________#
	
	function CancelRequestedRide() {
		$saveArray = $this->data;
		 if(isset($saveArray['request_id']) && !empty($saveArray['request_id']) && isset($saveArray['user_id']) && !empty($saveArray['user_id']) && isset($saveArray['request_status']) && !empty($saveArray['request_status']) ) {
			$user =	$this->Users->find()->where(['id'=>$saveArray['user_id']])->select(['Users.id','Users.first_name','Users.last_name','Users.phone_number','Users.country_code','Users.profile_pic','Users.latitude','Users.longitude','Users.user_type','Users.device_token','Users.availability_status'])->first();
			if($user['user_type']=='U'){
			$request_exist = $this->Pickup->find()->where(['Pickup.id' => $saveArray['request_id'],'Pickup.user_id'=>$saveArray['user_id']])->select(['Pickup.id','Pickup.category_id','Pickup.payment_mode','Pickup.source_lat','Pickup.source_lng','Pickup.dest_lat','Pickup.dest_lng','Pickup.ride_status','Pickup.date_time','Users.first_name','Users.last_name','Users.phone_number','Users.country_code','Users.profile_pic','Users.latitude','Users.longitude','Users.user_type'])->contain(['Users'])->first();
			if($request_exist){
				if($request_exist['ride_status']== 'N' || $request_exist['ride_status']== 'P'){				
				$getRequestStatus = $this->PickupRequest->find()->select(['PickupRequest.id','PickupRequest.user_id','PickupRequest.request_id','PickupRequest.driver_id','PickupRequest.request_status','PickupRequest.request_process_status','Pickup.category_id','Pickup.payment_mode','Pickup.source_lat','Pickup.source_lng','Pickup.dest_lat','Pickup.dest_lng','PickupRequest.declined_by','Pickup.seatings','Users.first_name','Users.last_name','Users.user_type','Driver.first_name','Driver.id','Driver.last_name','Driver.country_code','Driver.phone_number','Driver.profile_pic','Driver.latitude','Driver.longitude','Driver.car_number','Driver.available_seats','Driver.device_token','Driver.device_type','Driver.user_type'])->where(['PickupRequest.request_id' => $saveArray['request_id'], 'PickupRequest.user_id' => $saveArray['user_id'],'PickupRequest.request_status'=>'A'])->contain(['Users','Driver','Pickup'])->first();
				if($getRequestStatus){
					if($getRequestStatus['request_process_status']=='S' || $getRequestStatus['request_process_status']=='C'){
						$result = array('status' => '1', 'message' => 'Request can not be Cancelled By User as ride is started');
						echo json_encode($result);die;
					}
					$message = array('message' => 'User Cancel the Requested Ride','noti_to'=>"Driver",'noti_type'=>"RC",'request_id'=>$saveArray['request_id'],'user_id'=>$saveArray['user_id']);
					$devicetoken[] = $getRequestStatus['driver']['device_token'];
					
					if($getRequestStatus['driver']['device_type'] == 'A'){
						$notification = $this->Common->android_send_notification($devicetoken,$message, $getRequestStatus['driver']['user_type']);
					}else{
						$notification =  $this->Common->iphone_send_notification($devicetoken,$message, $getRequestStatus['driver']['user_type']);
					}
					$this->Users->query()->update()->set(['availability_status' => 'Y','available_seats'=>$getRequestStatus['driver']['availability_status']+$getRequestStatus['pickup']['seatings']])->where(['id' => $getRequestStatus['driver']['id']])->execute();
					$this->PickupRequest->query()->update()->set(['request_status' => 'D','declined_by'=>'U'])->where(['request_id' => $saveArray['request_id']])->execute();
					$result = array('status' => '1', 'message' => 'Request Cancelled By User.');	
				}else{
					$result = array('status' => '0', 'message' => 'Request Is not accepted by any one driver.');	
				}
				}elseif($request_exist['ride_status']== 'S'){
					// for schedule ride cancelation 
					$request_date = new Time($request_exist['date_time']);
					 $agorequest_date = strtotime(date('Y-m-d H:i:s',strtotime('-2 hours', $request_date->getTimestamp())));
					 $current_date = strtotime(Time::now());	
					 if($current_date >= $agorequest_date){	
						$result = array('status' => '1', 'message' => 'Requested Ride can not be Cancelled By User');
						echo json_encode($result);die;
					 }
					$getRequestStatus = $this->PickupRequest->find()->select(['PickupRequest.id','PickupRequest.user_id','PickupRequest.request_id','PickupRequest.driver_id','PickupRequest.request_status','PickupRequest.request_process_status','Pickup.category_id','Pickup.payment_mode','Pickup.source_lat','Pickup.source_lng','Pickup.dest_lat','Pickup.dest_lng','PickupRequest.declined_by','Users.first_name','Users.last_name','Users.user_type','Driver.first_name','Driver.id','Driver.last_name','Driver.country_code','Driver.phone_number','Driver.profile_pic','Driver.latitude','Driver.longitude','Driver.car_number','Driver.device_token','Driver.device_type','Driver.user_type'])->where(['PickupRequest.request_id' => $saveArray['request_id'],'PickupRequest.user_id' => $saveArray['user_id'],'PickupRequest.request_status'=>'A'])->contain(['Users','Driver','Pickup'])->first();
					if($getRequestStatus){
						if($getRequestStatus['request_process_status']=='S' || $getRequestStatus['request_process_status']=='C'){
						$result = array('status' => '1', 'message' => 'Request can not be Cancelled By User as ride is started');
						echo json_encode($result);die;
						}
						$message = array('message' => 'User Cancel the Requested Ride','noti_to'=>"Driver",'noti_type'=>"RC",'request_id'=>$saveArray['request_id'],'user_id'=>$saveArray['user_id']);
						$devicetoken[] = $getRequestStatus['driver']['device_token'];
						
						if($getRequestStatus['driver']['device_type'] == 'A'){
							$notification = $this->Common->android_send_notification($devicetoken,$message, $getRequestStatus['driver']['user_type']);
						}else{
							$notification =  $this->Common->iphone_send_notification($devicetoken,$message, $getRequestStatus['driver']['user_type']);
						}
						$this->Users->query()->update()->set(['availability_status' => 'Y'])->where(['id' => $getRequestStatus['driver']['id']])->execute();
						$this->PickupRequest->query()->update()->set(['request_status' => 'D','declined_by'=>'U'])->where(['request_id' => $saveArray['request_id']])->execute();
						$result = array('status' => '1', 'message' => 'Request Cancelled By User');	
						}else{
							$get_request=$this->PickupRequest->find()->where(['PickupRequest.request_id' => $saveArray['request_id'], 'PickupRequest.user_id' => $saveArray['user_id']])->first();
							if($get_request){
								$this->PickupRequest->query()->update()->set(['request_status' => 'D','declined_by'=>'U'])->where(['request_id' => $saveArray['request_id']])->execute();
							}else{
								$request['user_id']= $saveArray['user_id'];
								$request['driver_id']= '';
								$request['request_id']= $saveArray['request_id'];								
								$request['request_status']= 'D';								
								$request['declined_by']= 'U';								
								$request_data= $this->PickupRequest->newEntity($request);
								$this->PickupRequest->save($request_data);
							}
							$result = array('status' => '1', 'message' => 'Request Cancelled By User.');	
							echo json_encode($result);die;
						}
					
				}
			}else{
				$result = array('status' => '0', 'message' => 'Request not found');	
			}
			}else{
					$request_exist = $this->Pickup->find()->where(['Pickup.id' => $saveArray['request_id']])->select(['Pickup.id','Pickup.category_id','Pickup.payment_mode','Pickup.source_lat','Pickup.source_lng','Pickup.dest_lat','Pickup.dest_lng','Pickup.seatings','Users.first_name','Users.last_name','Users.phone_number','Users.country_code','Users.profile_pic','Users.latitude','Users.longitude','Users.user_type','Users.id','Users.device_token','Users.device_type'])->contain(['Users'])->first();
					if($request_exist){
					$devicetoken[] =$request_exist['user']['device_token'];
					$message = array('message' => 'Driver Cancel the Requested Ride','noti_to'=>"User",'noti_type'=>"CancelR");
					if($request_exist['user']['device_type'] == 'A'){
						$notification = $this->Common->android_send_notification($devicetoken,$message, $request_exist['user']['user_type']);
					}else{
						$notification =  $this->Common->iphone_send_notification($devicetoken,$message, $request_exist['user']['user_type']);
					}
					}
					$this->Users->query()->update()->set(['availability_status' => 'Y','available_seats'=>$user['availability_status']+ $request_exist['seatings']])->where(['id' => $saveArray['user_id']])->execute();
					$this->PickupRequest->query()->update()->set(['request_status' => 'D','declined_by'=>'D'])->where(['request_id' => $saveArray['request_id']])->execute();
					$result = array('status' => '1', 'message' => 'Request Cancelled By Driver.');	
				}
		 }else{
			$result = array('status' => '0', 'message' => 'All Fields Required.');
		 }
		echo json_encode($result);
		die;
	}
	#_________________________________________________________________________# 
    /**
     * @Date: 20-jan-2018
     * @Method : Ride Request Cancel reason By user
     * @Purpose: This function is used to Ride Request Cancel reason By user
     * @Param: none
     * @Return: none 
     * */
	#_________________________________________________________________________# 
	
	function UserRideCancelRequestReason() {
		$saveArray = $this->data;
		 if(isset($saveArray['request_id']) && !empty($saveArray['request_id']) && isset($saveArray['user_id']) && !empty($saveArray['user_id']) && isset($saveArray['cancel_reason']) && !empty($saveArray['cancel_reason'])  && isset($saveArray['driver_id']) && !empty($saveArray['driver_id'])) {
			 $user =	$this->Users->find()->where(['id'=>$saveArray['user_id'],'user_type'=>'U'])->select(['Users.id','Users.first_name','Users.last_name','Users.phone_number','Users.country_code','Users.profile_pic','Users.latitude','Users.longitude','Users.user_type','Users.device_token'])->first();
			if($user){
				$request_exist = $this->Pickup->find()->where(['Pickup.id' => $saveArray['request_id'],'Pickup.user_id'=>$saveArray['user_id']])->select(['Pickup.id','Pickup.category_id','Pickup.payment_mode','Pickup.source_lat','Pickup.source_lng','Pickup.dest_lat','Pickup.dest_lng','Users.first_name','Users.last_name','Users.phone_number','Users.country_code','Users.profile_pic','Users.latitude','Users.longitude','Users.user_type'])->contain(['Users'])->first();
				if($request_exist){
					$this->PickupRequest->query()->update()->set(['cancel_reason' => $saveArray['cancel_reason']])->where(['request_id' => $saveArray['request_id'],'driver_id'=>$saveArray['driver_id']])->execute();
					$result = array('status' => '1', 'message' => 'Cancellation Reason added Successfully.');
				}else{
					$result = array('status' => '0', 'message' => 'Request not found.');	
				}				
			}else{
				$result = array('status' => '0', 'message' => 'User not found.');
			}
		 }else{
			$result = array('status' => '0', 'message' => 'All Fields Required.');
		 }
		echo json_encode($result);
		die;
	}
	#_________________________________________________________________________#

    /**
     * @Date: 22 jan 2018
     * @Method : Transaction
     * @Purpose: This function is used to gget Transaction from User
     * @Param: none
     * @Return: none 
     * */
    #_____________________________________________________________________________#
	
	function Transaction(){
		$saveArray = $this->data;
		 //require_once(ROOT . 'vendor' . DS  . 'Facebook' . DS . 'src' . DS . 'facebook.php');
		if(isset($saveArray['request_id']) && !empty($saveArray['request_id']) && isset($saveArray['user_id']) && !empty($saveArray['user_id']) && isset($saveArray['driver_id']) && !empty($saveArray['driver_id']) ){
						$card_exist = $this->Account->find()->where(['set_default' => 'Y','user_id'=>$saveArray['user_id']])->first();	
						if($card_exist){							
						$request_exist = $this->Pickup->find()->where(['Pickup.id' => $saveArray['request_id'],'Pickup.user_id'=>$saveArray['user_id']])->select(['Pickup.id','Pickup.category_id','Pickup.payment_mode','Pickup.source_lat','Pickup.source_lng','Pickup.dest_lat','Pickup.dest_lng','Users.first_name','Users.last_name','Users.phone_number','Users.country_code','Users.profile_pic','Users.latitude','Users.longitude'])->contain(['Users'])->first();
						if($request_exist){
							$getRequestStatus = $this->PickupRequest->find()->select(['PickupRequest.id','PickupRequest.user_id','PickupRequest.request_id','PickupRequest.driver_id','PickupRequest.request_status','PickupRequest.request_process_status','PickupRequest.fare','PickupRequest.with_surcharge','Pickup.category_id','Pickup.payment_mode','Pickup.source_lat','Pickup.source_lng','Pickup.dest_lat','Pickup.dest_lng','Pickup.source_location','Pickup.dest_location','Pickup.created','PickupRequest.declined_by','Users.first_name','Users.last_name','Users.user_type','Driver.first_name','Driver.last_name','Driver.country_code','Driver.phone_number','Driver.profile_pic','Driver.latitude','Driver.longitude','Driver.car_number','Driver.device_token','Driver.device_type'])->where(['PickupRequest.request_id' => $saveArray['request_id'], 'PickupRequest.user_id' => $saveArray['user_id'],'PickupRequest.request_status'=>'A','PickupRequest.request_process_status'=>'C'])->contain(['Users','Driver','Pickup'])->first();
							if($getRequestStatus){
								$total_amount = $getRequestStatus['fare']+ $getRequestStatus['with_surcharge'];
								$amount = (int) round($total_amount,2);			
						/* Create a merchantAuthenticationType object with authentication details
						retrieved from the constants file */
						$merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
						$merchantAuthentication->setName('3w5t6YBtx6G9');  //  36fQvE77 3w5t6YBtx6G9
						$merchantAuthentication->setTransactionKey('8uv6U8E97J6a9YLd'); // KEY :: Simon //3uN49gDKjC26m3Ly 8uv6U8E97J6a9YLd
						
						// Set the transaction's refId
						$refId = 'ref' . time();

						// Create the payment data for a credit card
						$creditCard = new AnetAPI\CreditCardType();
						$creditCard->setCardNumber($card_exist['card_number']); //4111111111111111
						$creditCard->setExpirationDate($card_exist['expiration_date']); //"2038-12"
						// Set the token specific info
						//$creditCard->setIsPaymentToken(true);
						//$creditCard->setCryptogram("EjRWeJASNFZ4kBI0VniQEjRWeJA=");
							
						$paymentOne = new AnetAPI\PaymentType();
						$paymentOne->setCreditCard($creditCard);
						
						//create a transaction
						$transactionRequestType = new AnetAPI\TransactionRequestType();
						$transactionRequestType->setTransactionType("authCaptureTransaction"); 
						$transactionRequestType->setAmount($amount);
						$transactionRequestType->setPayment($paymentOne);


						$request = new AnetAPI\CreateTransactionRequest();
						$request->setMerchantAuthentication($merchantAuthentication);
						$request->setRefId( $refId);
						$request->setTransactionRequest( $transactionRequestType);
						$controller = new AnetController\CreateTransactionController($request);
						$response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::PRODUCTION);
						
						if ($response != null)
						{
						  if($response->getMessages()->getResultCode() == 'Ok')
						  {
							$tresponse = $response->getTransactionResponse();
							
							if ($tresponse != null && $tresponse->getMessages() != null)   
							{
							 // echo " Transaction Response code : " . $tresponse->getResponseCode() . "\n";
							 // echo "Charge Tokenized Credit Card AUTH CODE : " . $tresponse->getAuthCode() . "\n";
							 // echo "Charge Tokenized Credit Card TRANS ID  : " . $tresponse->getTransId() . "\n";
							 // echo " Code : " . $tresponse->getMessages()[0]->getCode() . "\n"; 
							 // echo " Description : " . $tresponse->getMessages()[0]->getDescription() . "\n";
									$saveArray['amount'] = $getRequestStatus['fare'];
									$saveArray['with_surcharge'] = $getRequestStatus['with_surcharge'];
									$saveArray['total_amount'] = $amount;
									$saveArray['transaction_status'] = 'C';
									$saveArray['transaction_id'] = $tresponse->getTransId();
									$transaction_data=$this->Transaction->newEntity($saveArray);						
									$this->Transaction->save($transaction_data);
									$resultdata['request_id'] = $getRequestStatus['request_id'];
									$resultdata['source_location'] = $getRequestStatus['pickup']['source_location'];
									$resultdata['dest_location'] = $getRequestStatus['pickup']['dest_location'];
									$resultdata['created'] = date('M d, Y h:i A',strtotime($getRequestStatus['pickup']['created']));						//,'getTransId'=>$tresponse->getTransId(),'details'=>$tresponse->getMessages()[0]->getDescription()						
									$result = array('status' => '1', 'message' => 'Transaction Success.','result'=>$resultdata);
							}
							else
							{
							  if($tresponse->getErrors() != null)
							  {
								//echo " Error code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";
								//echo " Error message : " . $tresponse->getErrors()[0]->getErrorText() . "\n";  
								$saveArray['amount'] = $getRequestStatus['fare'];
								$saveArray['with_surcharge'] = $getRequestStatus['with_surcharge'];
								$saveArray['total_amount'] = $amount;
								$saveArray['transaction_status'] = 'N';
								$saveArray['transaction_id'] = '';
								$transaction_data=$this->Transaction->newEntity($saveArray);						
								$this->Transaction->save($transaction_data);	
								$result = array('status' => '0', 'message' => 'Transaction Failure','error'=>$tresponse->getErrors()[0]->getErrorText());	
							  }else{
							   $result = array('status' => '0', 'message' => 'Transaction Failure.');	
							  }
							}
						  }
						  else
						  {
							//echo "Transaction Failed \n";
							$tresponse = $response->getTransactionResponse();
							if($tresponse != null && $tresponse->getErrors() != null)
							{
							 // echo " Error code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";
							 // echo " Error message : " . $tresponse->getErrors()[0]->getErrorText() . "\n";
								$saveArray['amount'] = $getRequestStatus['fare'];
								$saveArray['with_surcharge'] = $getRequestStatus['with_surcharge'];
								$saveArray['total_amount'] = $amount;
								$saveArray['transaction_status'] = 'N';
								$saveArray['transaction_id'] = '';
								$transaction_data=$this->Transaction->newEntity($saveArray);						
								$this->Transaction->save($transaction_data);
							  $result = array('status' => '0', 'message' => 'Transaction Failure.','error'=>$tresponse->getErrors()[0]->getErrorText());	
							}
							else
							{
							  //echo " Error code  : " . $response->getMessages()->getMessage()[0]->getCode() . "\n";
							 // echo " Error message : " . $response->getMessages()->getMessage()[0]->getText() . "\n";
								$saveArray['amount'] = $getRequestStatus['fare'];
								$saveArray['with_surcharge'] = $getRequestStatus['with_surcharge'];
								$saveArray['total_amount'] = $amount;
								$saveArray['transaction_status'] = 'N';
								$saveArray['transaction_id'] = '';
								$transaction_data=$this->Transaction->newEntity($saveArray);						
								$this->Transaction->save($transaction_data);
							    $result = array('status' => '0', 'message' => 'Transaction Failure.','error'=>$response->getMessages()->getMessage()[0]->getText());	
							}
						  }      
						}
						else
						{
							    $saveArray['amount'] = $getRequestStatus['fare'];
								$saveArray['with_surcharge'] = $getRequestStatus['with_surcharge'];
								$saveArray['total_amount'] = $amount;
								$saveArray['transaction_status'] = 'N';
								$saveArray['transaction_id'] = '';
								$transaction_data=$this->Transaction->newEntity($saveArray);						
								$this->Transaction->save($transaction_data);
							   $result = array('status' => '0', 'message' => 'Transaction Failure.');
						}
						
						}else{
								$result = array('status' => '0', 'message' => 'Request Is not accepted by any driver.');	
							}
						}else{
							$result = array('status' => '0', 'message' => 'Request not found.');	
						} 
						}else{
							$result = array('status' => '0', 'message' => 'User Card not Exist.');	
						} 
						//return $response;
			
			
			/* $request_exist = $this->Pickup->find()->where(['Pickup.id' => $saveArray['request_id'],'Pickup.user_id'=>$saveArray['user_id']])->select(['Pickup.id','Pickup.category','Pickup.payment_mode','Pickup.source_lat','Pickup.source_lng','Pickup.dest_lat','Pickup.dest_lng','Users.first_name','Users.last_name','Users.phone_number','Users.country_code','Users.profile_pic','Users.latitude','Users.longitude'])->contain(['Users'])->first();
				if($request_exist){
					$getRequestStatus = $this->PickupRequest->find()->select(['PickupRequest.id','PickupRequest.user_id','PickupRequest.request_id','PickupRequest.driver_id','PickupRequest.request_status','PickupRequest.request_process_status','PickupRequest.fare','PickupRequest.with_surcharge','Pickup.category','Pickup.payment_mode','Pickup.source_lat','Pickup.source_lng','Pickup.dest_lat','Pickup.dest_lng','Pickup.source_location','Pickup.dest_location','Pickup.created','PickupRequest.declined_by','Users.first_name','Users.last_name','Users.user_type','Driver.first_name','Driver.last_name','Driver.country_code','Driver.phone_number','Driver.profile_pic','Driver.latitude','Driver.longitude','Driver.car_number','Driver.device_token','Driver.device_type'])->where(['PickupRequest.request_id' => $saveArray['request_id'], 'PickupRequest.user_id' => $saveArray['user_id'],'PickupRequest.request_status'=>'A','PickupRequest.request_process_status'=>'C'])->contain(['Users','Driver','Pickup'])->first();
					if($getRequestStatus){
					    $total_amount = $getRequestStatus['fare']+ $getRequestStatus['with_surcharge'];
						$amount = (int) round($total_amount,2);
						$data = $this->Common->curl_initialization($amount,$saveArray['stripe_token']);						
						if($data->status == 'succeeded'){
							$saveArray['amount'] = $getRequestStatus['fare'];
							$saveArray['with_surcharge'] = $getRequestStatus['with_surcharge'];
							$saveArray['total_amount'] = $amount;
							$saveArray['transaction_status'] = 'C';
							$saveArray['transaction_id'] = $data->balance_transaction;
							$transaction_data=$this->Transaction->newEntity($saveArray);						
							$this->Transaction->save($transaction_data);
							$resultdata['request_id'] = $getRequestStatus['request_id'];
							$resultdata['source_location'] = $getRequestStatus['pickup']['source_location'];
							$resultdata['dest_location'] = $getRequestStatus['pickup']['dest_location'];
							$resultdata['created'] = date('M d, Y h:i A',strtotime($getRequestStatus['pickup']['created']));	
							$result = array('status' => '1', 'message' => 'Transaction completed successfylly','result'=>$resultdata);
						}else{
						if(isset($data->error->message)){
						$error=$data->error->message;
						}else{
						$error='';
						}
							$saveArray['amount'] = $getRequestStatus['fare'];
							$saveArray['with_surcharge'] = $getRequestStatus['with_surcharge'];
							$saveArray['total_amount'] = $amount;
							$saveArray['transaction_status'] = 'N';
							$saveArray['transaction_id'] = '';
							$transaction_data=$this->Transaction->newEntity($saveArray);						
							$this->Transaction->save($transaction_data);
							$result = array('status' => '0', 'message' => 'Transaction failure','error'=>$error);
						}
					}else{
						$result = array('status' => '0', 'message' => 'Request Is not accepted by any driver');	
					}
				}else{
					$result = array('status' => '0', 'message' => 'Request not found');	
				} */
		}else{
			$result = array('status' => '0', 'message' => 'All Fields Required.');
		}
		echo json_encode($result);
		die;
	}
	
	#_________________________________________________________________________#

    /**
     * @Date: 22 jan 2018
     * @Method : Driver feedback
     * @Purpose: This function is used to give the feedback to driver
     * @Param: none
     * @Return: none 
     * */
    #_____________________________________________________________________________#
	
		function feedback(){
			$saveArray= $this->data;
			if(isset($saveArray['request_id']) && !empty($saveArray['request_id']) && isset($saveArray['user_id']) && !empty($saveArray['user_id']) && isset($saveArray['driver_id']) && !empty($saveArray['driver_id']) && isset($saveArray['rating']) && !empty($saveArray['rating'])){				
				$request_exist = $this->Pickup->find()->where(['Pickup.id' => $saveArray['request_id']])->select(['Pickup.id','Pickup.category_id','Pickup.payment_mode','Pickup.source_lat','Pickup.source_lng','Pickup.dest_lat','Pickup.dest_lng','Users.first_name','Users.last_name','Users.phone_number','Users.country_code','Users.profile_pic','Users.latitude','Users.longitude'])->contain(['Users'])->first();
				if($request_exist){
					if (($saveArray['rating'] < 6) && ($saveArray['rating'] > 0)) {
						$getRequestStatus = $this->PickupRequest->find()->select(['PickupRequest.id','PickupRequest.user_id','PickupRequest.request_id','PickupRequest.driver_id','PickupRequest.request_status','PickupRequest.request_process_status','Pickup.category_id','Pickup.payment_mode','Pickup.source_lat','Pickup.source_lng','Pickup.dest_lat','Pickup.dest_lng','PickupRequest.declined_by','Users.first_name','Users.last_name','Users.user_type','Driver.first_name','Driver.last_name','Driver.country_code','Driver.phone_number','Driver.profile_pic','Driver.latitude','Driver.longitude','Driver.car_number','Driver.device_token','Driver.device_type'])->where(['PickupRequest.request_id' => $saveArray['request_id'],'PickupRequest.request_status'=>'A','PickupRequest.request_process_status'=>'C'])->contain(['Users','Driver','Pickup'])->first();
						if($getRequestStatus){
							$feedback_exist = $this->Feedback->find()->where(['request_id' => $saveArray['request_id'],'user_id'=>$saveArray['user_id']])->first();
							if(!$feedback_exist){
								$data=$this->Feedback->newEntity($saveArray);
								$this->Feedback->save($data);
							}else{
								 $data = $this->Feedback->patchEntity($feedback_exist,$saveArray);
								 $this->Feedback->save($data);  //update record
							}
							$result = array('status' => '1', 'message' => 'Feedback added successfully.');
						}else{
							$result = array('status' => '0', 'message' => 'Ride is not completed yet.');
						}
					}else{
						$result = array('status' => '0', 'message' => 'Enter valid Rating.');
					}
				}else{
					$result = array('status' => '0', 'message' => 'Request not found.');	
				}
			}else{
				$result = array('status' => '0', 'message' => 'All Fields Required.');
			}
			echo json_encode($result);
			die;
		}
	#_________________________________________________________________________#

    /**
     * @Date: 22 jan 2018
     * @Method : Show Online /Offline Driver
     * @Purpose: This function is used to show the driver online /offline
     * @Param: none
     * @Return: none 
     * */
    #_____________________________________________________________________________#
	
	function showOnline(){
		$saveArray = $this->data;
		if(isset($saveArray['driver_id']) && !empty($saveArray['driver_id']) && isset($saveArray['is_online']) && !empty($saveArray['is_online'])){
			$user_Exist =   $this->Users->find()->select(['id','email','first_name','last_name','phone_number','country_code','user_type','profile_pic','latitude','longitude'])->where(['id' => $saveArray['driver_id'],'user_type'=>'D','is_verified'=>'Y'])->first();
				if(!empty($user_Exist)){
					$this->Users->query()->update()->set(['is_online' => $saveArray['is_online']])->where(['id' => $saveArray['driver_id']])->execute();					
					$result = array('status' => '1', 'message' => 'Status Updated Successfully.','result'=>$saveArray['is_online']);
				}else{
					$result = array('status' => '0', 'message' => 'Driver Not Found.');
				}
		}else{
			$result = array('status' => '0', 'message' => 'All Fields Required.');
		}
		echo json_encode($result);
		die;
	}
	#_________________________________________________________________________#

    /**
     * @Date: 22 jan 2018
     * @Method : Show pool  Driver
     * @Purpose: This function is used to show the driver online /offline
     * @Param: none
     * @Return: none 
     * */
    #_____________________________________________________________________________#
	
	function showPool(){
		$saveArray = $this->data;
		if(isset($saveArray['driver_id']) && !empty($saveArray['driver_id']) && isset($saveArray['is_pool']) && !empty($saveArray['is_pool'])){
			$user_Exist =   $this->Users->find()->select(['id','email','first_name','last_name','phone_number','country_code','user_type','profile_pic','latitude','longitude'])->where(['id' => $saveArray['driver_id'],'user_type'=>'D','is_verified'=>'Y'])->first();
				if(!empty($user_Exist)){
					$this->Users->query()->update()->set(['is_pool' => $saveArray['is_pool']])->where(['id' => $saveArray['driver_id']])->execute();					
					$result = array('status' => '1', 'message' => 'Status Updated Successfully.','result'=>$saveArray['is_pool']);
				}else{
					$result = array('status' => '0', 'message' => 'Driver Not Found.');
				}
		}else{
			$result = array('status' => '0', 'message' => 'All Fields Required.');
		}
		echo json_encode($result);
		die;
	}
		
	 #_________________________________________________________________________#

    /**
     * @Date: 26 jan 2018
     * @Method : Ride History For Driver End
     * @Purpose: This function is used to show the history of rides take by users
     * @Param: none
     * @Return: none 
     * */
    #_____________________________________________________________________________#
	
	function driverRidesHistory(){
		 $saveArray = $this->data;
		 if(isset($saveArray['driver_id']) && !empty($saveArray['driver_id'])){
			$user = $this->Users->find()->where(['Users.id' => $saveArray['driver_id'],'Users.user_type' => 'D'])->first();
			if(!empty($user)){
			$req = $this->PickupRequest->find()->where(['PickupRequest.driver_id' => $saveArray['driver_id'],'PickupRequest.request_status'=>'A'])->select(['PickupRequest.id','PickupRequest.fare','PickupRequest.with_surcharge','Users.first_name','Users.last_name','Users.phone_number','Users.country_code','Users.profile_pic','Users.latitude','Users.longitude','Pickup.id' ,'Pickup.source_location','Pickup.dest_location' , 'Pickup.created','PickupRequest.request_status'])->contain(['Users','Pickup'])->order(['Pickup.id' => 'DESC']);			
			foreach($req as $req){				
			 $old_date = strtotime($req['pickup']['created']);
			 $new = date('M d, Y h:i A', $old_date);
			 $record[] = ['username' => $req['user']['first_name'] . ' ' . $req['user']['last_name'] , 
			              'user_image' => !empty($req['user']['profile_pic']) ?Router::url('/', true) . "img/profile_images/" . $req['user']['profile_pic'] :Router::url('/', true) . "img/user_default.png" ,
						  'bill' => $req['fare']  +  $req['with_surcharge'],
						  'request_status' => $req['request_status'],
						  'date' => $new,
						  'pickup_location' => $req['pickup']['source_location'],
						   'pickup_destination' => $req['pickup']['dest_location']
						  
						  ];
						 
			}
			$result = ['status' => '1' , 'message' => 'Driver Rides History.' , 'result' => $record];
			}else{
			
			 $result = array('status' => '0', 'message' => 'Invalid Driver ID.');
			}
			
		 }else{
			 $result = array('status' => '0', 'message' => 'All Fields Required.');
		 }
		 echo json_encode($result);
		 die;
	}	
	
	 #_________________________________________________________________________#

    /**
     * @Date: 26 jan 2018
     * @Method : User Ride History For Driver End
     * @Purpose: This function is used to show the user history of rides take by users
     * @Param: none
     * @Return: none 
     * */
    #_____________________________________________________________________________#
	
	function UserRidesHistory(){
		 $saveArray = $this->data;
		 $record=[];
		 if(isset($saveArray['user_id']) && !empty($saveArray['user_id'])){
			$user = $this->Users->find()->where(['Users.id' => $saveArray['user_id'],'Users.user_type' => 'U'])->first();
			if(!empty($user)){
			$req = $this->PickupRequest->find()->where(['PickupRequest.user_id' => $saveArray['user_id'],'PickupRequest.request_process_status'=>'C'])->select(['PickupRequest.id','PickupRequest.fare','PickupRequest.with_surcharge','PickupRequest.request_process_status','Users.first_name','Users.last_name','Users.phone_number','Users.country_code','Users.profile_pic','Users.latitude','Users.longitude','Pickup.id' ,'Pickup.source_location','Pickup.dest_location' , 'Pickup.created','Driver.first_name','Driver.last_name','Driver.country_code','Driver.phone_number','Driver.profile_pic','Driver.latitude','Driver.longitude','Driver.car_number','Driver.device_token','Driver.device_type' ])->contain(['Users','Pickup','Driver'])->order(['Pickup.id' => 'DESC']);
			
			if($req){
			foreach($req as $req){
				//dump($req);
			 $old_date = strtotime($req['pickup']['created']);
			 $new = date('M d, Y h:i A', $old_date);
			 $record[] = ['username' => $req['user']['first_name'] . ' ' . $req['user']['last_name'] , 
						  'drivername' => $req['driver']['first_name'] . ' ' . $req['driver']['last_name'] , 
			              'car_number' => $req['driver']['car_number'] , 
			              'driver_image' => !empty($req['driver']['profile_pic']) ?Router::url('/', true) . "img/profile_images/" . $req['driver']['profile_pic'] :Router::url('/', true) . "img/user_default.png" ,
						  'bill' => round($req['fare']  +  $req['with_surcharge'],2),
						  'date' => $new,
						  'pickup_location' => $req['pickup']['source_location'],
						  'pickup_destination' => $req['pickup']['dest_location'],
						  'pickup_status' => $req['request_process_status']						  
						  ];
						 
			}
			}
			$result = ['status' => '1' , 'message' => 'Driver Rides History.' , 'result' => $record];
			}else{
			
			 $result = array('status' => '0', 'message' => 'Invalid Driver ID.');
			}
			
		 }else{
			 $result = array('status' => '0', 'message' => 'All Fields Required.');
		 }
		 echo json_encode($result);
		 die;
	}
		
	#_________________________________________________________________________#

    /**
     * @Date: 26 jan 2018
     * @Method : Schedule Rides For Driver End
     * @Purpose: This function is used to show the history of schedule rides take by users
     * @Param: none
     * @Return: none 
     * */
    #_____________________________________________________________________________#
	
	function ScheduleRides(){
		 $saveArray = $this->data;
		 $record=[];
		 if(isset($saveArray['driver_id']) && !empty($saveArray['driver_id'])){
			$user = $this->Users->find()->where(['Users.id' => $saveArray['driver_id'],'Users.user_type' => 'D'])->first();
			if(!empty($user)){
			$req = $this->PickupRequest->find()->where(['PickupRequest.driver_id' => $saveArray['driver_id'],'Pickup.ride_status'=>'S','PickupRequest.request_status !='=>'D','Pickup.date_time >=' => date('Y-m-d H:i:s')])->select(['PickupRequest.id','PickupRequest.fare','PickupRequest.with_surcharge','Users.first_name','Users.last_name','Users.phone_number','Users.country_code','Users.profile_pic','Users.latitude','Users.longitude','Pickup.id' ,'Pickup.source_lat','Pickup.source_lng','Pickup.source_location','Pickup.dest_lat','Pickup.dest_lng','Pickup.dest_location' ,'Pickup.date_time', 'Pickup.created','Pickup.user_id'  ])->contain(['Users','Pickup'])->order(['Pickup.id' => 'DESC']); //,'DATE(Pickup.date_time)' => 'CURDATE()'
			if($req){		
				foreach($req as $req){
				 $old_date = strtotime($req['pickup']['date_time']);
				 $new = date('M d, Y h:i A', $old_date);
				 $record[] = [
							  'user_id'=>$req['pickup']['user_id'],
							  'username' => $req['user']['first_name'] . ' ' . $req['user']['last_name'] , 
							  'user_image' => !empty($req['user']['profile_pic']) ?Router::url('/', true) . "img/profile_images/" . $req['user']['profile_pic'] :Router::url('/', true) . "img/user_default.png" ,
							  'bill' => $req['fare']  +  $req['with_surcharge'],
							  'date' => $new,
							  'source_lat' => $req['pickup']['source_lat'],
							  'source_lng' => $req['pickup']['source_lng'],
							  'pickup_location' => $req['pickup']['source_location'],
							  'dest_lat' => $req['pickup']['dest_lat'],
							  'dest_lng' => $req['pickup']['dest_lng'],
							   'pickup_destination' => $req['pickup']['dest_location'],
							   'request_id' => $req['pickup']['id']
							  
							  ];
							 
				}
			}
			$result = ['status' => '1' , 'message' => 'Driver Schedule Rides History.' , 'result' => $record];
			}else{
			
			 $result = array('status' => '0', 'message' => 'Invalid Driver ID.');
			}
			
		 }else{
			 $result = array('status' => '0', 'message' => 'All Fields Required.');
		 }
		 echo json_encode($result);
		 die;
	}		
	
	#_________________________________________________________________________#

    /**
     * @Date: 23 Mrach 2018
     * @Method : Schedule Rides For User End
     * @Purpose: This function is used to show the history of schedule rides take by users
     * @Param: none
     * @Return: none 
     * */
    #_____________________________________________________________________________#
	
	function UserScheduleRides(){
		 $saveArray = $this->data;
		 $record=[];
		 if(isset($saveArray['user_id']) && !empty($saveArray['user_id'])){
			$user = $this->Users->find()->where(['Users.id' => $saveArray['user_id'],'Users.user_type' => 'U'])->first();
			if(!empty($user)){
			$req = $this->Pickup->find()->where(['Pickup.user_id' => $saveArray['user_id'],'Pickup.ride_status'=>'S','Pickup.date_time >=' => date('Y-m-d H:i:s')])->select(['Users.first_name','Users.last_name','Users.phone_number','Users.country_code','Users.profile_pic','Users.latitude','Users.longitude','Pickup.id' ,'Pickup.source_lat','Pickup.source_lng','Pickup.source_location','Pickup.dest_lat','Pickup.dest_lng','Pickup.dest_location' ,'Pickup.date_time', 'Pickup.created','Pickup.user_id'])->contain(['Users'])->order(['Pickup.id' => 'DESC']); //,'DATE(Pickup.date_time)' => 'CURDATE()'
			if($req){		
				foreach($req as $req){
					$drivers = $this->PickupRequest->find()->where(['request_id' => $req['id'],'request_status !='=>'D'])->select(['Driver.first_name','Driver.id','Driver.last_name','Driver.phone_number','Driver.country_code','Driver.profile_pic','Driver.latitude','Driver.longitude','Driver.car_number'])->contain(['Driver'])->order(['PickupRequest.id' => 'DESC'])->first();
					$cancel_by_user = $this->PickupRequest->find()->where(['request_id' => $req['id'],'request_status '=>'D','declined_by'=>'U'])->order(['PickupRequest.id' => 'DESC'])->first();
					if(empty($cancel_by_user)){
						
					$old_date = strtotime($req['date_time']);
					$new = date('M d, Y h:i A', $old_date);
					$record[] = [
							  'user_id'=>$req['user_id'],
							  'username' => $req['user']['first_name'] . ' ' . $req['user']['last_name'] , 
							  'user_image' => !empty($req['user']['profile_pic']) ?Router::url('/', true) . "img/profile_images/" . $req['user']['profile_pic'] :Router::url('/', true) . "img/user_default.png" ,
							  'bill' => $req['fare']  +  $req['with_surcharge'],
							  'date' => $new,
							  'time' =>date('h:i A', $old_date),
							  'source_lat' => $req['source_lat'],
							  'source_lng' => $req['source_lng'],
							  'pickup_location' => $req['source_location'],
							  'dest_lat' => $req['dest_lat'],
							  'dest_lng' => $req['dest_lng'],
							   'pickup_destination' => $req['dest_location'],
							   'request_id' => $req['id'],
							   'driver_id'=> !empty($drivers)?$drivers['Driver']['id']:'',
							   'driver_image' => !empty($drivers)?(!empty($drivers['Driver']['profile_pic']) ?Router::url('/', true) . "img/profile_images/" . $drivers['Driver']['profile_pic'] :Router::url('/', true) . "img/user_default.png"):'' ,
							   'drivername' => !empty($drivers)?$drivers['Driver']['first_name'] . ' ' . $drivers['Driver']['last_name']:'',
							   'car_number' => !empty($drivers)?$drivers['Driver']['car_number'] :'',
							   'phone_number' => !empty($drivers)?$drivers['Driver']['country_code'] . $drivers['Driver']['phone_number']:'',
							   'message' => !empty($drivers)?'' :'No Driver Assigned to Ride',
							   
							  
							  ];
					}
							 
				}
			}
			$result = ['status' => '1' , 'message' => 'User Schedule Rides History.' , 'result' => $record];
			}else{
			
			 $result = array('status' => '0', 'message' => 'Invalid User ID.');
			}
			
		 }else{
			 $result = array('status' => '0', 'message' => 'All Fields Required.');
		 }
		 echo json_encode($result);
		 die;
	}	
	
	#_________________________________________________________________________#

    /**
     * @Date: 26 jan 2018
     * @Method : Save Card Details
     * @Purpose: This function is used to save cards
     * @Param: none
     * @Return: none 
     * */
    #_____________________________________________________________________________#

		function SaveCards(){
			$saveArray = $this->data;
			$record=[];
			if(isset($saveArray['user_id']) && !empty($saveArray['user_id']) && isset($saveArray['expiration_date']) && !empty($saveArray['expiration_date']) && isset($saveArray['card_number']) && !empty($saveArray['card_number']) ){
			$user = $this->Users->find()->where(['Users.id' => $saveArray['user_id']])->first();
				if(!empty($user)){
					$find_card = $this->Account->find()->where(['user_id' => $saveArray['user_id']])->first();
					if(!$find_card){
						$saveArray['set_default']='Y';
					}
					$card_exist = $this->Account->find()->where(['card_number' => $saveArray['card_number'],'user_id'=>$saveArray['user_id']])->first();
					if(!$card_exist){
						$data=$this->Account->newEntity($saveArray);
						$this->Account->save($data);
						$query = $this->Account->find()->where(['user_id'=>$saveArray['user_id']]);
						$card_exist = $query->all();
						$result = array('status' => '1', 'message' => 'Card Details Saved Successfully.','result'=>$card_exist);
					}else{
						$result = array('status' => '0', 'message' => 'Card already exist.');
					}					
				}else{			
				$result = array('status' => '0', 'message' => 'Invalid User ID.');
				}			
			}else{
			 $result = array('status' => '0', 'message' => 'All Fields Required.');
			}
			echo json_encode($result);
		    die;
		}
		#_________________________________________________________________________#

		/**
		 * @Date: 26 jan 2018
		 * @Method : Get Card Details
		 * @Purpose: This function is used to get save cards
		 * @Param: none
		 * @Return: none 
		 * */
		#_____________________________________________________________________________#
		
		function GetCards(){
			$saveArray = $this->data;
			$record=[];
			if(isset($saveArray['user_id']) && !empty($saveArray['user_id'])){
			$user = $this->Users->find()->where(['Users.id' => $saveArray['user_id']])->first();
				if(!empty($user)){
					$query = $this->Account->find()->where(['user_id'=>$saveArray['user_id']]);
					$card_exist = $query->all();
					$result = array('status' => '1', 'message' => 'Card Details.','result'=>$card_exist);
				}else{			
				$result = array('status' => '0', 'message' => 'Invalid User ID.');
				}			
			}else{
			 $result = array('status' => '0', 'message' => 'All Fields Required.');
			}
			echo json_encode($result,200); //JSON_PRETTY_PRINT
		    die;
		}
		
		#_________________________________________________________________________#

		/**
		 * @Date: 26 jan 2018
		 * @Method : Get Card Details
		 * @Purpose: This function is used to get save cards
		 * @Param: none
		 * @Return: none 
		 * */
		#_____________________________________________________________________________#
		
		function SetDefaultCard(){
			$saveArray = $this->data;
			$record=[];
			if(isset($saveArray['user_id']) && !empty($saveArray['user_id']) && isset($saveArray['card_id']) && !empty($saveArray['card_id'])){
				$user = $this->Users->find()->where(['Users.id' => $saveArray['user_id']])->first();
				if(!empty($user)){
					$this->Account->query()->update()->set(['set_default' => 'Y'])->where(['id'=>$saveArray['card_id']])->execute();
					$this->Account->query()->update()->set(['set_default' => 'N'])->where(['id !='=>$saveArray['card_id']])->execute();
					$query = $this->Account->find()->where(['user_id'=>$saveArray['user_id']]);
					$card_exist = $query->all();
					$result = array('status' => '1', 'message' => 'Card set as default.','result'=>$card_exist);
				}else{			
				$result = array('status' => '0', 'message' => 'Invalid User ID.');
				}
			}else{
			 $result = array('status' => '0', 'message' => 'All Fields Required.');
			}
			echo json_encode($result,200); //JSON_PRETTY_PRINT
		    die;
		}
		
		#_________________________________________________________________________#

		/**
		 * @Date: 26 jan 2018
		 * @Method : Edit Profile Driver details
		 * @Purpose: This function is used to edit profile details
		 * @Param: none
		 * @Return: none 
		 * */
		#_____________________________________________________________________________#
		
		function DriverDetails(){
			$saveArray = $this->data;
			 $this->loadModel('Users');
			$saveArray['unique_code']='';
			if (isset($saveArray['user_id'])  && !empty($saveArray['user_id']) ) {
			$user_Exist = $this->Users->find()->where(['id' => $saveArray['user_id']])->first(); 
			if (!empty($user_Exist)) {
				$driverdetails_Exist = $this->DriverDetail->find()->where(['user_id' => $saveArray['user_id']])->first(); 
				 if(empty($driverdetails_Exist)){
					$details   =   $this->DriverDetail->newEntity();  
					$driver =[
					'user_id' =>$saveArray['user_id']
					];
					$details =$this->DriverDetail->patchEntity($details, $driver); 
					$driverdetails_Exist = $this->DriverDetail->save($details);					
				} 
				
				 $destination = WWW_ROOT . 'img/driver_info/'; 
				$condition = [
					'id' => $saveArray['user_id']
				];
				if(isset($_FILES['license_doc']) && !empty($_FILES['license_doc'])){
				$saveArray['license_doc'] = $this->uploadPic('lic_',$_FILES['license_doc'],$saveArray['user_id'], $destination);
				}else{
					$saveArray['license_doc'] = $driverdetails_Exist['license_doc'];
				}
				if(isset($_FILES['insurance_doc']) && !empty($_FILES['insurance_doc'])){
				$saveArray['insurance_doc'] = $this->uploadPic('ins_',$_FILES['insurance_doc'],$saveArray['user_id'], WWW_ROOT . 'img/driver_info/');
				}else{
					$saveArray['insurance_doc'] = $driverdetails_Exist['insurance_doc'];
				}
				if(isset($_FILES['prmit_doc']) && !empty($_FILES['prmit_doc'])){
				$saveArray['prmit_doc'] = $this->uploadPic('permit_',$_FILES['prmit_doc'],$saveArray['user_id'], WWW_ROOT . 'img/driver_info/');
				} else{
					$saveArray['prmit_doc'] = $driverdetails_Exist['prmit_doc'];
				}
				if(isset($_FILES['vehicle_registration_doc']) && !empty($_FILES['vehicle_registration_doc'])){
				$saveArray['vehicle_registration_doc'] = $this->uploadPic('reg_',$_FILES['vehicle_registration_doc'],$saveArray['user_id'], WWW_ROOT . 'img/driver_info/');
				}else{
					$saveArray['vehicle_registration_doc'] = $driverdetails_Exist['vehicle_registration_doc'];
				}
				if(isset($_FILES['police_doc']) && !empty($_FILES['police_doc'])){
				$saveArray['police_doc'] = $this->uploadPic('police_',$_FILES['police_doc'],$saveArray['user_id'], WWW_ROOT . 'img/driver_info/');
				}else{
					$saveArray['police_doc'] = $driverdetails_Exist['police_doc'];
				}
				if(isset($_FILES['social_security_doc']) && !empty($_FILES['social_security_doc'])){
				$saveArray['social_security_doc'] = $this->uploadPic('ssec_',$_FILES['social_security_doc'],$saveArray['user_id'], WWW_ROOT . 'img/driver_info/');
				}else{
					$saveArray['social_security_doc'] = $driverdetails_Exist['social_security_doc'];
				}
			  /*  $this->Users->query()->update()
				->set(['profile_pic' => $filename])
				->where($condition)
				->execute(); */
				$userData = $this->DriverDetail->patchEntity($driverdetails_Exist, $saveArray);
				$this->DriverDetail->save($userData);  //update record
				if(isset($saveArray['car_capacity']) && !empty($_FILES['car_capacity'])){					
					$driverData = $this->User->patchEntity($user_Exist, $driverData);
					$this->User->save($user_Exist); //update car capacity
				}
				$result = array('status' => '1', 'message' => 'Profile Updated successfully.');
			}else{
					$result = array('status' => '0', 'message' =>'User Not Found.');
				}
			}else{
				$result = array('status' => '0', 'message' =>'Fields are required.');
			}
			echo json_encode($result,200); //JSON_PRETTY_PRINT
		    die;
		}
		
		
		#_________________________________________________________________________#

		/**
		 * @Date: 10 april 2018
		 * @Method : Get Reviews Listing
		 * @Purpose: This function is used to get Reviews Listing
		 * @Param: none
		 * @Return: none 
		 * */
		#_____________________________________________________________________________#
		
		function ReviewsListing(){
			$saveArray = $this->data;
			$record=[];
			$record['percentage_rate_1'] =0;
			$record['user_rate_1']= 0;
			$record['percentage_rate_2'] =0;
			$record['user_rate_2']= 0;
			$record['percentage_rate_3'] =0;
			$record['user_rate_3']= 0;
			$record['percentage_rate_4'] =0;
			$record['user_rate_4']= 0;
			$record['percentage_rate_5'] =0;
			$record['user_rate_5']= 0;
			if(isset($saveArray['user_id'])){
				$user = $this->Users->find()->where(['Users.id' => $saveArray['user_id']])->first();
				if(!empty($user)){
							$query = $this->Feedback->find()->where(['driver_id'=>$saveArray['user_id']]);
							$feedback_exist = $query->toArray();
						// (average rating-minimum rating)/(max rating - min rating)
						    $query = $this->Feedback->find()->where(['driver_id'=>$saveArray['user_id'],'rating'=>"1.0"]);
							$rev_exist = $query->toArray();
							if($rev_exist){
							$percentage =  (count($rev_exist)/count($feedback_exist))*100;
							$record['percentage_rate_1']= round($percentage,2);
							$record['user_rate_1']= count($rev_exist);
							}
							$query = $this->Feedback->find()->where(['driver_id'=>$saveArray['user_id'],'rating'=>"2.0"]);
							$rev_exist = $query->toArray();
							if($rev_exist){							
							$percentage =  (count($rev_exist)/count($feedback_exist))*100;
							$record['percentage_rate_2']= round($percentage,2);
							$record['user_rate_2']= count($rev_exist);
							}
							$query = $this->Feedback->find()->where(['driver_id'=>$saveArray['user_id'],'rating'=>"3.0"]);
							$rev_exist = $query->toArray();
							if($rev_exist){
							$percentage =  (count($rev_exist)/count($feedback_exist))*100;
							$record['percentage_rate_3']= round($percentage,2);
							$record['user_rate_3']= count($rev_exist);
							}
							$query = $this->Feedback->find()->where(['driver_id'=>$saveArray['user_id'],'rating'=>"4.0"]);
							$rev_exist = $query->toArray();
							if($rev_exist){
							$percentage =  (count($rev_exist)/count($feedback_exist))*100;
							$record['percentage_rate_4']= round($percentage,2);
							$record['user_rate_4']= count($rev_exist);
							}
							$query = $this->Feedback->find()->where(['driver_id'=>$saveArray['user_id'],'rating'=>"5.0"]);
							$rev_exist = $query->toArray();
							if($rev_exist){
							$percentage =  (count($rev_exist)/count($feedback_exist))*100;
							$record['percentage_rate_5']= round($percentage,2);
							$record['user_rate_5']= count($rev_exist);
							}
								$averagerating=0;
									if($feedback_exist){
										$totalrating=0;
										foreach($feedback_exist as $feedback){
											$totalrating += $feedback['rating'] ;
										}
										$averagerating = $totalrating/count($feedback_exist);
									}
							// (5*252 + 4*124 + 3*40 + 2*29 + 1*33) / (252+124+40+29+33) =
						$record['avrg_rating']= round(((5*$record['user_rate_5'] + 4*$record['user_rate_4'] + 3* $record['user_rate_3'] + 2* $record['user_rate_2'] + 1*$record['user_rate_1'])/count($feedback_exist)),2);
						$record['total_users']= count($feedback_exist);
						$query = $this->Feedback->find()->where(['driver_id'=>$saveArray['user_id'],'comment !='=>""])->select(['Users.first_name','Users.last_name','Users.user_type','Users.latitude','Users.longitude','Users.profile_pic','Feedback.user_id','Feedback.rating','Feedback.comment','Feedback.user_id','Feedback.id','Feedback.created'])->contain(['Users','Driver']);
						$reviews = $query->toArray();
						if($reviews){
							foreach($reviews as $n => $review){
								$review['user']['profile_pic'] = !empty($review['user']['profile_pic']) ?Router::url('/', true) . "img/profile_images/" . $review['user']['profile_pic'] :Router::url('/', true) . "img/user_default.png" ;
								$review['created'] = date('M d, Y h:i A', strtotime($review['created']));
							}
						}
					$result = array('status' => '1', 'message' => 'Card set as default.','result'=>$reviews,'record'=>$record);
				}else{			
				$result = array('status' => '0', 'message' => 'Invalid User ID.');
				}
			}else{
			 $result = array('status' => '0', 'message' => 'All Fields Required.');
			}
			echo json_encode($result,200); //JSON_PRETTY_PRINT
		    die;
		}
		
	 #_________________________________________________________________________#

    /**
     * @Date: 14 jan 2018
     * @Method : uploadPic
     * @Purpose: This function is used to upload images 
     * @Param: none
     * @Return: none 
     * */
    #_____________________________________________________________________________#
	
    function uploadPic($type=null, $upload_type,$id = null, $destination = null) {
        if (!empty($_FILES)) {
            $file = $upload_type;
            if ($file['size'] != 0) {
                if (empty($destination)) {
                    $destination = WWW_ROOT . 'img/profile_images/';
                }
                $ext = $this->Common->file_extension($file['name']);
				 $filename = $id . '_' . time() . '.' . strtolower($ext);
				 if (!empty($destination)) {
					  $filename = $type.$id . '_' . time() . '.' . strtolower($ext);
				 }
               
                $size = $file['size'];
                if ($size > 0) {
                    $files = $this->Common->get_files($destination, "/^" . $id . "_/i");
                    if (!empty($files)) {
                        foreach ($files as $x) {
                            @unlink($destination . $x);
                        }
                    }
                    if (preg_match("/gif|jpg|jpeg|png/i", $ext) > 0) {
                        $result = $this->Upload->upload($file, $destination, $filename, array('type' => 'resize','size' =>'300'));
                        return $filename;
                    }
                }
            }
        }
    }
    
   #________________________________________________________#
   
   
   function AutoDeclineRequest(){
	    $expirerequest = $this->PickupRequest->find()->select(['PickupRequest.id','PickupRequest.user_id','PickupRequest.request_id','PickupRequest.driver_id','PickupRequest.request_status','PickupRequest.request_process_status','Pickup.category_id','Pickup.payment_mode','Pickup.source_lat','Pickup.source_lng','Pickup.dest_lat','Pickup.dest_lng','Pickup.date_time','Pickup.decline_time','PickupRequest.declined_by','Users.first_name','Users.last_name','Users.device_token','Users.device_type','Users.user_type','Users.latitude','Users.longitude','Driver.first_name','Driver.last_name','Driver.country_code','Driver.phone_number','Driver.profile_pic','Driver.latitude','Driver.longitude','Driver.car_number'])->where(['PickupRequest.request_status !=' => 'D', 'Pickup.ride_status' =>'S','Pickup.date_time <=' => date('Y-m-d H:i:s')])->contain(['Users','Driver','Pickup'])->all();
		if($expirerequest){
			foreach($expirerequest as $expire){
				 $this->PickupRequest->query()->update()->set(['request_status' => 'D','declined_by' => 'D','cancel_reason'=>'cron'])->where(['PickupRequest.id' => $expire['id']])->execute();
			}
		}
	    $this->PickupRequest->query()->update()->set(['PickupRequest.request_status' => 'D','PickupRequest.declined_by' => 'D','PickupRequest.cancel_reason'=>'cron'])->where(['PickupRequest.request_status !=' => 'D'])->execute();	//,'Pickup.date_time <=' => date('Y-m-d H:i:s')
	   $getRequestStatus = $this->PickupRequest->find()->select(['PickupRequest.id','PickupRequest.user_id','PickupRequest.request_id','PickupRequest.driver_id','PickupRequest.request_status','PickupRequest.request_process_status','Pickup.category_id','Pickup.payment_mode','Pickup.source_lat','Pickup.source_lng','Pickup.dest_lat','Pickup.dest_lng','Pickup.date_time','Pickup.decline_time','PickupRequest.declined_by','Users.first_name','Users.last_name','Users.device_token','Users.device_type','Users.user_type','Users.latitude','Users.longitude','Driver.first_name','Driver.last_name','Driver.country_code','Driver.phone_number','Driver.profile_pic','Driver.latitude','Driver.longitude','Driver.car_number'])->where(['PickupRequest.request_status !=' => 'D', 'Pickup.ride_status' =>'S','Pickup.date_time >=' => date('Y-m-d H:i:s')])->contain(['Users','Driver','Pickup'])->all(); //,'Pickup.date_time >=' => date('Y-m-d H:i:s')
	   if($getRequestStatus){
		    $request_ids = [];
		   foreach($getRequestStatus as $n=>$request){
			   $request_date = strtotime($request['pickup']['decline_time']);			  
			   $current_date = strtotime(Time::now());			 
			   if($current_date > $request_date){
				     $this->PickupRequest->query()->update()->set(['request_status' => 'D','declined_by' => 'D'])->where(['PickupRequest.id' => $request['id']])->execute();
					 $request_ids[] = $request['id'];
			   }
		   }
		   $result = array('status' => '1', 'message' => 'Requests has been decline.','result'=>$request_ids);	
	   }else{
		   $result = array('status' => '1', 'message' => 'Requests has been decline.','result'=>[]);	
	   }
	   
	   echo json_encode($result,200); //JSON_PRETTY_PRINT
		    die;
   }
   #_________________________________________________________________________#		
    /**
     * @Date: 14 jan 2018
     * @Method : Send pool Request
     * @Purpose: This function Forget Password is used to send request to near driver
     * @Param: none
     * @Return: none 
     * */
    #_____________________________________________________________________________#
	function SendPoolRequest() {
			$saveArray = $this->data;
			if (isset($saveArray['user_id'])  && !empty($saveArray['user_id']) && isset($saveArray['source_lat'])  && !empty($saveArray['source_lat']) && isset($saveArray['source_lng'])  && !empty($saveArray['source_lng']) && isset($saveArray['dest_lat'])  && !empty($saveArray['dest_lat']) && isset($saveArray['dest_lng'])  && !empty($saveArray['dest_lng']) && isset($saveArray['seatings']) && !empty($saveArray['seatings'])) { 
				$user_Exist =   $this->Users->find()->select(['id','email','first_name','last_name','phone_number','country_code','user_type','profile_pic','latitude','longitude','device_token','device_type'])->where(['id' => $saveArray['user_id'],'user_type'=>'U'])->first();
				if (!empty($user_Exist)) {
				     $id = $saveArray['user_id'];
					$latitude = $saveArray['source_lat'];
					$longitude = $saveArray['source_lng'];
					$seatings = $saveArray['seatings'];
					$distance= 20;
					$conn = ConnectionManager::get('default');			
					$query = "SELECT id,first_name,last_name,device_token,device_type,latitude,longitude,user_type, SQRT( POW( 69.1 * (latitude - $latitude ) , 2 ) + POW( 69.1 * ( $longitude - longitude ) * COS(latitude / 57.3 ) , 2 ) ) AS distance FROM users where $id NOT IN (id) && user_type='D' && is_online='Y' && is_verified='Y' && is_Active='Y' && is_approved='Y' && availability_status='Y' && is_deleted='N' && latitude !='' && longitude != '' && is_pool = 'Y' && available_seats >= $seatings HAVING distance < $distance";	
					$drivers_found=$conn->execute($query)->fetchAll('assoc');
					$driver_count = count($drivers_found);
					if($drivers_found){		
						$saveArray['ride_status']='P';
						$data=$this->Pickup->newEntity($saveArray);
						$pickup = $this->Pickup->save($data);
						if($pickup){						
							$count =0;$total_distance=00.00;
							$total_fare =00.00;
							$surcharge =00.00;
							$minutes =0;
							if(isset($saveArray['category_id'])){		
									$cat_carge = $this->Category->find()->where(['status' => 'Y','is_deleted'=>'N','id'=>$saveArray['category_id']])->first();
									$arr = $this->Common->GetDrivingDistance(array('lat' => $saveArray['source_lat'], 'long' => $saveArray['source_lng']), array('lat' => $saveArray['dest_lat'], 'long' => $saveArray['dest_lng']));
									$minutes = round($arr['durantion_in_min']);
									//Personal , Plus ,pool [later] , Premier [later]
										$fare_est = ($arr['distance_in_miles'] * $cat_carge['per_mile']);									
										$fare = round($fare_est, 2);
										$charge_data = $this->Charge->find()->where(['id' =>1])->first();
										$surcharge = $fare * $charge_data['value'] /100;
										$surcharge = round($surcharge, 2);	
										$total_fare = round($fare + $surcharge,2);
										$total_distance =round($arr['distance_in_miles'],2);
								}
							foreach($drivers_found as $n=>$drivers){
								$device_token = '';
								$request['user_id']= $saveArray['user_id'];
								$request['driver_id']= $drivers['id'];
								$request['request_id']= $pickup['id'];
								$request_data= $this->PickupRequest->newEntity($request);
								$this->PickupRequest->save($request_data);
								//user information details
								$username = $user_Exist['first_name'] . " " . $user_Exist['last_name'];
								$request_id= $pickup['id'];
								$user_image = ($user_Exist['profile_pic'])? Router::url('/', true) . "img/profile_images/" . $user_Exist['profile_pic']:Router::url('/', true) . "img/user_default.png";
								$mobile =  $user_Exist['country_code'] . " " . $user_Exist['phone_number'];
								$message = array(
									'message'=>" Recieved new request ",
									'source_latitude' => $latitude, 
									'source_longitude' => $longitude,
									'source_addresss'=>$saveArray['source_location'],
									'request_id' => $request_id,
									'image' => $user_image, 
									'username' => $username,
									'mobile' => $mobile,
									'destination_latitude' => $saveArray['dest_lat'],
									'destination_longitude' => $saveArray['dest_lng'],
									'destination_addresss' => $saveArray['dest_location'],								
									'seatings' => $seatings,								
									'noti_to' =>"Driver",
									'duration'=>$minutes,
									'surcharge'=>$surcharge,
									'charges'=>$total_fare,
									'request_status'=>'Arrived',
									'total_distance'=>$total_distance,
									'user_id'=>$saveArray['user_id'],
									'noti_type' =>"PR"
								);
								$device_token[] = $drivers['device_token'];
								 if($drivers['device_type'] == 'A'){							 
									$notification = $this->Common->android_send_notification($device_token,$message, $drivers['user_type']);
								 }else{
									$notification =  $this->Common->iphone_send_notification($device_token,$message, $drivers['user_type']);
								 }
								 $count++;
							}
						}
					 //$result = array('status' => '1', 'message' => 'Request sent','request_id' => $pickup['id']);
					//check the Request Status
					//$time =  strtotime(date("m/d/Y h:i:s a", time()+10;
					//$later_time =  strtotime(date("m/d/Y h:i:s a", time())); $later_time ==  $time)
					 $a = 1;
					 sleep(5);
					 $chkReqStatus = $this->PickupRequest->find()->select(['id','request_status'])->where(['request_id' =>$pickup['id'],'request_status'=>'A'])->first();
                    if (empty($chkReqStatus)) {
                        $message = array('message' => 'No Driver, please retry.','noti_to' =>"User",'noti_type' =>"ND"); 
						$device_token[]= $user_Exist['device_token'];
						 /* if($user_Exist['device_type']=='A'){
							$this->Common->android_send_notification($device_token, $message, $user_Exist['user_type']);
						 }else{
							$this->Common->iphone_send_notification($device_token,$message,$user_Exist['user_type']); 
						 } */
						 $this->PickupRequest->query()->update()->set(['request_status' => 'D','declined_by' => 'U'])->where(['request_id' => $pickup['id']])->execute();
                        $result = array('status' => '1', 'message' => 'No Driver, please retry.', 'request_id' => $pickup['id']);	
						echo json_encode($result);die;					
                    } else {
                        $message = array('message' => 'Request Accepted, Driver is on the way!!','noti_to' =>"User",'noti_type' =>"RA");
						$device_token[]= $user_Exist['device_token'];
						if($user_Exist['device_type']=='A'){
							//$this->Common->android_send_notification($device_token, $message, $user_Exist['user_type']);
						 }else{
							//$this->Common->iphone_send_notification($device_token, $message, $user_Exist['user_type']); 
						 }
                        $result = array('status' => '1', 'message' => 'Request Accepted, Driver is on the way!!','request_id' => $pickup['id']);
						echo json_encode($result);die;		
                    } 
					//End check request status section
					}else{
						$result = array('status' => '0', 'message' =>'No near by Taxi found, please request in few minutes.','request_id' =>'');
						echo json_encode($result);die;
					}
				}else{
					$result = array('status' => '0', 'message' =>'User Not Found.');
				}
			 }else{
				$result = array('status' => '0', 'message' =>'All fields are required.');
			}			 
			echo json_encode($result);
			die; 
	}
	#_________________________________________________________________________#		
    /**
     * @Date: 11 may 2018
     * @Method : Driver Ongoing Ride
     * @Purpose: This function Driver Ongoing Ride for drivers
     * @Param: none
     * @Return: none 
     * */
    #_____________________________________________________________________________#
	
	function DriverOngoingRide() {
		$saveArray = $this->data;$record=[];
		if(isset($saveArray['user_id'])  && !empty($saveArray['user_id'])){
			$user_Exist =   $this->Users->find()->select(['id','email','first_name','last_name','phone_number','country_code','user_type','profile_pic','latitude','longitude','device_token','device_type'])->where(['id' => $saveArray['user_id'],'user_type'=>'D'])->first();
				if (!empty($user_Exist)) {
					$request = $this->PickupRequest->find()->where(['PickupRequest.driver_id' => $saveArray['user_id'],'PickupRequest.request_status'=>'A','PickupRequest.request_process_status !='=>'C'])->select(['PickupRequest.id','PickupRequest.fare','PickupRequest.with_surcharge','PickupRequest.request_status','PickupRequest.request_process_status','Users.first_name','Users.last_name','Users.phone_number','Users.country_code','Users.profile_pic','Users.latitude','Users.longitude','Pickup.id','Pickup.user_id','Pickup.source_location','Pickup.source_lat','Pickup.source_lng','Pickup.dest_lat','Pickup.dest_lng','Pickup.dest_location' , 'Pickup.created','Pickup.category_id','Pickup.seatings' ])->contain(['Users','Pickup'])->order(['Pickup.id' => 'DESC']);
					if($request){
					foreach($request as $req){
						    $category_id = ($req['pickup']['category_id'])?$req['pickup']['category_id']:1;
							$cat_carge = $this->Category->find()->where(['status' => 'Y','is_deleted'=>'N','id'=>$category_id])->first();
							$arr = $this->Common->GetDrivingDistance(array('lat' => $req['pickup']['source_lat'], 'long' => $req['pickup']['source_lng']), array('lat' => $req['pickup']['dest_lat'], 'long' => $req['pickup']['dest_lng']));
							$minutes = round($arr['durantion_in_min']);
							//Personal , Plus ,pool [later] , Premier [later]
							$fare_est = ($arr['distance_in_miles'] * $cat_carge['per_mile']);						
							$fare = round($fare_est, 2);
							$charge_data = $this->Charge->find()->where(['id' =>1])->first();
							$surcharge = $fare * $charge_data['value'] /100;
							$surcharge = round($surcharge, 2);	
							$total_fare = round($fare + $surcharge,2);
							$total_distance =round($arr['distance_in_miles'],2);
							$old_date = strtotime($req['pickup']['created']);
							$new = date('M d, Y h:i A', $old_date);
							$mobile =  $user_Exist['country_code'] . " " . $user_Exist['phone_number'];
							if($req['request_process_status']=='A'){
								$show_text="Start Ride";
							}elseif($req['request_process_status']=='S'){
								$show_text="Ride Completed";
							}else{
								$show_text="";
							}
							$record[] = [
								  'request_id' => $req['pickup']['id'],
								  'user_id' => $req['pickup']['user_id'],
								  'username' => $req['user']['first_name'] . ' ' . $req['user']['last_name'] , 
								  'user_image' => !empty($req['user']['profile_pic']) ?Router::url('/', true) . "img/profile_images/" . $req['user']['profile_pic'] :Router::url('/', true) . "img/user_default.png" ,
								  'mobile'=>$mobile,
								  'bill' => $req['fare']  +  $req['with_surcharge'],
								  'request_process_status' => $req['request_process_status'],
								  'request_status' => $req['request_status'],
								  'display_text'=>$show_text,
								  'date' => $new,
								  'duration'=>$minutes,
								  'surcharge'=>$surcharge,
								  'charges'=>$total_fare,
								  'total_distance'=>$total_distance,
								  'source_latitude' =>$req['pickup']['source_lat'], 
								  'source_longitude' => $req['pickup']['source_lng'],
								  'source_addresss'=>$req['pickup']['source_location'],
								  'destination_latitude' => $req['pickup']['dest_lat'],
								  'destination_longitude' =>$req['pickup']['dest_lng'],
								  'destination_addresss' => $req['pickup']['dest_location'],
								  'seatings' => ($req['pickup']['seatings'])?$req['pickup']['seatings']:''
								];
						}
					}
					$result = ['status' => '1' , 'message' => 'Driver Ongoing Rides' , 'result' => $record];
				}else{
					$result = array('status' => '0', 'message' =>'User Not Found.');
				}
			}else{
				$result = array('status' => '0', 'message' =>'All fields are required.');
			}			 
			echo json_encode($result);
			die; 
	}
}
