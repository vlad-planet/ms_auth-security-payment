<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

include_once(dirname(__FILE__) . '/../models/Authentication.php');

class AuthenticationTest extends TestCase {

	public function testAuthenticationPass() {
		
		$_model = new Authentication();
		
		$id = $_model->authenticate('application1', 'abc123', $_model->logins);
		
		$this->assertEquals(1, $id);
	}
	
	public function testAuthenticationFail() {
		
		$_model = new Authentication();
		
		$id = $_model->authenticate('application1', 'abc1234', $_model->logins);

		$this->assertFalse($id);
	}
	
	public function testTokenGeneration() {
		
		$_model = new Authentication();
		
		$token = $_model->generateToken();
		
		$this->assertTrue(true);
	}
	
	public function testStoringToken() {
		
		$_model = new Authentication();
		
		$token = $_model->generateToken();
		
		$_model->storeToken($token, strtotime('+1 minute'));

		$this->assertTrue(isset($_model->tokens[$token]));
	}
	
	public function testConsumingToken() {
		
		$_model = new Authentication();
		
		$token = $_model->generateToken();
		
		$_model->storeToken($token, strtotime('+1 minute'));
		
		$_model->consumeToken($token);
		
		$this->assertFalse(isset($_model->tokens[$token]));
	}
	
	public function testHasExpiredFalse() {
		
		$_model = new Authentication();
		
		$token = $_model->generateToken();
		
		$_model->storeToken($token, time() + 5);
		
		$expired = $_model->hasExpired($token, time());
		
		$this->assertFalse($expired);
	}
	
	public function testHasExpiredTrue() {
		
		$_model = new Authentication();
		
		$token = $_model->generateToken();
		
		$_model->storeToken($token, time() + 4);
		
		sleep(5); //Set to expire in 4 seconds
		
		$expired = $_model->hasExpired($token, time());
		
		$this->assertTrue($expired);
	}
	
	public function testHasPriviligesAll() {
		
		$_model = new Authentication();
		
		$token = $_model->generateToken();
		
		$_model->storeToken($token, strtotime('+1 minute'));
		
		$hasAccess = $_model->checkPrivileges($token, '*');
		
		$this->assertTrue($hasAccess);
	}
	
	public function testHasPriviligesVideoTrue() {
		
		$_model = new Authentication();
		
		$token = $_model->generateToken();
		
		$_model->storeToken($token, strtotime('+1 minute'), array('video', 'image', 'messenging'));
		
		$hasAccess = $_model->checkPrivileges($token, 'video');
		
		$this->assertTrue($hasAccess);
	}
	
	public function testHasPriviligesVideoFalse() {
		
		$_model = new Authentication();
		
		$token = $_model->generateToken();
		
		$_model->storeToken($token, strtotime('+1 minute'), array('image', 'messenging'));
		
		$hasAccess = $_model->checkPrivileges($token, 'video');
		
		$this->assertFalse($hasAccess);
	}
	
	public function testValidToken() {
		
		$_model = new Authentication();
		
		$token = $_model->generateToken();
		
		$_model->storeToken($token, strtotime('+1 minute'));
		
		$hasAccess = $_model->validateToken($token, time(), 'video');
		
		$this->assertFalse($hasAccess);
	}
	
	public function testValidTokenPrivilegesFalse() {
		
		$_model = new Authentication();
		
		$token = $_model->generateToken();
		
		$_model->storeToken($token, strtotime('+1 minute'), array('image', 'messenging'));
		
		$hasAccess = $_model->validateToken($token, time(), 'video');
		
		$this->assertFalse($hasAccess);
	}
	
	public function testValidTokenPrivilegesExpired() {
		
		$_model = new Authentication();
		
		$token = $_model->generateToken();
		
		$_model->storeToken($token, time() + 4, array('*'));
		
		sleep(5);
		$hasAccess = $_model->validateToken($token, time(), 'video');
		
		$this->assertFalse($hasAccess);
	}

}
