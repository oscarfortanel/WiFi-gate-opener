<?php

	class SessionOps{
		private $con;

		function __construct(){
			$COOKIE_DOMAIN = '/*DOMAIN_FOR COOKIE*/';
			require_once dirname(__FILE__).'/dbOps.php';
			require_once dirname(__FILE__).'/val.php';
		}


		// Creates session and sets session cookies, saves information to DB
		function logIn($username, $pass){
			$dbOps = new DBOps();

			if($res = $dbOps->simpleSelect("users", "u_name", "s", $username)){
				
				$rows = $dbOps->getRows();

				if (count($rows) == 1){
					if(password_verify($pass, $rows[0]['pw'])){
						$_SESSION['uName'] = $rows[0]['u_name'];

						// Generate random token
						$token = bin2hex(random_bytes(32));
						// Hash token to inset into data base
						$hashToken = password_hash($token, PASSWORD_DEFAULT);

						$valuesArray = [$hashToken, $rows[0]['id'], date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s'). ' + 30 days')), true];

						if($dbOps->simpleInsert("session_token", "id, token, u_id, expiration, valid", "?, ?, ?, ?", "sisi", $valuesArray)){
							$cookie = $rows[0]['id'] . ":" . $token;
							$mac = hash_hmac('sha256', $cookie, SECRET_KEY);
							$cookie .= ":" . $mac;
							$arr_cookie_options = array (
								'expires' => time() + 86400 * 30,
								'path' => '/',
								'domain' => $COOKIE_DOMAIN, // leading dot for compatibility or use subdomain
								'secure' => true,     // or false
								'httponly' => true,    // or false
								'samesite' => 'Strict' // None || Lax  || Strict
							);
							setcookie('sT', $cookie, $arr_cookie_options);
							$_SESSION['tID'] = $dbOps->getID();
							
						}

						return true;
					}
				}else{
					// TODO: user account not found or multiple matching records found, not good
				}
			}
		}

		// Gets session cookies if they exist and checks if they are valid, re-establishes session if it does not exist and
		// cookies are valid
		function verifySession(){
			$dbOps = new DBOps();

			list($userID, $token, $mac) = explode(":", $_COOKIE['sT']);

			if($res = $dbOps->simpleSelect("session_token", "u_id", "i", $userID)){
				$tokenRows = $dbOps->getRows();
				foreach($tokenRows as $tokenRow){
					if (!hash_equals(hash_hmac('sha256', $userID . ':' . $token, SECRET_KEY), $mac)) {
						return false;
					}
					if(password_verify($token, $tokenRow['token']) && $tokenRow['valid'] == 1){
						if($res = $dbOps->simpleSelect("users", "id", "i", $tokenRow['u_id'])){
							$userRows = $dbOps->getRows();
							if(count($userRows) == 1){
								$_SESSION['uName'] = $userRows[0]['u_name'];
								$_SESSION['tID'] = $tokenRow['id'];
								return true;
							}
						}

					}
				}
			}
			return false;
		}

		// Destroys session, cookies, and invalidates session token in DB
		function logOut(){
			$dbOps = new DBOps();
			if(isset($_SESSION['tID']) && isset($_COOKIE['sT'])){
				list($userID, $token, $mac) = explode(":", $_COOKIE['sT']);
				if (!hash_equals(hash_hmac('sha256', $userID . ':' . $token, SECRET_KEY), $mac)) {
					return false;
				}
				if($dbOps->simpleSelect("session_token", "id", "i", $_SESSION['tID'])){
					$tokenRows = $dbOps->getRows();
					if(count($tokenRows) == 1){
						if($tokenRows[0]['u_id'] == $userID){
							$arr_cookie_options = array (
								'expires' => -1,
								'path' => '/',
								'domain' => $COOKIE_DOMAIN, // leading dot for compatibility or use subdomain
								'secure' => true,     // or false
								'httponly' => true,    // or false
								'samesite' => 'Strict' // None || Lax  || Strict
							);
							setcookie('sT', null, $arr_cookie_options);
							session_destroy();
							$dbOps->simpleDelete("session_token", "id", "i", $_SESSION['tID']);
						}
					}
				}
			}
		}


		function clickAction(){
			$url = '/*ADDRESS_FOR_ESP32/URL_KEY*/';
			$data = array('key1' => 'value1', 'key2' => 'value2');

			// Using POST, will use this later
			$options = array(
    			'http' => array(
        		'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        		'method'  => 'POST',
        		'content' => http_build_query($data)
    			)
			);
			$context  = stream_context_create($options);
			$result = file_get_contents($url, false, $context);
		}
	}

?>