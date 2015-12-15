<?php

namespace Bdu\UserData;

class Moodle2Edx
{
	const COURSE_ID_1 = "course-v1:Andela+AND101+2015_1";
	const COURSE_ID_2 = "course-v1:Andela+AND102+2015_2";
	const COURSE_ID_3 = "course-v1:Andela+AND103+2015_3";
	const COURSE_ID_4 = "course-v1:Andela+AND104+2015_4";
	const COURSE_ID_5 = "course-v1:Andela+AND105+2015_5";

	/**
	 * Handle saving data of table auth_user
	 *
	 * @return int
	 */
	public function createAuthUser() {
		$file = fopen('moodle_data/auth_user.txt', 'w');

		if (! is_null($file)) {
			$sql = "SELECT u.id id, u.username username, u.password password, u.email email, u.firstaccess date_joined, u.lastlogin last_login FROM mdl_user u";
			$userData = $this->retrieveData($sql);

			foreach ($userData as $d) {
				$record = $d['id'] . ">" . $d['username'] . ">>>" . $d['email'] . ">" . $d['password'] . ">0>1>0>" . $d['last_login'] . ">" . $d['date_joined'] . ">\n";
				fwrite($file, $record);
			}

			fclose($file);

			return 1;
		}

		return 0;
	}

	/**
	 * Handle saving data of table auth_userprofile
	 *
	 * @return int
	 */
	public function createAuthUserProfile() {
		$file = fopen('moodle_data/auth_userprofile.txt', 'w');

		if (! is_null($file)) {
			$sql = "SELECT u.id user_id, u.firstname firstname, u.lastname lastname, u.city city, u.country country, u.lang language, u.description bio FROM mdl_user u;";
			$userData = $this->retrieveData($sql);

			foreach ($userData as $d) {
				$record = $d['user_id'] . ">" . $d['user_id'] . ">" . $d['firstname'] . " " . $d['lastname'] . ">" . $d['language'] . ">>>>>>>>>1>" . $d['country'] . ">" . $d['city'] . ">" . $d['bio'] . ">" . "NULL" . ">\n";
				fwrite($file, $record);
			}

			fclose($file);

			return 1;
		}

		return 0;
	}

	/**
	 * Handle saving data of table student_courseenrollment
	 *
	 * @param $startID
	 * @return int
	 */
	public function createStudentCourseEnrollment($startID) {
		$file = fopen('moodle_data/student_courseenrollment.txt', 'w');

		if (! is_null($file)) {
			$sql = "SELECT u.id user_id, c.id course_id, c.timecreated created, c.visible is_active FROM mdl_user u
					INNER JOIN mdl_role_assignments ra ON ra.userid=u.id
					INNER JOIN mdl_context ct ON ct.id = ra.contextid
					INNER JOIN mdl_course c ON c.id = ct.instanceid
					INNER JOIN mdl_role r ON r.id = ra.roleid
					WHERE r.id = 5";
			$userData = $this->retrieveData($sql);
			$idIncrement = $startID;

			foreach ($userData as $d) {
				$record = $idIncrement . ">" . $d['user_id'] . ">" . $d['course_id'] . ">" . $d['created'] . ">" . $d['is_active'] . ">audit>\n";
				fwrite($file, $record);
				$idIncrement++;
			}

			fclose($file);

			return 1;
		}

		return 0;
	}

	/**
	 * Handle calling of all methods synchronously
	 *
	 * @param $startID
	 * @return int
	 */
	public function convertAll($startID) {
		$proceedStatus = 0;

		if ($this->createAuthUser()) {
			$proceedStatus = $this->createAuthUserProfile();
		}

		if ($proceedStatus) {
			$proceedStatus += $this->createStudentCourseEnrollment($startID);
		}

		return $proceedStatus;
	}

	/**
	 * Handle retrieval of users' data
	 *
	 * @param $sql SQL query statement
	 * @return array|string All users' data
	 */
	private function retrieveData($sql)
	{
		$dbConn = new DbConn;

		try {
			$query = $dbConn->prepare($sql);
			$query->execute();
		} catch (\PDOException $e) {
			return $e->getMessage();
		} finally {
			$dbConn = null;
		}

		$data = $query->fetchAll(DbConn::FETCH_ASSOC);

		if (isset($data[0]['date_joined'])) {
			foreach($data as &$d) {
				date_default_timezone_set('Africa/Lagos');
				$d['date_joined'] = date("Y-m-d H:i:s", $d['date_joined']);
				$d['last_login'] = date("Y-m-d H:i:s", $d['last_login']);
			}
		}

		if (isset($data[0]['created'])) {
			foreach($data as &$d) {
				date_default_timezone_set('Africa/Lagos');
				$d['created'] = date("Y-m-d H:i:s", $d['created']);
			}
		}

		if (isset($data[0]['course_id'])) {
			foreach($data as &$d) {
				$d['course_id'] = $this->formatCourseId($d['course_id']);
			}
		}

		return $data;
	}

	private function formatCourseId($moodleCourseId) {
		$edxFormat = "";

		switch ($moodleCourseId) {
			case(1):
				$edxFormat = $this::COURSE_ID_1;
				break;
			case(2):
				$edxFormat = $this::COURSE_ID_2;
				break;
			case(3):
				$edxFormat = $this::COURSE_ID_3;
				break;
			case(4):
				$edxFormat = $this::COURSE_ID_4;
				break;
			case(5):
				$edxFormat = $this::COURSE_ID_5;
				break;
			default:
				break;

		}

		return $edxFormat;
	}
}