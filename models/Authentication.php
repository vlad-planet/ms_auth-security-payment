<?php
/**
 * Модель сервиса Аутентификации
 */
class Authentication {

	//Фиктивные логины для системы. В производстве это была бы база данных
	//Содержит логин, пароль и привилегии, которыми обладает login
	//Пароль рекомендуется хранить в хеше
	public $logins = array(
		'1'=> array('login' =>'application1', 'password'=>'abc123', 'privileges' => array('*')),
		'2'=> array('login' =>'application2', 'password'=>'123456', 'privileges' => array('send_notification', 'send_email')),
		'3'=> array('login' =>'application3', 'password'=>'qwerty', 'privileges' => array('purchase', 'refund')),
	);

	//Фиктивные токены для системы. Также должны храниться в базе данных
	public $tokens = array();

	/**
	 * Подтверждение, что текущему логину разрешен доступ к системе
	 * 
	 * @param string $login
	 * @param string $password
	 * 
	 * @return mixed Возвращает идентификатор пользователя, в противном случае false
	 */
	public function authenticate(string $login, string $password) {

		foreach($this->logins as $key => $value) {
			if($login == $value['login'] && $password == $value['password']) {
				return $key;
			}
		}//end foreach
		
		return false;
	}

	/**
	 * Проверить, является ли переданный токен действительным;
	 * 
	 * @param string $token
	 * @param string $requested_privilege
	 * 
	 * @return boolean Возвращает значение true, если токен действителен, в противном случае значение false
	 */
	public function validateToken(string $token, int $current_time, string $requested_privilege) : bool {
		
		if(isset($this->tokens[$token]) && !$this->hasExpired($token, $current_time) && $this->checkPrivileges($token, $requested_privilege)) {
			return true;
		}

		return false;
	}

	/**
	 * Проверить, истек ли срок действия токена
	 * 
	 * @param string $token
	 * @param array $tokens The tokens
	 * 
	 * @return bool Возвращает true, если истек срок действия или не существует, в противном случае false
	 */
	public function hasExpired(string $token, int $current_time) : bool {
		
		if(!isset($this->tokens[$token]) || $this->tokens[$token]['expiration'] < $current_time) {
			return true;
		}
		return false;
	}

	/**
	 * Проверить, имеет ли токен правильные привилегии для доступа к действию
	 * 
	 * @param string $token
	 * @param string $requested_privilege
	 * 
	 * @return boolean Возвращает значение true, если токен имеет привилегии, в противном случае значение false
	 */
	public function checkPrivileges(string $token, string $requested_privilege) : bool {

		if (in_array($requested_privilege, $this->tokens[$token]['privileges'])) {
			return true;
		}

		return false;
	}

	/**
	 * Создать токен, связанный с логином
	 * 
	 * @param string $id
	 * @param int $expiration
	 * 
	 * @return string Возвращает токен
	 */
	public function createAccessToken(string $id, int $expiration) : string {

		$token = $this->generateToken();
		
		$this->storeToken($token, $expiration, $this->logins[$id]['privileges']);
		
		return $token;
	}

	/**
	 * Создать уникальный токен для использования
	 * 
	 * @return string $token Возвращает токен
	 */
	public function generateToken() : string {
		
		$token = prodigyview\system\Security::generateToken(65);
		return $token;
	}

	/**
	 * Добавить токен в "базу данных" для последующей проверки
	 * 
	 * @param string $token
	 * @param array  $privileges
	 * @param int $expiration
	 *
	 * @return void
	 */
	public function storeToken(string $token, int $expiration, array $privileges = array('*')) : void {
		
		//Set token in the token "database"
		$this->tokens[$token] = array(
			'expiration' => $expiration,
			'privileges' => $privileges
		);
	}

	/**
	 * Дективарует токен, чтобы его больше нельзя было использовать
	 * 
	 * @param string token
	 * @param array A database of tokens
	 */
	public function consumeToken(string $token) {
		
		unset($this->tokens[$token]);
	}

}