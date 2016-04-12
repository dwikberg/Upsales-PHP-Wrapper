<?php

	require('curl.php');
	
	class Upsales {

		// Enter your Upsales User credentials here
		private $upsales_user = '';
		private $upsales_pw = '';

		private $upsales_headers = array('Content-Type: application/json','Accept-Encoding: utf-8');
		private $upsalesAccountID = '';
		private $sessionToken="";
		private $baseURL="https://power.upsales.com/api/v2/";

		function doLogin() {

			if ($this->upsales_user=='' || $this->upsales_pw=='') {

				throw new Exception('Missing login credentials');

			}

			$data = array('email' => $this->upsales_user, 'password' => $this->upsales_pw);

			// Post login credentials to login service
			$output = curlPost($this->baseURL . "session",$this->upsales_headers,$data);

			if (!$output) {

				throw new Exception('Error creating Upsales session');

			}

			// Save Token for future API Calls
			$this->sessionToken = $output["data"]["token"];

			sleep(2);

			$output = curlGet($this->baseURL . "self/?token=" . $this->sessionToken, 
			$this->upsales_headers);

			// Save Upsales Account ID and update base URL for future calls.			
			$this->upsalesAccountID = $output["data"]["clients"][0]["clientId"];
			$this->baseURL = "https://power.upsales.com/api/v2/" . $this->upsalesAccountID  . "/";

		} 

		public function addAccount($data=array()) {

			return $response = $this->upsalesCurl("POST","accounts",null,$data);
			
		}

		public function addContact($data=array()) {

			return $this->upsalesCurl("POST","contacts",null,$data);

		}

		public function addActivity($data=array()) {

			return $this->upsalesCurl("POST","activities",null,$data);
			
		}

		public function addOrder($data=array()) {

			return $this->upsalesCurl("POST","orders",null,$data);
			
		}

		public function sendMail($to,$subject,$from,$fromName,$body,$account_id,$contact_id) {

			date_default_timezone_set('Etc/Universal');
			$current_date = date('Y-m-d') . "T" . date('H:i:s') . ".000Z";

			$data = array('mail' => array('body' => $body, 
		    	'client' => $account_id,'contact' => $contact_id,'date' => $current_date,'from' => $from,'fromName' => $fromName,
		    	'subject' => $subject,'template' => 0, 'to' =>$to,'type' => 'out'));

			return $this->upsalesCurl("POST","mail",null,$data);

		}

		public function sendMailTemplate($to,$from,$fromName,$account_id,$contact_id,$template_id) {

			date_default_timezone_set('Etc/Universal');
			$current_date = date('Y-m-d') . "T" . date('H:i:s') . ".000Z";

		    $data = array('mail' => array(
		    			'type' => 'out',
		    			'body' => '',
		    			'subject' => '',
		    			'from' => $from,
		    			'fromName' => $fromName,
		    			'to' => $to,
		    			'date' => $current_date,
		    			'contact' => $contact_id,
		    			'client' => $account_id,
		    			'template' => $template_id
		    			)
		    		);

		    return $this->upsalesCurl("POST","mail",null,$data);

		}

		public function getAccounts($params=array()) {

			return $response = $this->upsalesCurl("GET","accounts",$params);
			
		}

		public function getContacts($params=array()) {

			return $response = $this->upsalesCurl("GET","contacts",$params);
			
		}

		public function getActivities($params=array()) {

			return $response = $this->upsalesCurl("GET","activities",$params);
			
		}

		// Used to get email clicks, web site visits etc.

		public function getEvents($params) {

			$upsalesParams = array("q"=>$params);

			return $response = $this->upsalesCurl("GET","events",$upsalesParams);
			
		}


		public function updateAccount($id, $data=array()) {

			return $response = $this->upsalesCurl("PUT", "accounts", array("id" => $id), $data);

		}

		public function updateContact($id, $data=array()) {

			return $response = $this->upsalesCurl("PUT","contacts", array("id" => $id), $data);

		}

		public function updateActivity($id, $data=array()) {

			return $response = $this->upsalesCurl("PUT","activities", array("id" => $id), $data);

		}

		// Wrapper for doing Curl requests
		private function upsalesCurl($method, $endpoint, $params = array(), $data = array()) {

			$url ="";
			
			if($method == "POST" || $method == "GET") {
				
				$url = $this->baseURL . $endpoint . "/?token=" . $this->sessionToken;

			} elseif($method=="PUT" || $method=="DELETE") {

				$id = $params["id"];
				$url = $this->baseURL . $endpoint . "/" . $id . "/?token=" . $this->sessionToken;

			}

			if($params && is_array($params) && ($method == "POST" || $method =="GET")) {

				foreach ($params as $key => $value) {
					$url = $url . "&" . $key . "=" . $value;

				}

			}

			if ($method=="POST") {

				return curlPost($url,$this->upsales_headers,$data);

			} elseif($method=="GET") {

				return curlGet($url,$this->upsales_headers);
				
			} elseif($method=="PUT") {

				return curlPut($url,$this->upsales_headers,$data);

			}

		}

		// Constructor method to login, set token, API Base URL and Upsales Account ID
		function Upsales() {

				$this->doLogin();
			
		}

}

?>