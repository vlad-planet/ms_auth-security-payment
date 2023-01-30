<?php
include_once('vendor/autoload.php');

use prodigyview\system\Security;
use prodigyview\network\Socket;

include_once('./models/Payment.php');
include_once('./services/AuthenticationService.php');

$model = new Payment();

//Создать Сервер
$server = new Socket('localhost', 8601, array(
	'bind' => true,
	'listen' => true
));

//Запустить Сервер
$server->startServer('', function($message) {

	echo "Processing...\n";

	$requested_privilege = '*';

	//Расшифровать зашифрованные полученные данные
	Security::init();
	$message = Security::decrypt($message);

	//Преобразовать данные в массив
	$data = json_decode($message, true);

	$response = array('status' => 'error', 'message' => 'Invalid Command');

	$action = (isset($data['action'])) ? $data['action'] : '';

	//Проверка полученного токена
	if (isset($data['token']) && AuthenticationService::hasAccess($data['token'], $requested_privilege)) {

		global $model;
		
		//Выполнить платежные действия на покупку товара
		if ($action == 'charge') {

			//Проверить токен полученный от платежной системы со стороны вебклиента
			if ($model->checkNounce($data['nounce'])) {
				
				//Занести транзакцию в БД
				$id = $model->charge($data['amount'], $data['nounce'], array('product_id'=> 'zzZZ', 'account_id'=>1));
				$response = array('status' => 'success', 'message' => 'Charge Successful', 'id' => $id );
				
			} else {
				$response = array('status' => 'error', 'message' => 'Invalid Nounce');
			}
		}
		
		//Выполнить платежные действия на возврат товара
		if ($action == 'refund') {
			
			//Проверить транзакция купленного товара
			if ($model->refund($data['id'])) {
				
				$response = array('status' => 'success', 'message' => 'Refund Successful', 'id' => $data['id']);
				
			} else {
				$response = array('status' => 'error', 'message' => 'Unable To Peform Refund');
			}
		}
		
	} else {
		$response = array('status' => 'error', 'message' => 'Invalid Token On Purchase');
	}
	
	//Преобразовать данные в JSON
	$response = json_encode($response);
		
	//Вернуть зашифрованное сообщение
	return Security::encrypt($response);
	
}, 'closure');