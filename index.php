<?php
require_once 'vendor/autoload.php';

	use Bdu\UserData\Moodle2Edx;

$userData = new Moodle2Edx;

//	echo $userData->createAuthUser(8);
	echo $userData->createAuthUserProfile(8);
//echo $userData->getUserData();
//	echo '<pre>';
//print_r($userData->getUserData());
//	echo '</pre>';