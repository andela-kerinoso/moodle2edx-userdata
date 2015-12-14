<?php

namespace Bdu\UserData;

use PDO;
use PDOException;

class Moodle2Edx
{
    public $table = "mdl_user";

    public function createConnection()
    {
        try {
            return new PDO("mysql:port=8889;dbname=moodle30;host=127.0.0.1", "root", "root");
        } catch (PDOException $e) {
            return "Connection to database failed: " . $e->getMessage();
        }
    }

    public function getUserData()
    {
        $db = $this->createConnection();
        $data = $db->query("SELECT u.id id, u.username username, u.password password, u.email email, u.firstaccess date_joined, u.lastlogin last_login FROM {$this->table} u")->fetchAll();

        foreach($data as &$d)
        {
            $d['date_joined'] = date("Y-m-d H:i:s", $d['date_joined']);
            $d['last_login'] = date("Y-m-d H:i:s", $d['last_login']);
        }
        echo print_r(json_encode($data));
    }

}