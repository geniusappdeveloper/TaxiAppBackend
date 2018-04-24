<?php 

namespace App\Model\Table;

    use Cake\ORM\Table;
    use Cake\Validation\Validator;
    class TransactionTable extends Table
    {
	
       public function initialize(array $config)
        {
            $this->addBehavior('Timestamp');
			$this->table('transactions');
        }
        public function validationDefault(Validator $validator)
        {
             return $validator ;
              
				
        }

       
        
        
    }
?>