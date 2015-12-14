<?php

namespace Bdu\UserData;

class Moodle2Edx
{
	private $dbConn;

    public function __construct()
    {
        $this->dbConn = new DbConn();
    }

	/**
	 * Handle retrieval of users' data
	 *
	 * @param $sql SQL query statement
	 * @return array|string All users' data
	 */
    protected function retrieveData($sql)
    {
	    try {
		    $query = $this->dbConn->prepare($sql);
		    $query->execute();
	    } catch (\PDOException $e) {
		    return $e->getMessage();
	    } finally {
		    $this->dbConn = null;
	    }

	    $data = $query->fetchAll(DbConn::FETCH_ASSOC);

	    if (isset($data[0]['date_joined'])) {
		    foreach($data as &$d) {
			    date_default_timezone_set('Africa/Lagos');
			    $d['date_joined'] = date("Y-m-d H:i:s", $d['date_joined']);
			    $d['last_login'] = date("Y-m-d H:i:s", $d['last_login']);
		    }
	    }

        return $data;
    }

	/**
	 * @param $startID
	 * @return int
	 */
	public function createAuthUser($startID) {
		$file = fopen('moodle_data/auth_user.txt', 'w');

		if (! is_null($file)) {
			$sql = "SELECT u.username username, u.password password, u.email email, u.firstaccess date_joined, u.lastlogin last_login FROM mdl_user u";
			$userData = $this->retrieveData($sql);
			$counter = $startID;

			foreach ($userData as $d) {
				$record = $counter . ">" . $d['username'] . ">>>" . $d['email'] . ">" . $d['password'] . ">0>1>0>" . $d['last_login'] . ">" . $d['date_joined'] . ">\n";
				fwrite($file, $record);
				$counter++;
			}

			fclose($file);

			return 1;
		}

		return 0;
	}

	/**
	 * @param $startID
	 * @return int
	 */
	public function createAuthUserProfile($startID) {
		$file = fopen('moodle_data/auth_userprofile.txt', 'w');

		if (! is_null($file)) {
			$sql = "SELECT u.firstname firstname, u.lastname lastname, u.city city, u.country country, u.lang language, u.description bio FROM mdl_user u;";
			$userData = $this->retrieveData($sql);
			$idIncrement = $startID;

			foreach ($userData as $d) {
				$record = $idIncrement . ">" . $idIncrement . ">" . $d['firstname'] . " " . $d['lastname'] . ">" . $d['language'] . ">>>>>>>>>1>" . $d['country'] . ">" . $d['city'] . ">" . $d['bio'] . ">" . "NULL" . ">\n";
				fwrite($file, $record);
				$idIncrement++;
			}

			fclose($file);

			return 1;
		}

		return 0;
	}

}