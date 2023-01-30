<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

include_once(dirname(__FILE__) . '/../models/Payment.php');


final class PaymentTest extends TestCase {
	
	public function testNounceExistTrue() {
		
		$_model = new Payment();
		
		$result = $_model->checkNounce('1c46538c712e9b5bf0fe43d692', $_model->nounces);
		
		$this->assertTrue($result);
	}
	
	public function testNounceExistFalse() {
		
		$_model = new Payment();
		
		$result = $_model->checkNounce('abc123', $_model->nounces);
		
		$this->assertFalse($result);
	}
	
	public function testChargeExistTrue() {
		
		$_model = new Payment();
		
		$result = $_model->chargeExist('123456', $_model->charges);
		
		$this->assertTrue($result);
		
	}
	
	public function testChargeExistFalse() {
		
		$_model = new Payment();
		
		$result = $_model->chargeExist('doe123P', $_model->charges);
		
		$this->assertFalse($result);
		
	}
	
	public function testChargeExistAfterPayment() {
		
		$_model = new Payment();
		
		$result = $_model->charge(5.00, 'abc123', array('product' => 'radio1'), $_model->charges);
		
		$this->assertTrue($_model->chargeExist($result, $_model->charges));
	}
	
	public function testRefundTrue() {
		
		$_model = new Payment();
		
		$refunded = $_model->refund('123456', $_model->charges);
		
		$this->assertTrue($refunded);
	}
	
	public function testRefundFalse() {
		
		$_model = new Payment();
		
		$refunded = $_model->refund('789012', $_model->charges);
		
		$this->assertFalse($refunded);
	}
	
}
	