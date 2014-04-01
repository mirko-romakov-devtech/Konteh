<?php
class DatabaseHandler {

	/**
	 * 
	 * @var PDO
	 */
	private $pdoInstance;
	private static $instance;
	private static $connectionString = 'mysql:dbname=%s;host=%s';
	
	private function __construct() {
	}
	
	public static function WithDefaultConfig() {
		if (!isset(self::$instance)) {
			self::$instance = new DatabaseHandler();
			self::$instance->pdoInstance = new PDO(sprintf(self::$connectionString, DB_NAME, DB_HOST), DB_USER, DB_PASS);
		}
		return self::$instance;
	}
	
	public static function WithCustomConfig($dbHost, $dbDatabaseName, $dbUsername, $dbPassword) {
		if (!isset(self::$instance)) {
			self::$instance = new DatabaseHandler();
		}
		self::$instance->pdoInstance = new PDO(sprintf(self::$connectionString, $dbDatabaseName, $dbHost), $dbUsername, $dbPassword);
		return self::$instance;
	}
	
	public function insertActivation(LinkModel $model, $encrypted) {
		$statement = $this->pdoInstance->prepare("INSERT INTO konteh.activation (candidate_id, action, encrypted_value, used) VALUES (?, ?, ?, ?)");
		$statement->execute(array($model->GUID, $model->Action, $encrypted, $model->Used));
	}
}