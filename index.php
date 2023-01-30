<?php
include ('vendor/autoload.php');

use prodigyview\network\Router;
use prodigyview\network\Request;

include('services/PaymentService.php');

Router::init();

//Перенаправить полученный запрос от клиента на оплату товара
Router::post('/products/purchase', array('callback'=>function(Request $request) {
	$action = 'charge';
	PaymentService::sendRequest($action, $request);
}));

//Перенаправить полученный запрос от клиента на возврат товара
Router::post('/products/refund', array('callback'=>function(Request $request) {
	$action = 'refund';
	PaymentService::sendRequest($action, $request);
}));

Router::setRoute();