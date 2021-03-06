<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use  Cake\Event\Event;

class CommonComponent extends Component{

    function randomCode($plength='8'){
        
                $code="";
                $chars = 'ABCDEFGHJKLMNPQRTUVWXY346789ABCDEFGHJKLMNPQRTUVWXY346789';//string by which new code will be generated
                mt_srand(microtime() * 1000000);
                for($i = 0; $i < $plength; $i++) {
                    $key = mt_rand(0,strlen($chars)-1);
                    $code = $code . $chars{$key};
                }
                $code=trim($code);
                return $code ;
        
            }
        
            
            /**
        
                * @Date: 11-Nov-2009
                *@Method : changeDateFormat
                *@Purpose:Gets Details of a Date Span . Called Via AJAX.
            **/
        
            function changeDateFormat($date = "", $format_in = "", $format_to = ""){
        
              $tmp_date = explode("-",$date);
              switch($format_in){
              case "Y-m-d":
              $t_stmp = mktime(0,0,0,$tmp_date[1],$tmp_date[2],$tmp_date[0]);
              case "m-d-Y":
              $t_stmp = mktime(0,0,0,$tmp_date[0],$tmp_date[1],$tmp_date[2]);
              }
              return date($format_to,$t_stmp);
                 }
        
           
          /**
            * @Date: 1-Dec-2009
            *@Method : getRandomNumber
            *@Purpose: Generates a random number
          **/
        
            function getRandomNumber(){
        
              srand ((double) microtime( )*1000000);
              $random_number = rand();
              return $random_number;
          }
        
           /**
            * @Date: 16-Dec-2009
            *@Method : getMonthsArray
            *@Purpose: Get an array of Months
          **/
        
          function getMonthsArray(){
        
            return array(
            ""=>"-Select-",
            "01" => "January",
            "02" => "February",
            "03" => "March",
            "04" => "April",
            "05" => "May",
            "06" => "June",
            "07" => "July",
            "08" => "August",
            "09" => "September",
            "10" => "October",
            "11" => "November",
            "12" => "December"
                );
          }
        
           /**
             * @Date: 16-Dec-2009
             *@Method : getDaysArray
             *@Purpose: Get an array of days
           **/
        
           function getDaysArray(){
           
              $i 		= 1;
              $array 	= array();
              while($i<=31){
              $array[$i] = $i;
              $i++;
              }
              return $array;
           }
           /**
            * @Date: 16-Dec-2009
            *@Method : getDaysArray
            *@Purpose: Get an array of years
           **/
        
           function getYearsArray(){
        
              $i 	= date("Y")-100;
              $array 	= array();
              while($i<=date("Y")){
              $array[$i] = $i;
              $i++;
              }
              return $array;
           }
           
           /**
            * @Date: 29-April-2012
            *@Method : getDaysArray
            *@Purpose: Get an array of years
           **/
        
           function getCustomYearsArray(){
        
              $i 	= 2011;
              $array 	= array();
              while($i<=date("Y")){
              $array[$i] = $i;
              $i++;
              }
              return $array;
           }
           
           /** @Date: 11-Jan-2009
            *@Method : getRandomString
            *@Purpose: generates random number.
          **/
            function getRandomString($length)
        
            {
                if($length>0) 
                { 
                $rand_id="";
                for($i=1; $i<=$length; $i++)
                {
                mt_srand((double)microtime() * 1000000);
                $num = mt_rand(1,36);
                $rand_id .= $this->assign_rand_value($num);
                }
                }
                return $rand_id;
            }
        
           /** @Date: 11-Jan-2009
            *@Method : assign_rand_value
            *@Purpose: generates random number. This function is used by getRandomString function.
          **/
        
