<?php

// Include the composer autoloader
if(!file_exists(__DIR__ .'/../vendor/autoload.php')) {
	echo "The 'vendor' folder is missing. You must run 'composer update' to resolve application dependencies.\nPlease see the README for more information.\n";
	exit(1);
}
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/common/connection.php';
require __DIR__ . '/common/telegram.php';



?>