<?php

class Payment {
	
	// Фиктивные данные о транзакции. В производстве это была бы база данных
	public $charges = array(
		'123456'=> array(
			'amount' => '10',
			'status' => 'charged',
			'meta' => array('product_id'=> 'x3c3', 'account_id'=>1)
		),
		'789012'=> array(
			'amount' => '20',
			'status' => 'refunded',
			'meta' => array('product_id'=> 'c8d0', 'account_id'=>2)
		),
	);

	// Одноразовая Унция от веб-клиента: В производстве запрашивается на стороне платежных услуг
	public $nounces = array(
		'1c46538c712e9b5bf0fe43d692',
		'004f617b494d004e29daaf'
	);

	/**
	 * Проверить, действителна ли унция
	 * 
	 * @param string $nounce
	 *
	 * @return boolean
	 */
	public function checkNounce(string $nounce) : bool {
	
		return in_array($nounce, $this->nounces);
	}

	/**
	 * Создает транзакцию и возвращает идентификатор нового платежа
	 * 
	 * @param float $amount
	 * @param string $nounce
	 * @param array $meta
	 * 
	 * @return string Возвращает идентификатор транзакции
	 */
	public function charge(float $amount, string $nounce, array $meta = array()) : string {
		
		$id = uniqid();
		
		$this->charges[$id] = array(
			'amount' => $amount,
			'status' => 'charged',
			'meta' => $meta
		);
		
		return $id;
	}

	/**
	 * Возврат приобретенного товара
	 * 
	 * @param string $id
	 * 
	 * @return boolean Возвращает значение true, если статус транзакции изменен, в противном случае значение false
	 */
	public function refund(string $id) : bool {
		
		if (isset($this->charges[$id]) && $this->charges[$id]['status'] == 'charged') {
			$this->charges[$id]['status'] = 'refunded';
			return true;
		}
		
		return false;
	}

	/**
	 * Проверяет, существует ли транзакция
	 * 
	 * @param string $id
	 * 
	 * @return boolean Возвращает значение true для существующей транзакции, в противном случае значение false
	 */
	public function chargeExist(string $id) : bool {

		return isset($this->charges[$id]);
	}

}