              function assign_rand_value($num)
            {
            // accepts 1 - 36
            switch($num)
            {
            case "1":
            $rand_value = "a";
            break;
            case "2":
            $rand_value = "b";
            break;
            case "3":
            $rand_value = "c";
            break;
            case "4":
            $rand_value = "d";
            break;
            case "5":
            $rand_value = "e";
            break;
            case "6":
            $rand_value = "f";
            break;
            case "7":
            $rand_value = "g";
            break;
            case "8":
            $rand_value = "h";
            break;
            case "9":
            $rand_value = "i";
            break;
            case "10":
            $rand_value = "j";
            break;
            case "11":
            $rand_value = "k";
            break;
            case "12":
            $rand_value = "l";
            break;
            case "13":
            $rand_value = "m";
            break;
            case "14":
            $rand_value = "n";
            break;
            case "15":
            $rand_value = "o";
            break;
            case "16":
            $rand_value = "p";
            break;
            case "17":
            $rand_value = "q";
            break;
            case "18":
            $rand_value = "r";
            break;
            case "19":
            $rand_value = "s";
            break;
            case "20":
            $rand_value = "t";
            break;
            case "21":
            $rand_value = "u";
            break;
            case "22":
            $rand_value = "v";
            break;
            case "23":
            $rand_value = "w";
            break;
            case "24":
            $rand_value = "x";
            break;
            case "25":
            $rand_value = "y";
            break;
            case "26":
            $rand_value = "z";
            break;
            case "27":
            $rand_value = "0";
            break;
            case "28":
            $rand_value = "1";
            break;
            case "29":
            $rand_value = "2";
            break;
            case "30":
            $rand_value = "3";
            break;
            case "31":
            $rand_value = "4";
            break;
            case "32":
            $rand_value = "5";
            break;
            case "33":
            $rand_value = "6";
            break;
            case "34":
            $rand_value = "7";
            break;
            case "35":
            $rand_value = "8";
            break;
            case "36":
            $rand_value = "9";
            break;
            }
            return $rand_value;
            }
            /**
            * @Date: 15-Feb-2010
            * @Method : validEmailId
            * @Purpose: Validate email Id if filled
            * @Param:  $value
            * @Return: boolean
            **/
        
           function validEmailId($value = null) {
        
                 $v1 = trim($value);
                 if($v1 != "" && !eregi("^[\'+\\./0-9A-Z^_\`a-z{|}~\-]+@[a-zA-Z0-9_\-]+(\.[a-zA-Z0-9_\-]+){1,3}$",$v1)){
                return false; 
                 }
              return true;
           } 
        
            function file_exists_in_directory($directory, $pattern=false, $filename=false) {
        //echo $pattern."+++++".$directory;
        
                if(!isset($directory) OR !isset($filename) OR is_dir($directory) == false OR strlen($filename) < 0) return false;
        
                $returnval = false;
                if(false != ($handle = opendir($directory))) {
        
                while (false !== ($file = readdir($handle))) {
        
                if ($file != "." && $file != "..") {
        
                if($pattern != false) {
        
                if(preg_match("$pattern", $file) > 0 ) {
                $returnval = $file;
                break;
                  }
                } else {
                if($file == $filename) {
                 $returnval = $file;
                  break;
                                }
                            }
                        }
                    }
                }
                closedir($handle);
                return $returnval;
            }
            // return an array of files in directory else false if none found
        
            function get_files($directory, $pattern = false) {
        
                if(!isset($directory) OR is_dir($directory) == false ) return false;
                $returnval = array();
        
                if(false != ($handle = opendir($directory))) {
                while (false !== ($file = readdir($handle))) {
        
                if ($file != "." && $file != "..") {
        
                if($pattern != false) {
        
                if(preg_match("$pattern", $file) > 0 ) {
                $returnval[] = $file;
                     }
                    }else{
                        $returnval[] = $file;
                    }
                    }
                }
                }
                closedir($handle);
                return $returnval;
            }
        
            /**
            * Makes directory, returns TRUE if exists or made
            * @param string $pathname The directory path.
            * @return boolean returns TRUE if exists or made or FALSE on failure.
            */
            function mkdir_recursive($path, $mode = 0777)
        
            {	
                $basicPath = ROOT.DS."app".DS."webroot".DS."contents".DS;
                $dirs = explode(DS , $path);
                $count = count($dirs);
                $path = '';
                for ($i = 0; $i < $count; ++$i) {
                $path .= $dirs[$i].DS;
                if (!is_dir($basicPath.rtrim($path,"/"))){
                mkdir($basicPath.$path, $mode);
                    }
                }
                return true;
            }
            /**
            * Remove directory, returns TRUE if exists or made
            * @param string $pathname The directory path.
            */
               function rmdir_recursive($dir) {
        
                $basicPath = ROOT.DS."app".DS."webroot".DS."contents".DS;
                if(is_dir($basicPath.$dir)){
                $files = scandir($basicPath.$dir);
                array_shift($files);    // remove '.' from array
                array_shift($files);    // remove '..' from array
        
                foreach ($files as $file) {
                $file = $basicPath.$dir .DS. $file;
                if (is_dir($file)) {
                rmdir_recursive($file);
                rmdir($file);
                } else {
        
                unlink($file);
                      }
                }
                rmdir($basicPath.$dir);
                }
            }
            
            function file_extension($filename)
            {
                $path_info = pathinfo($filename);
                return $path_info['extension'];
            }
            
