<?php 

namespace App\Model\Table;

    use Cake\ORM\Table;
    use Cake\Validation\Validator;
    class FeedbackTable extends Table
    {
	
       public function initialize(array $config)
        {
            $this->addBehavior('Timestamp');
			$this->table('feedbacks');
			$this->belongsTo('Users');
			$this->belongsTo('Pickup', [
				'foreignKey' => 'request_id',
				'className' => 'Pickup'
			]);
			$this->belongsTo('Driver', [
				'foreignKey' => 'driver_id',
				'className' => 'Users'
			]);
        }
        public function validationDefault(Validator $validator)
        {
             return $validator ;
              
				
        }

       
        
        
    }
?>