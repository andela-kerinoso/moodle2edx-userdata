<?php
require_once 'vendor/autoload.php';

	use Bdu\UserData\Moodle2Edx;

$userData = new Moodle2Edx;

echo $userData->convertAll(1);