            //changes sql timestamp format to compare
            function expiredTime($modified_date){
                        $diff = abs(time()-strtotime($modified_date));
                         if ($diff < 0)$diff = 0;
                         $dl = floor($diff/60/60/24);
                         $hl = floor(($diff - $dl*60*60*24)/60/60);
                         $ml = floor(($diff - $dl*60*60*24 - $hl*60*60)/60);
                         $sl = floor(($diff - $dl*60*60*24 - $hl*60*60 - $ml*60));
                       // OUTPUT
                       $hl = ($dl *24)+$hl;
                       $return = array('hours'=>$hl, 'minutes'=>$ml, 'seconds'=>$sl);
                       return $return;
            }
            
          function dateDiff($time1, $time2, $precision = 6) {
            // If not numeric then convert texts to unix timestamps
            if (!is_int($time1)) {
            $time1 = strtotime($time1);
            }
            if (!is_int($time2)) {
              $time2 = strtotime($time2);
            }
            // If time1 is bigger than time2
            // Then swap time1 and time2
            if ($time1 > $time2) {
              $ttime = $time1;
              $time1 = $time2;
              $time2 = $ttime;
            }
         
            // Set up intervals and diffs arrays
            $intervals = array('year','month','day','hour','minute','second');
            $diffs = array();
         
            // Loop thru all intervals
            foreach ($intervals as $interval) {
              // Set default diff to 0
              $diffs[$interval] = 0;
              // Create temp time from time1 and interval
              $ttime = strtotime("+1 " . $interval, $time1);
              // Loop until temp time is smaller than time2
              while ($time2 >= $ttime) {
            $time1 = $ttime;
            $diffs[$interval]++;
            // Create new temp time from time1 and interval
            $ttime = strtotime("+1 " . $interval, $time1);
              }
            }
         
            $count = 0;
            $times = array();
            // Loop thru all diffs
            foreach ($diffs as $interval => $value) {
              // Break if we have needed precission
              if ($count >= $precision) {
            break;
              }
              // Add value and interval 
              // if value is bigger than 0
              if ($value > 0) {
            // Add s if value is not 1
            if ($value != 1) {
              $interval .= "s";
            }
            // Add value and interval to times array
            $times[] = $value . " " . $interval;
            $count++;
              }
            }
         
            // Return string with times
            return implode(", ", $times);
          }
          
           /** 	 @Date: 04-April-2013
                *@Method : getuser (This 
                *@Purpose: This function is used to active user list.
            **/
            function getuser(){
                 App::import("Model","User");
                 $this->User= new User;
                 $result= $this->User->find('list', array(
                 'conditions' => array('status'=>'1'),
                 'fields' => array('id','user_name'),
                 'order'=>'user_name  ASC'
            ));
                 $result = array('' => '--Select--') + $result;
                // pr($result);
                 return $result;
            }
            
            
              // get permissions for menus 
            function menu_permissions($controller,$action,$userID) {
                  App::import('Model','Permission');
                  $this->Permission= new Permission;
                  $cond = "WHERE controller='$controller' AND action='$action'";
                  $permissionsResult = $this->Permission->find('first',array(
                   'fields' => array('user_id'),
                   'conditions' => $cond,
                  ));
                  if(!empty($permissionsResult)){
                   $users = explode(",",$permissionsResult['Permission']['user_id']);
                    if (in_array($userID,$users)) {
                    return true;
                   }
                   else{
                    return false;
                   }
                  }
                  return true;
                  
            }
            
            function getSecurityQuestions($id=null){
                $result = array(
                '1'=>'Model of first car',
                '2'=>"Mothers maiden name",
                '3'=>'Name of best friend in high school',
                '4'=>'Name of first pet',
                '5'=>'Fathers middle name',
                );
                if(!empty($id)){
                    return $result[$id];
                }else{
                    return $result;
                }
            }
            
            function time_elapsed_string($datetime, $full = false) {
            $now = new DateTime;
            $ago = new DateTime($datetime);
            $diff = $now->diff($ago);
        
            $diff->w = floor($diff->d / 7);
            $diff->d -= $diff->w * 7;
        
            $string = array(
                'y' => 'year',
                'm' => 'month',
                'w' => 'week',
                'd' => 'day',
                'h' => 'hour',
                'i' => 'minute',
                's' => 'second',
            );
            foreach ($string as $k => &$v) {
                if ($diff->$k) {
                    $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
                } else {
                    unset($string[$k]);
                }
            }
        
            if (!$full) $string = array_slice($string, 0, 1);
            return $string ? implode(', ', $string) . ' ago' : 'just now';
        } 
        
