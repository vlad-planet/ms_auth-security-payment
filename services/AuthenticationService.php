<?php
/**
 * Получения токена для Аутетификации
 */
use prodigyview\network\Socket;
use prodigyview\system\Security;
 
class AuthenticationService {
	
	/**
	 * Авторизация приложения
	 * 
	 * @param string $login
	 * @param string $password
	 * 
	 * @return mixed Возвращает токен в случае успеха, в противном случае false
	 */
	public static function getToken(string $login, string $password) {
		
		//кодирует сообщение с запросом токена и отправкой логина и пароля
		$message = json_encode(array('login'=> $login, 'password' => $password, 'request' => 'authenticate'));

		return Self::authentication($message);
	}
	
	
	/**
	 * Проверяет, имеет ли токен правильный доступ с соответствующим действием.
	 * 
	 * @param string $token
	 * @param string $action
	 * 
	 * @return bolean Возвращает значение true, если токен действителен, в противном случае значение false
	 */
	public static function hasAccess(string $token, string $requested_privilege) : bool {
		
		//кодирует сообщение с полученным токена и запросом на авторизацию, и прав доступа
		$message = json_encode(array('token'=> $token, 'request' => 'authorize', 'privilege' => $requested_privilege));

		return Self::authentication($message);
	}


	/**
	 * Отправка данных на Микросервис Аутентификации.
	 * 
	 * @param string $message
	 * 
	 * @return string возвращает в сообщении токен, или описание ошибки
	 */
	public static function authentication(string $message) : string {
		
		//Специальный сокет, который подключается к нашей службе аутентификации
		$socket = Self::getSocket(8600);

		//Зашифровать данные
		Security::init();
		$message = Security::encrypt($message);
		
		//Отправить данные на обработку для Аутетификации
		$result = $socket->send($message);
	
		//Расшивровать полученный ответ
		$result = Security::decrypt($result);

		//Закрыть соединение
		$socket->close();
		
		$response = json_decode($result, true);
		
		//var_dump($response);
		if (isset($response['error'])) {
			return false;
		} else {
			return $response['token'];
		}
	}
	
	/**
	 * Открыть сокет для микросервиса
	 *
	 * @param int $port
	 *
	 * @return Socket object
	 */
	public static function getSocket(int $port) : object  {
		
		$host = '127.0.0.1';
		return new Socket($host, $port, array('connect' => true));
	}
	
}