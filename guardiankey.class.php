<?php
// Please run "register.php" for generate your configuration
        $GKconfig = array(
                                        'email' => "ge.sielbernardes@gmail.com",
                                        'hashid' => "2958f41c52fceb885a2217ff2d38d7c2",
                                        'key' => "XIEda0IIxk7Ruk8lNYg3mrbX4ENQPPIwQhjaZjvHO+w=",
                                        'iv' => "ZaLu9/GwDqpZjFoucd5k7A==",
                                        'orgid' => "2958f41c52fceb885a2217ff2d38d7c2",
                                        'groupid' => "2958f41c52fceb885a2217ff2d38d7c2",
                                        'reverse' => "1",
                                        );


class guardiankey {

   function _json_encode($obj) {
         array_walk_recursive($obj, function (&$item, $key) {
                                           $item = utf8_encode($item);
                                     });
    }

   function create_message($username) {
			global $GKconfig;
        $keyb64    = $GKconfig['key'];
        $ivb64 	   = $GKconfig['iv'];
        $hashid    = $GKconfig['hashid'];
        $orgid     = $GKconfig['orgid'];
        $authgroupid    = $GKconfig['groupid'];
        $reverse   = $GKconfig['reverse'];
        $timestamp = time();
        if(strlen($hashid)>0){
          $key=base64_decode($keyb64);
          $iv=base64_decode($ivb64);
          
          $json = new stdClass();
          $json->generatedTime=$timestamp;
          $json->agentId=$hashid;
          $json->organizationId=$orgid;
          $json->authGroupId=$authgroupid;
          $json->service=$GKconfig['service'];;
          $json->clientIP=$_SERVER['REMOTE_ADDR'];
          $json->clientReverse = ($reverse==1)?  gethostbyaddr($json->clientIP) : "";
          $json->userName=$username;
          $json->authMethod="";
          $json->loginFailed="0";
          $json->userAgent=substr($_SERVER['HTTP_USER_AGENT'],0,500);
          $json->psychometricTyped="";
          $json->psychometricImage="";
          $tmpmessage = $this->_json_encode($json);
		  $blocksize=8;
          $padsize = $blocksize - (strlen($tmpmessage) % $blocksize);
          $message=str_pad($tmpmessage,$padsize," ");
		  $cipher = openssl_encrypt($message, 'aes-256-cfb8', $key, 0, $iv);
		  return $cipher;
		}
	}
	
    function sendevent($username)  {
 	    global $GKconfig;
        $hashid  = $GKconfig['hashid'];
	    $cipher  = $this->create_message($username);
        $payload = $GKconfig['hashid']."|".$cipher;
        $socket  = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        socket_sendto($socket, $payload, strlen($payload), 0, "collector.guardiankey.net", "8888");
    }
    
    function checkaccess($username) {
			global $GKconfig;
		$guardianKeyWS='https://api.guardiankey.io/checkaccess';
		$message = $this->create_message($username);
		$tmpdata->id = $GKconfig['hashid'];
		$tmpdata->message = $message;
		$data = $this->_json_encode($tmpdata);
				
        $ch = curl_init();
			curl_setopt($ch,CURLOPT_URL, $guardianKeyWS);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json',                                                                                
	            'Content-Length: ' . strlen($data)));  
			curl_setopt($ch,CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_REETURNTRANSFER, 1);
		$return = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);
		return $return;
		}
	}
?>