    public function android_send_notification($registatoin_ids,$message,$type=NULL) {
          $url = 'https://android.googleapis.com/gcm/send'; 
		   $url = 'https://fcm.googleapis.com/fcm/send';		  
                $fields = array(
                     'registration_ids' => $registatoin_ids,
                    'data' => $message
                );
           if(!defined('GOOGLE_API_KEY')){
           $GOOGLE_API_KEY = "AIzaSyBY5-p5n4I3rhCeE1HHHT6ziDDo6Gdrx4I";                           
          }
		  if($type =='D'){
			$GOOGLE_API_KEY = "AIzaSyBFCIlg5t0Ec4n5L3gDFAvMC3hCTcy3LKc";
		  }else{
			$GOOGLE_API_KEY = "AIzaSyB4E-783mPoVhBwzOpgn5RVmKNTbMt1OI8"; //passenger key
		  }
           $headers = array(
               'Authorization: key='.$GOOGLE_API_KEY,
                'Content-Type: application/json'
           );
				// Open connection
				$ch = curl_init();
                // Set the url, number of POST vars, POST data
                curl_setopt($ch, CURLOPT_URL, $url);       
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);        
                // Disabling SSL Certificate support temporarly
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));        
                // Execute post
                $result = curl_exec($ch);
                if ($result === FALSE){
                    die('Curl failed: ' . curl_error($ch));
                }
        
                // Close connection
                curl_close($ch);
           return $result;
     }
			
       
        public function iphone_send_notification($deviceIds,$message,$badge_count2){
             $result='';
        $body['aps'] = array(
        'alert' => $message['message'],
        'noti_for'=>$message,
        'sound' => 'default',
        'badge' => $badge_count2,
        );
        //pr($body);
        // Encode the payload as JSON
        $payload = json_encode($body);
        $ctx = stream_context_create();
        $passphrase = ''; 
        stream_context_set_option($ctx, 'ssl', 'local_cert', 'certs/CabooAppLive.pem');   
        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase); $fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 4, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
         if($fp){
				$message = chr(0) . pack('n', 32) . pack('H*', $deviceIds[0]) . pack('n', strlen($payload)) . $payload;
				$result = fwrite($fp, $message, strlen($message));
               fclose($fp);
            } 
                
           return $result;
            
          }
          function tr_to_utf($text){
           $text = trim($text);
           $search = array('Ü', 'Þ', 'Ð', 'Ç', 'Ý', 'Ö', 'ü', 'þ', 'ð', 'ç', 'ý', 'ö');
           $replace = array('Ãœ', 'Åž', '&#286;ž', 'Ã‡', 'Ä°', 'Ã–', 'Ã¼', 'ÅŸ', 'ÄŸ', 'Ã§', 'Ä±', 'Ã¶');
           $new_text = str_replace($search, $replace, $text);
           return $new_text;
          }

    function getDistance($from, $to, $key = NULL) {
    
            if (is_null($key))
                $index = 0;
            else
                $index = $key;    
            //$keys_all = array('AIzaSyAp_1Skip1qbBmuou068YulGux7SJQdlaw', 'AIzaSyDczTv9Cu9c0vPkLoZtyJuCYPYRzYcx738', 'AIzaSyBZtOXPwL4hmjyq2JqOsd0qrQ-Vv0JtCO4', 'AIzaSyDXdyLHngG-zGUPj7wBYRKefFwcv2wnk7g', 'AIzaSyCibRhPUiPw5kOZd-nxN4fgEODzPgcBAqg', 'AIzaSyB1Twhseoyz5Z6o5OcPZ-3FqFNxne2SnyQ', 'AIzaSyCgHxcZuDslVJNvWxLs8ge4syxLNbokA6c', 'AIzaSyDH-y04IGsMRfn4z9vBis4O4LVLusWYdMk', 'AIzaSyB1Twhseoyz5Z6o5OcPZ-3FqFNxne2SnyQ', 'AIzaSyBQ4dTEeJlU-neooM6aOz4HlqPKZKfyTOc'); //$this->dirKeys;
            $keys_all = array('AIzaSyC_ZtEFG0IsH7gayms0z_qeUX4eSagwSt8');
            $url = 'https://maps.googleapis.com/maps/api/directions/json?origin=' . $from['lat'] . ',' . $from['long'] . '&destination=' . $to['lat'] . ',' . $to['long'] . '&sensor=false&key=' . $keys_all[$index];
    
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            $result = curl_exec($ch);
            $arr = json_decode($result, true);
            if (!empty($arr['routes'])) {
                if (!is_array($arr['routes'][0])) {
                    $index++;
                    $arr['key_arr'] = array('key' => $keys_all[$index], 'index' => $index, 'all' => $keys_all);
                    if (count($keys_all) > $index)
                        return $this->getDistance($from, $to, $index);
                    else
                    pr($arr['routes'][0]);
                    $distance_in_mtr = $arr['routes'][0]['legs'][0]['distance']['value'];
                    $duration_in_sec = $arr['routes'][0]['legs'][0]['duration']['value'];
                       // pr($duration_in_sec);
                }
            }
            $arr['key_arr'] = array('key' => $keys_all[$index], 'index' => $index, 'all' => $keys_all);
           // pr($arr);
        }
        
        function GetDrivingDistance($from, $to,$key = NULL)
        {
            $dist = 0;
            $time = 0;
            $distance_in_meter = 0;
			$distance_in_miles=0;
            $duration_in_min = 0;
            if (is_null($key))
            $index = 0;
        else
            $index = $key;
            //AIzaSyC_ZtEFG0IsH7gayms0z_qeUX4eSagwSt8
        //$keys_all = array('AIzaSyAp_1Skip1qbBmuou068YulGux7SJQdlaw', 'AIzaSyDczTv9Cu9c0vPkLoZtyJuCYPYRzYcx738', 'AIzaSyBZtOXPwL4hmjyq2JqOsd0qrQ-Vv0JtCO4', 'AIzaSyDXdyLHngG-zGUPj7wBYRKefFwcv2wnk7g', 'AIzaSyCibRhPUiPw5kOZd-nxN4fgEODzPgcBAqg', 'AIzaSyB1Twhseoyz5Z6o5OcPZ-3FqFNxne2SnyQ', 'AIzaSyCgHxcZuDslVJNvWxLs8ge4syxLNbokA6c', 'AIzaSyDH-y04IGsMRfn4z9vBis4O4LVLusWYdMk', 'AIzaSyB1Twhseoyz5Z6o5OcPZ-3FqFNxne2SnyQ', 'AIzaSyBQ4dTEeJlU-neooM6aOz4HlqPKZKfyTOc'); //$this->dirKeys;
         $keys_all = array('AIzaSyCch5rvrwr20HC4KRHlk4nOtIpf-hAi3ek'); //
            $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$from['lat'].",".$from['long']."&destinations=".$to['lat'].",".$to['long']."&mode=driving&language=pl-PL&key". $keys_all[$index];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $response = curl_exec($ch);
            curl_close($ch);
            $response_a = json_decode($response, true);
            if($response_a['status'] == 'OK'){
                if($response_a['rows'][0]['elements'][0]['status'] != 'ZERO_RESULTS'){
                $dist = $response_a['rows'][0]['elements'][0]['distance']['text'];
                $time = $response_a['rows'][0]['elements'][0]['duration']['text'];
                $distance_in_meter = $response_a['rows'][0]['elements'][0]['distance']['value'];
				$distance_in_miles = $distance_in_meter*0.000621371192;
                $duration_in_sec = $response_a['rows'][0]['elements'][0]['duration']['value'];
				$duration_in_min = $duration_in_sec / 60;
                }
            }
            return array('distance' => $dist, 'time' => $time,'distance_in_miles'=> $distance_in_miles,'durantion_in_min'=>$duration_in_min);
        }
		
		function getaddress($lat,$lng)
		{
			$keys_all = 'AIzaSyCch5rvrwr20HC4KRHlk4nOtIpf-hAi3ek'; //
			$url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($lat).','.trim($lng).'&sensor=false';
			$json = @file_get_contents($url);
			$data=json_decode($json);
			$status = $data->status;
			if($status=="OK")
			return $data->results[0]->formatted_address;
			else
			return false;
		}
		
		function curl_initialization($amount,$stripe_token){
					$apiKey = 'sk_test_UKJBC7pnuUJ6ZssmV86D4rpQ'; // secret key  for test  "sk_test_UKJBC7pnuUJ6ZssmV86D4rpQ"
					$curl = curl_init();
					$name=array(
							'Authorization: Bearer ' . $apiKey
						);
					curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
					curl_setopt($curl, CURLOPT_URL,'https://api.stripe.com/v1/charges');
					curl_setopt($curl, CURLOPT_POST,1);
					curl_setopt($curl, CURLOPT_HTTPHEADER,$name);
					$data=array(
							"amount" =>$amount,
							"currency" => 'BZD',
							"source" => $stripe_token,
							"description" => 'Example Testing Charge'
						);
					curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
					$resp = curl_exec($curl);
					curl_close($curl);
					$data=json_decode($resp);					
					if(!isset($data->status))
					{
					  $data->status='failed';
					}
					if($data->status == "succeeded")
					{
						$data = $data;
					}
					return $data;
		}


}



?>