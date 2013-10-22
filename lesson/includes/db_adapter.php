<?
class DBAdapter {

	private static $instance;
	private $db;

	public function __construct() {
		global $wpdb;
		$this->db = $wpdb;
	}

	public static function getInstance() {
		if (!self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function getDBAdapter() {
		return $this->db;
	}

}