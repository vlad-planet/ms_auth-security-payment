microservices
 
## Организация доступа сервисов" по RestApi, с использованием авторизации и получения токена, + прав на выполнения задачи, <br> а также проверка токена доступа к платежному шлюзу. + Безопасность данных.

Client RestFullApi-> Socket:8600 Auth Server-> Response to Service-> Socket:8601 Payment Server-> Security token Auth.

Требования к Запуску Теста:<br>
- Composer<br>
- ^PHP7<br>
- PHP Sockets Extensions Installed<br>

1. Перейдите в корневой каталог.
2. Запустите composer install для установки необходимых пакетов
3. Откройте 4 вкладки в консоле
4. Запустите веб-сервер с помощью: php -S 127.0.0.1:8000
6. Запустите сервер аутентификации с помощью: php servers/AuthenticationServer.php
7. Запустите сервер покупки с помощью: php servers/PaymentServer.php
8. Запустите клиент с помощью: client.php

### Запустите тестирование PHPUnit!
cd ./vendor/bin/ <br>
phpunit --verbose ../../tests/AuthenticationServerTest.php <br>
phpunit --verbose ../../tests/PaymentSeverTest.php

____________________________________________________________________________________
Отправить push-уведомление от клиента нашему сервису
```php
$product_route = '127.0.0.1:8000/products';
$curl = new Curl($product_route.'/purchase');
$curl->send('post', array(
	'amount' => '20.00',
	'nounce' => '1c46538c712e9b5bf0fe43d692',
	'product_id' => 'cdf',
	'account_id' => 4,
));
echo $curl->getResponse();
```

Перенаправление полученных данных в соотвествии с запросом
```php
Router::post('/products/purchase', array('callback'=>function(Request $request) {
	$action = 'charge';
	PaymentService::sendRequest($action, $request);
}));
```
