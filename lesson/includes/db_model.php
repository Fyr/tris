<?
class DBModel {

	const TABLE_PREFIX = 'ls_';
	const WP_TABLE_PREFIX = 'wp_';

	protected $db;
	protected $useTable;

	public function __construct($tableName = '') {
		$this->db = DBAdapter::getInstance()->getDBAdapter();
		if ($tableName) {
			$this->setTableName($tableName);
		}
	}

	public function setTableName($tableName) {
		return $this->useTable = $tableName;
	}

	public function getTableName($tableName = '') {
		if ($tableName) {
			return self::TABLE_PREFIX.$tableName;
		}
		return self::TABLE_PREFIX.$this->useTable;
	}

	public function getWPTableName($tableName) {
		return self::WP_TABLE_PREFIX.$tableName;
	}

	public function save($data) {
		$id = false;
		if (isset($data['id']) && intval($data['id'])) {
			$this->db->update($this->getTableName(), $data, array('id' => $data['id']));
			$id = $data['id'];
		} else {
			$this->db->insert($this->getTableName(), $data);
			$id = $this->db->insert_id;
		}
		return $id;
	}

	public function getItem($id) {
		return $this->findOne(array('id' => $id));
	}

	public function findOne($conditions = array(), $order = '') {
		if (defined('DEBUG_SQL')) {
			fdebug($this->getSQL($conditions, $order)."\r\n\r\n", 'sql.log');
		}
		$_ret = $this->db->get_row($this->getSQL($conditions, $order), ARRAY_A);
		return ($_ret) ? $_ret : array();
	}

	protected function getSQLWhere($conditions = array()) {
		$sql = '';
		if ($conditions) {
			$sql.= ' WHERE ';
			$where = array();
			foreach ($conditions as $key => $val) {

				if (is_numeric($key)) {
					$where[] = $val;
				} else {

					if (is_array($val)) {
						$aVal = array();
						foreach($val as $_val) {
							$aVal[] = (is_numeric($val)) ? intval($_val) : '"'.$_val.'"';
						}
						$val = ' IN ('.implode(',', $aVal).')';
					} else {
						$val = ' = '.((is_numeric($val)) ? intval($val) : '"'.$val.'"');
					}
					$where[] = $key.$val;
				}
			}
			$sql.= implode(' AND ', $where);
		}
		return $sql;
	}

	protected function getSQL($conditions = array(), $order = '') {
		$sql = 'SELECT * FROM '.$this->getTableName();
		$sql.= $this->getSQLWhere($conditions);
		if ($order) {
			if (is_array($order)) {
				$sql.= ' ORDER BY '.implode(',', $order);
			} else {
				$sql.= ' ORDER BY '.$order;
			}
		}
		return $sql;
	}

	public function findAll($conditions = array(), $order = '') {
		return $this->query($this->getSQL($conditions, $order));
	}

	public function delete($id) {
		$this->db->query(
			$this->db->prepare('DELETE FROM '.$this->getTableName().' WHERE id = %d', $id)
		);
	}

	public function deleteAll($conditions) {
		$this->db->delete($this->useTable, $conditions);
	}

	public function query($sql) {
		if (defined('DEBUG_SQL')) {
			fdebug($sql."\r\n\r\n", 'sql.log');
		}
		return $this->db->get_results($sql, ARRAY_A);
	}

}