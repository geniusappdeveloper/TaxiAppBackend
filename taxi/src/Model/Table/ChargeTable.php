<?php 

namespace App\Model\Table;

    use Cake\ORM\Table;
    use Cake\Validation\Validator;
    class ChargeTable extends Table
    {
	
       public function initialize(array $config)
        {
            $this->addBehavior('Timestamp');
			$this->table('charges');
        }
        public function validationDefault(Validator $validator)
        {
             return $validator ;
              
				
        }

       
        
        
    }
?>