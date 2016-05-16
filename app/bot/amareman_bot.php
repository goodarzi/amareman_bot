<?php

require '../bootstrap.php';

$telegram = new telegram('amareman_bot.conf');
try {
$DBHandle = $telegram->DbConnect();
} catch (Exception $e) {
	echo $e;
}
echo "<pre>";
var_dump($telegram);
echo "</pre>";

try {
	$telegram->handle();
} catch (Exception $e) {
	echo $e;
}




?>