<?php 
    namespace App\Controller\Component;
    require_once(ROOT . '/vendor' . DS  . 'twilio' . DS  .  'sdk' . DS  . 'Services' . DS  . 'Twilio.php');
    
    use Cake\Controller\Component;
    use Twilio\Rest\Client;
class TwilioComponent extends Component{
   
	public function startup($controller) 
    {  $AccountSid=""; 
        $AuthToken="";
        $this->Twilio = new Twilio($AccountSid, $AuthToken);
    } 

	function sms($from, $to, $message)
	{
        $AccountSid="AC864b2f54b5ca099dc28153cd005fb025"; //"AC864b2f54b5ca099dc28153cd005fb025"
        $AuthToken="ba5a9edcbf7318d9af46f7f06dee3cca";// ba5a9edcbf7318d9af46f7f06dee3cca
        $from = "447400372834";
        $this->Twilio = new Client($AccountSid, $AuthToken);
        $response = $this->Twilio->messages->create(
            // the number you'd like to send the message to
            $to,
            array(
                // A Twilio phone number you purchased at twilio.com/console
                'from' => $from,
                // the body of the text message you'd like to send
                'body' => $message
            )
        );
        pr($response);
        //$response = $this->Twilio->sms($from, $to,$message);
        pr($response->IsError);die;
		if($response->IsError)
			echo 'Error: ' . $response->ErrorMessage;
		else
			echo 'Sent message to ' . $to;
	}
}

?>