<?php

require 'vendor/autoload.php';

use Bdu\UserData\Moodle2Edx;

$db = new Moodle2Edx();

$db->getUserData();