<?
class LessonAPI extends AdapterAPI {

	public function getUsersList($conditions = array()) {
		$sql = $this->db->prepare('SELECT *, u.user_nicename AS username FROM '.$this->getWPTableName('users').' AS u '.
			$this->getSQLWhere($conditions).' ORDER BY u.ID'
		);
		$aRowset = $this->db->get_results($sql, ARRAY_A);
		$aUsers = array();
		foreach($aRowset as $row) {
			$aUsers[$row['ID']] = $row;
		}
		return $aUsers;
	}
}

