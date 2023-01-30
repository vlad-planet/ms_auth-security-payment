<?php
include_once('vendor/autoload.php');

use prodigyview\system\Security;
use prodigyview\network\Socket;

include_once('./models/Authentication.php');

$model = new Authentication();

//Create The Server
$server = new Socket('localhost', 8600, array(
	'bind' => true,
	'listen' => true
));

//Start The Server
$server->startServer('', function($message) {
	
	echo "Processing...\n";

	//Decrypt our encrypted message
	Security::init();
	$message = Security::decrypt($message);

	//Turn the data into an array
	$data = json_decode($message, true);

	//Default response
	$response = array('status' => 'error', 'message' => 'Nothing found.');
	
	//Execute A Request If Exist
	if(isset($data['request'])) {
		
		global $model;

		//Authenticate Request A Token
		if($data['request'] == 'authenticate') {

			if($id = $model->authenticate($data['login'], $data['password'])) {
				
				$token = $model->createAccessToken($id, strtotime('+1 minute'));
				$response = array('status' => 'success', 'token' => $token, 'message' => 'Authenticate Access Granted' );
			
			} else {
				$response = array('status' => 'error', 'message' => 'Invalid Login');
			}
		}

		//Authorize a token based action
		else if($data['request'] == 'authorize') {

			if($model->validateToken($data['token'], time(), $data['privilege'])) {
				
				$model->consumeToken($data['token']);
				$response = array('status' => 'success', 'token' => $data['token'], 'message' => 'Authorization Access Granted');
			
			} else {
				$response = array('status' => 'error', 'message' => 'Invalid Token On Authorization');
			}
		}
	}

	//JSON encode the response
	$response =json_encode($response);
	
	//Return an encrypted message
	return Security::encrypt($response);

}, 'closure');



