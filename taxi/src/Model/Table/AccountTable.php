<?php 

namespace App\Model\Table;

    use Cake\ORM\Table;
    use Cake\Validation\Validator;
    class AccountTable extends Table
    {
	
       public function initialize(array $config)
        {
			$this->table('accounts');
            $this->addBehavior('Timestamp');
			$this->belongsTo('Users');
			
        }
        public function validationDefault(Validator $validator)
        {
            return $validator;
				
        }

       /*  public function buildRules(RulesChecker $rules)
        {
            //$rules->add($rules->isUnique(['username']));
            $rules->add($rules->isUnique(['email']));
            return $rules;
        }  */
        
        
    }
?>