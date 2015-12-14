<?php
require_once 'vendor/autoload.php';

	use Bdu\UserData\Moodle2Edx;

$userData = new Moodle2Edx;


	echo $userData->saveUserData();
//echo $userData->getUserData();
//	echo '<pre>';
//print_r($userData->getUserData());
//	echo '</pre>';