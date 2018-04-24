<?php 

namespace App\Model\Table;

    use Cake\ORM\Table;
    use Cake\Validation\Validator;
    class DriverDetailTable extends Table
    {
	
       public function initialize(array $config)
        {
            $this->addBehavior('Timestamp');
			$this->table('driver_details');
        }
        public function validationDefault(Validator $validator)
        {
             return $validator ;
              
				
        }

       
        
        
    }
?>