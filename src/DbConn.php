<?php
namespace Bdu\UserData;

use \PDO;

class DbConn extends PDO
{
	/**
	 * Handle DB Connection
	 */
    public function __construct()
    {
        $this->loadDotenv();

		$engine = getenv('DB_ENGINE');
		$host = getenv('DB_HOST');
		$dbname = getenv('DB_DATABASE');
		$port= getenv('DB_PORT');
		$user = getenv('DB_USERNAME');
		$password = getenv('DB_PASSWORD');

		try {
			if ($engine == 'mysql') {
				parent::__construct($engine . ':host=' . $host . ';port=' . $port . ';dbname=' . $dbname . ';charset=utf8mb4', $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
								PDO::ATTR_PERSISTENT => false]);
			}
        } catch (\PDOException $e) {
            return 'Error in connection';
        }

    }

    /**
     * Load Dotenv to grant getenv() access to environment variables in .env file
     */
    protected function loadDotenv()
    {
		$dotenv = new \Dotenv\Dotenv($_SERVER['DOCUMENT_ROOT']);
		$dotenv->load();
    }
}
