<?php
require_once 'vendor/autoload.php';

	use Bdu\UserData\Moodle2Edx;
// use PDO;
// print_r(PDO::getAvailableDrivers());

$userData = new Moodle2Edx;

echo $userData->convertAll(1);
