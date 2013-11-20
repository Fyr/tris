<?
class LessonAPI extends AdapterAPI {

	public function getUsersList($conditions = array()) {
		$sql = 'SELECT *, u.user_nicename AS username FROM '.$this->getWPTableName('users').' AS u '.
			$this->getSQLWhere($conditions).' ORDER BY u.ID';
		$aRowset = $this->db->get_results($sql, ARRAY_A);
		$aUsers = array();
		foreach($aRowset as $row) {
			$aUsers[$row['ID']] = $row;
		}
		return $aUsers;
	}

	public function checkUserAccess($lessonID, $userID) {
		$sql = 'SELECT * FROM '.$this->getWPTableName('comments').
			$this->getSQLWhere(array('user_id' => $userID, 'comment_type' => 'sensei_course_start'));
		$row = $this->db->get_row($sql, ARRAY_A);
		// return ($row && isset($row['comment_post_ID']) && $row['comment_post_ID']) ? $row['comment_post_ID'] : false;
		return true;
	}
}

