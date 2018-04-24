<?php 

namespace App\Model\Table;

    use Cake\ORM\Table;
    use Cake\Validation\Validator;
    class UsersTable extends Table
    {
       public function initialize(array $config)
        {
            $this->addBehavior('Timestamp');
			$this->hasMany('PickupRequest')->foreignKey(['user_id','driver_id']);
			$this->hasMany('Pickup');
			$this->hasMany('driver_details');
        }
        public function validationDefault(Validator $validator)
        {
            return $validator
                 ->requirePresence('first_name','create','First Name is required')
                ->notEmpty('first_name', 'First Name can not be empty', 'create')
              //  ->requirePresence('password','create','A password is required')
                ->notEmpty('password', 'A password can not be empty', 'create')
                ->add('password', [
                    'length' => [
                    'rule' => ['minLength', 8],
                    'message' => 'Passwords must be at least 8 characters long.',
                     ]
                ])
               // ->requirePresence('country_code','create', 'A country code is required')
               // ->notEmpty('country_code', 'A country code  can not be empty', 'create')
               // ->requirePresence('phone_number','create', 'A phone Number is required')
                ->add('phone_number', [ 'unique' => ['rule' => 'validateUnique', 'provider' => 'table','message' => 'Phone Number already exist'] ])
                ->notEmpty('phone_number', 'A phone Number  can not be empty', 'create')
                ->notEmpty('user_type', 'A role is required')
                ->add('user_type', 'inList', [
                    'rule' => ['inList', ['U', 'D']],
                    'message' => 'Please enter a valid role'
                ])
               ->requirePresence('email','create','E-mail must is required')
                ->add('email', [ 'unique' => ['rule' => 'validateUnique', 'provider' => 'table','message' => 'E-mail already exist'] ])
                ->add('email', 'validFormat', [
                    'rule' => 'email',
                    'message' => 'E-mail must be valid'
                ]);
        }

       /*  public function buildRules(RulesChecker $rules)
        {
            //$rules->add($rules->isUnique(['username']));
            $rules->add($rules->isUnique(['email']));
            return $rules;
        }  */
        
        
    }
?>