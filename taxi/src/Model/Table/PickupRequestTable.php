<?php 

namespace App\Model\Table;

    use Cake\ORM\Table;
    use Cake\Validation\Validator;
    class PickupRequestTable extends Table
    {
	
       public function initialize(array $config)
        {
            $this->addBehavior('Timestamp');
			$this->table('pickup_requests');
			$this->belongsTo('Pickup', [
				'foreignKey' => 'request_id',
				'className' => 'Pickup'
			]);
			$this->belongsTo('Users', [
				'foreignKey' => 'user_id',
				'className' => 'Users'
			]);
			$this->belongsTo('Driver', [
				'foreignKey' => 'driver_id',
				'className' => 'Users'
			]);
			$this->belongsTo('Users');
        }
        public function validationDefault(Validator $validator)
        {
             return $validator ;
              /*   ->requirePresence('source_lat','create','Source Latitude is required')
				  ->notEmpty('source_lat', 'source Latitude can not be empty', 'create')
				  ->requirePresence('source_lng','create','Source Longitude is required')
                ->notEmpty('source_lng', 'source Longitude can not be empty', 'create')
                 ->requirePresence('dest_lat','create','Destination Latitude is required')
				  ->notEmpty('dest_lat', 'Destination Latitude can not be empty', 'create')
				  ->requirePresence('dest_lng','create','Destination Longitude is required')
                ->notEmpty('dest_lng', 'Destination Longitude can not be empty', 'create')
                ->add('category', 'inList', [
                    'rule' => ['inList', ['Personal','Plus','Pool','Premier']],
                    'message' => 'Please enter a valid Category'
                ])
				->add('payment_mode', 'inList', [
                    'rule' => ['inList', ['CC']],
                    'message' => 'Please enter a valid Payment Mode'
                ]); */
				
        }

       /*  public function buildRules(RulesChecker $rules)
        {
            //$rules->add($rules->isUnique(['username']));
            $rules->add($rules->isUnique(['email']));
            return $rules;
        }  */
        
        
    }
?>