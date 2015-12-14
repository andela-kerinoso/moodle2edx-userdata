<?php

namespace Bdu\UserData;

class Moodle2Edx
{
    protected $table = "mdl_user";
	protected $dbConn;

    public function __construct()
    {
        $this->dbConn = new DbConn();
    }

    public function getUserData()
    {
	    try {
		    $sql = "SELECT u.id id, u.username username, u.password password, u.email email, u.firstaccess date_joined, u.lastlogin last_login FROM {$this->table} u";
		    $query = $this->dbConn->prepare($sql);
		    $query->execute();
	    } catch (\PDOException $e) {
		    return $e->getMessage();
	    } finally {
		    $this->dbConn = null;
	    }

	    $data = $query->fetchAll(DbConn::FETCH_ASSOC);

        foreach($data as &$d)
        {
	        date_default_timezone_set('Africa/Lagos');
            $d['date_joined'] = date("Y-m-d H:i:s", $d['date_joined']);
            $d['last_login'] = date("Y-m-d H:i:s", $d['last_login']);
        }

        return $data;
    }

	public function saveUserData() {
		$file = fopen('converted-data/auth_user.txt', 'w');

		if (! is_null($file)) {

			$userData = $this->getUserData();

			foreach ($userData as $data) {
				$length = sizeof($data);
				$counter = 1;

				foreach ($data as $test) {
					fwrite($file, $test . ($counter < $length ? '>' : ''));
					$counter++;
				}

				fwrite($file, "\n");
			}

			fclose($file);

			return 'success';
		}
	}

}