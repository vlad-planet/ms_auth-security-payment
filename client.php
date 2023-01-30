<?php
include ('vendor/autoload.php');

use prodigyview\network\Curl;

$product_route = '127.0.0.1:8000/products';

echo "\nStarting RESTFUL Tests\n\n";

echo "Testing Notificatons\n\n";

/*/Invalid Nonce
$curl = new Curl($product_route.'/purchase');
$curl->send('post', array(
	'amount' => '20.00',
	'nounce' => 'abc123',
	'product_id' => 'cdf',
	'account_id' => 4,
));
echo $curl->getResponse();
echo "\n\n";
//*/


//Отправить запрос на оплату товара
$curl = new Curl($product_route.'/purchase');
$curl->send('post', array(
	'amount' => '20.00',
	'nounce' => '1c46538c712e9b5bf0fe43d692',
	'product_id' => 'cdf',
	'account_id' => 4,
));
echo $curl->getResponse();
echo "\n\n";
//*/


/*/InValid Refund
$curl = new Curl($product_route.'/refund');
$curl->send('post', array(
	'id' => '657567',
));
echo $curl->getResponse();
echo "\n\n";
//*/


//Отправить запрос на возврат товара в соотвествии с ID транзакцией
$curl = new Curl($product_route.'/refund');
$curl->send('post', array(
	'id' => '123456',
));
echo $curl->getResponse();
echo "\n\n";
//*/
