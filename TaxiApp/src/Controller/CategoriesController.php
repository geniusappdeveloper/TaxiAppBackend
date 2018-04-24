<?php

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Core\App;
use Cake\ORM\TableRegistry;

class CategoriesController extends AppController
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
    }

		public function categoryList(){
		   $this->setSession();
		   $this->set("title", "Category List");
		   $query = $this->Category->find()->where(['is_deleted' => 'N']);
		   $categories = $this->paginate($this->Category->find()->where(['is_deleted' => 'N']));
		  $this->set('categories', $categories);
		}


		public function add_category(){
			 $this->setSession();
			$article = $this->Category->newEntity();
				if ($this->request->is('post')) {
					$article = $this->Category->patchEntity($article, $this->request->getData());

					if ($this->Category->save($article)) {
						$this->Flash->success(__('Your article has been saved.'));
						return $this->redirect(['action' => 'alist']);
					}
					$this->Flash->error(__('Unable to add your article.'));
				}
				$this->set('article', $article);
			
			 
			

		}
		
		 public function addCategory(){
			 $this->setSession();
			  $this->set("title", "Add Category");
			$article = $this->Category->newEntity();
				if ($this->request->is('post')) {
					$article = $this->Category->patchEntity($article, $this->request->getData());

					if ($this->Category->save($article)) {
						$this->Flash->success('Catgeory has been saved.');
						return $this->redirect(['action' => 'category-list']);
					}
					$this->Flash->error(__('Unable to add your article.'));
				}
				$this->set('article', $article);
		}
		
		
		public function editCategory($id = null){
			$this->setSession();
			   $this->set("title", "Edit Category");
			   $this->set("id",$id);
			 $cat_Exist = $this->Category->find()->where(['id' => $id])->first(); 
			 if($this->request->getData()){
			 
					if(!empty($cat_Exist)) {
							$fareData = $this->Category->patchEntity($cat_Exist, $this->request->getData());
							$this->Category->save($fareData);  //update record
							$this->Flash->success('Catgeory has been Updated.');
							return $this->redirect(['action' => 'category-list']);
						}
				}
				$this->set('category', $cat_Exist);
		 }
		
		 public function changeStatus($id = null){ 
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
		
		
		public function delete($id = null){
			$session = $this->request->session();
			if(isset($id)){
				$deleteString = implode("','",$this->request->params['form']['IDs']);
			}elseif(isset($id) ){
				$deleteString = $id;
			}else{
				
				$this->redirect($this->referer());
			}
			if(!empty($deleteString)){
				//$this->Users->deleteAll("id in ('".$deleteString."')");
				$this->Category->query()->update()->set(['is_deleted' => 'Y'])->where(["id in ('".$deleteString."')"])->execute();
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




