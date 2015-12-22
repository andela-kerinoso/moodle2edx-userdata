<?php

namespace Bdu\UserData;

class moodle2edx
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
    public function createAuthUser()
    {
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
    public function createAuthUserProfile()
    {
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
    public function createStudentCourseEnrollment($startID)
    {
        $file = fopen('moodle_data/student_courseenrollment.txt', 'w');

        if (! is_null($file)) {
            $sql = "SELECT u.id user_id, c.id course_id FROM mdl_user u
					INNER JOIN mdl_role_assignments ra ON ra.userid=u.id
					INNER JOIN mdl_context ct ON ct.id = ra.contextid
					INNER JOIN mdl_course c ON c.id = ct.instanceid
					INNER JOIN mdl_role r ON r.id = ra.roleid
					WHERE r.id = 5";
	        $userData = $this->retrieveData($sql);
	        $idIncrement = $startID;

	        foreach ($userData as $d) {
		        $record = $idIncrement . ">" . $d['user_id'] . ">" . $d['course_id'] . ">NULL>1>honor>\n";
		        fwrite($file, $record);
		        $idIncrement++;
	        }

	        fclose($file);

	        return 1;
        }

	    return 0;
    }

	/**
	 * Handle saving data of table courseware_studentmodule
	 *
	 * @param $startID
	 * @return int
	 */
    public function createCoursewareStudentModule($startID)
    {
        $file = fopen('moodle_data/courseware_studentmodule', 'w');

        if (! is_null($file) ) {
            $sql = "SELECT u.id student_id, c.id course_id, gi.itemname assessment,
					gi.itemtype module_type, gi.grademin grade_min, gi.grademax max_grade,
					gg.finalgrade grade, gg.timemodified modified_date FROM mdl_user u
					INNER JOIN mdl_grade_grades gg ON gg.userid = u.id
					INNER JOIN mdl_grade_items gi ON gi.id = gg.itemid
					INNER JOIN mdl_course c ON c.id = gi.courseid
					WHERE gi.itemtype = 'mod' AND gi.itemname not in ('')";
            $userData = $this->retrieveData($sql);
            $idIncrement = $startID;

            foreach ($userData as $d) {
                $record = $idIncrement . ">problem>>" . $d['student_id'] . ">NULL>" . $d['grade'] . ">" . $d['modified_date'] . ">" . $d['modified_date'] . ">" . $d['max_grade'] . ">na>" . $d['course_id'] . ">\n";
                fwrite($file, $record);
                $idIncrement++;
            }

            fclose($file);

            return 1;
        }

        return 0;
    }

	/**
	 * Handle saving data of table certificates_generatedcertificate
	 *
	 * @param $startID
	 * @return int
	 */
    public function createCertificatesGeneratedCertificate($startID)
    {
	    $file = fopen('moodle_data/certificates_generatedcertificate.txt', 'w');

	    if (! is_null($file)) {
		    $sql = "SELECT u.id user_id, gi.gradepass gradepass, gg.timemodified modified_date,
					gg.finalgrade grade, c.id course_id, u.username name FROM mdl_user u
					INNER JOIN mdl_grade_grades gg ON gg.userid = u.id
					INNER JOIN mdl_grade_items gi ON gi.id = gg.itemid
					INNER JOIN mdl_course c ON c.id = gi.courseid
					WHERE gi.itemtype = 'course'";
		    $userData = $this->retrieveData($sql);
		    $idIncrement = $startID;

		    foreach ($userData as $d) {
			    $record = $idIncrement . ">" . $d['user_id'] . ">>" . substr($d['grade']/100, 0, 5) . ">" . $d['course_id'] . ">>0>" . (($d['grade'] >= $d['gradepass']) ? "generating" : "notpassing") . ">>>" . $d['name'] . ">" . $d['modified_date'] . ">" . $d['modified_date'] . ">>honor>\n";
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
    public function convertAll($startID)
    {
        $proceedStatus = 0;

        if ($this->createAuthUser()) {
            $proceedStatus = $this->createAuthUserProfile();
        }

        if ($proceedStatus) {
            $proceedStatus += $this->createStudentCourseEnrollment($startID);
        }

	    if ($proceedStatus == 2) {
		    $proceedStatus += $this->createCoursewareStudentModule($startID);
	    }

	    if ($proceedStatus == 3) {
		    $proceedStatus += $this->createCertificatesGeneratedCertificate($startID);
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
            foreach ($data as &$d) {
				$this->setTimeZone();
                $d['date_joined'] = date("Y-m-d H:i:s", $d['date_joined']);
                $d['last_login'] = date("Y-m-d H:i:s", $d['last_login']);
            }
        }

        if (isset($data[0]['course_id'])) {
            foreach ($data as &$d) {
                $d['course_id'] = $this->formatCourseId($d['course_id']);
            }
        }

	    if (isset($data[0]['modified_date'])) {
		    foreach ($data as &$d) {
			    $this->setTimeZone();
			    $d['modified_date'] = date("Y-m-d H:i:s", $d['modified_date']);
		    }
	    }

        return $data;
    }

	/**
	 * Handle conversion of course_id on Moodle to that on Open edX
	 *
	 * @param $moodleCourseId
	 * @return string
	 */
    private function formatCourseId($moodleCourseId)
    {
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

	private function setTimeZone() {
		DbConn::loadDotenv();
		date_default_timezone_set(getenv('TIME_ZONE'));
	}
}