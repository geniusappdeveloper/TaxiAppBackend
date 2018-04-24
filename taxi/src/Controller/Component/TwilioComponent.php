<?php 
    namespace App\Controller\Component;
    require_once(ROOT . '/vendor' . DS  . 'twilio' . DS  .  'sdk' . DS  . 'Services' . DS  . 'Twilio.php');
    
    use Cake\Controller\Component;
    use Twilio\Rest\Client;
   use Twilio\Exceptions\TwilioException;
class TwilioComponent extends Component{
   
	public function startup($controller) 
    {  $AccountSid=""; 
        $AuthToken="";
        $this->Twilio = new Twilio($AccountSid, $AuthToken);
    } 

	function sms($from, $to, $message)
	{
        $AccountSid="AC76e1b1fcc25a29483ff2c19ca5bec1e9"; //"AC864b2f54b5ca099dc28153cd005fb025"
        $AuthToken="81d1527c91898f53716753f5d8eabdcb";// ba5a9edcbf7318d9af46f7f06dee3cca
        $from = "12243343502";
        $this->Twilio = new Client($AccountSid, $AuthToken);
		try{
        $response = $this->Twilio->messages->create(
             $to,
            array(
                // A Twilio phone number you purchased at twilio.com/console
                'from' => $from,
                // the body of the text message you'd like to send
                'body' => $message
            )
        );
		//dump($response);die;
		}catch(TwilioException $e) {
			return true;
		}
		return true;
		
	}
}

?>