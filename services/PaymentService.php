<?php
/**
 * Формирование и отправка запросов на Платежный Шлюз
 */
include_once('vendor/autoload.php');

use prodigyview\network\Request;
use prodigyview\network\Response;
use prodigyview\network\Socket;
use prodigyview\system\Security;

include_once('AuthenticationService.php');

class PaymentService {

	/**
	 * Формироввание полученных данных от Клиента
	 *
	 * @param string $action
	 * @param Request $request
	 *
	 * @return mixed Возвращает ответ от клиента
	 */
	public static function sendRequest(string $action, Request $request) {

		$response = array();

		if ($data = $request->getRequestData('array')) {
			
			$data['action'] = $action;
			
			//Отправить данные на микросервис
			$result = Self::sendToServer($data);

			//Сформировать ответ из микросервиса
			$response = array('status' => $result);
		} else {
			$response = array('status' => 'Unable To Perform' . $action);
		}
		
		//Отправить ответ клиенту, который получил доступ к API
		return Self::sendResponse(json_encode($response));
	}


	/**
	 * Отправить данные на Платежный Шлюз 
	 *
	 * @param array $message
	 * 
	 * @return string Возвращает сообщение от платежного микросервиса
	 */
	public static function sendToServer(array $message) : string {
		
		// Авторизация и получение токена
		$token = AuthenticationService::getToken('application1', 'abc123');
		$message['token'] = $token;

		//Специальный сокет, который подключается к нашему платежному шлюзу
		$socket = Self::getSocket(8601);
		
		$message = json_encode($message);
		
		//Зашифровать данные
		Security::init();
		$message = Security::encrypt($message);

		//Отправить данные на обработку в платежный шлюз
		$result = $socket->send($message);

		//Расшивровать полученный ответ
		$result = Security::decrypt($result);
		
		//Закрыть соединение
		$socket->close();

		return $result;
	}


	/**
	 * Отправить ответ api клиенту
	 *
	 * @param string $message
	 * @param int $status
	 *
	 * @return void
	 */
	public static function sendResponse(string $message, int $status = 200) : void {
		echo Response::createResponse(200, $message);
	}


	/**
	 * Открыть сокет для микросервиса
	 *
	 * @param int $port
	 *
	 * @return object Возвращает экземпляр класса Socket
	 */
	public static function getSocket(int $port) : object  {
		
		$host = '127.0.0.1';
		return new Socket($host, $port, array('connect' => true));
	}

}