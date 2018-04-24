<?php

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Core\App;
use Cake\ORM\TableRegistry;
use Cake\ORM\Table;

class TransactionsController extends AppController
{
	
	public $paginate = [
        'limit' => 15,
        'order' => [
            'Transaction.id' => 'desc'
        ]
    ];
	
	public function initialize()
    {
        parent::initialize();
        $this->loadModel('Category');
		$this->loadModel('Transaction');
		$this->loadModel('User');
		$this->loadModel('PickupRequest');
    }

	public function transactionlist(){
		$session=$this->request->session()->read('SESSION_ADMIN');
			$this->set('ses',$session);
			if(!empty($session)){
		   $this->setSession();
		   $this->set("title", "Transaction List");
		   
		   $query = $this->Transaction->find()->select(['Transaction.id' , 'Users.id' , 'Users.first_name' , 'Driver.id' , 'Driver.first_name','Pickup.id','Pickup.source_location','Pickup.dest_location','Transaction.request_id','Transaction.transaction_id','Transaction.amount','Transaction.with_surcharge','Transaction.total_amount','Transaction.transaction_status','Transaction.created', ])->where(['Transaction.is_deleted' => 'N'])->contain(['Users','Driver','Pickup']);
		   
		  
		   $transactions = $this->paginate($query);
	
		  $this->set('transaction', $transactions);
		}else{
			$this->Flash->error('First Login here');
			$this->redirect(array('controller'=>'users','action' => 'login'));
		}
	}


		public function addFare(){
			 $this->setSession();
			  $this->set("title", "Add Fare");
			$article = $this->Fare->newEntity();
				if ($this->request->is('post')) {
					$article = $this->Fare->patchEntity($article, $this->request->getData());

					if ($this->Fare->save($article)) {
						$this->Flash->success(__('Your Fare For catgeory has been saved.'));
						return $this->redirect(['action' => 'fare-list']);
					}
					$this->Flash->error(__('Unable to add your article.'));
				}
				$this->set('article', $article);
		}
		
		public function editFare($id = null){
			 $this->setSession();
			   $this->set("title", "Edit Fare");
			   $this->set("id",$id);
			 $fare_Exist = $this->Fare->find()->where(['id' => $id])->first(); 
			 if($this->request->getData()){
			 
					if(!empty($fare_Exist)) {
							$fareData = $this->Fare->patchEntity($fare_Exist, $this->request->getData());
							$this->Fare->save($fareData);  //update record
							$this->Flash->success(__('Your Fare For catgeory has been Updated.'));
							return $this->redirect(['action' => 'fare-list']);
						}
				}
				$this->set('fare', $fare_Exist);
				//return $this->redirect(['action' => 'edit_fare']);				
		}
		
		 public function changeStatus(){ 
				if(isset($this->request->params['pass'][0]) ){
					$id = $this->request->params['pass'][0];
				}
				 $Cat_Exist = $this->Category->find()->where(['id' => $id])->first();
				if($Cat_Exist){
					$status = $Cat_Exist->status =='Y'?'N':'Y';
					$message = $Cat_Exist->status =='Y'?'Deactivated':'Activated';
					$this->Category->query()->update()->set(['status' => $status])->where(['id' => $id])->execute();
					$this->Flash->success("Cetegories $message Successfully.");
					$this->redirect($this->referer());
				}
             $this->redirect($this->referer()); 			
		}
		
		
		public function delete(){
			$session = $this->request->session();
			if(isset($this->request->params['form']['IDs'])){
				$deleteString = implode("','",$this->request->params['form']['IDs']);
			}elseif(isset($this->request->params['pass'][0]) ){
				$deleteString = $this->request->params['pass'][0];
			}else{
				
				$this->redirect($this->referer());
			}
			if(!empty($deleteString)){
				//$this->Users->deleteAll("id in ('".$deleteString."')");
				$this->Transaction->query()->update()->set(['is_deleted' => 'Y'])->where(["id in ('".$deleteString."')"])->execute();
				$this->Flash->success('Transaction Deleted Successfully.');
				$this->redirect($this->referer());
			}
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
	



}




