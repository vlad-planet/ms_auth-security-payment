Launch 4 tabs in the Console!
Start the web server with: 					php -S 127.0.0.1:8000
Start the authentications server with: 		php servers/AuthenticationServer.php
Start the purchase server with:		 		php servers/PaymentServer.php
Run in the final tab client with:			php client.php 

Launch phpUnit Testing!
cd ./vendor/bin/ 
phpunit --verbose ../../tests/AuthenticationServerTest.php
phpunit --verbose ../../tests/PaymentSeverTest.php