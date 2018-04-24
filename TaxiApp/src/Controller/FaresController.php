<?php

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Core\App;
use Cake\ORM\TableRegistry;

class FaresController extends AppController
{
	
	public $paginate = [
        'limit' => 5,
        'order' => [
            'Category.id' => 'asc'
        ]
    ];
	
	public function initialize()
    {
        parent::initialize();
        $this->loadModel('Category');
		$this->loadModel('Fare');
    }

		public function fareList(){
		   $this->setSession();
		   $this->set("title", "Fare List");
		   $query = $this->Fare->find()->where(['is_deleted' => 'N']);
		   $fares = $this->paginate($this->Fare->find()->where(['is_deleted' => 'N']));
		  $this->set('fares', $fares);
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
				$this->Fare->query()->update()->set(['is_deleted' => 'Y'])->where(["id in ('".$deleteString."')"])->execute();
				$this->Flash->success('Cetegories Deleted Successfully.');
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




