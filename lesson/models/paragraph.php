<?
class ParagraphModel extends LessonModel {

	public function getFirstParagraph($lessonID) {
		$sql = $this->db->prepare('SELECT p.* FROM '.$this->getTableName('chapters').' AS c
			LEFT JOIN '.$this->getTableName('paragraphs').' AS p ON p.chapter_id = c.id
			WHERE c.lesson_id = %d
			ORDER BY c.sort_order, p.sort_order LIMIT 1', $lessonID
		);
		$res = $this->query($sql);
		return ($res) ? $res[0] : array();
	}

	public function getParagraphList($filters = array()) {
		$conditions = array();
		if (isset($filters['lesson_id'])) {
			$conditions['c.lesson_id'] = $filters['lesson_id'];
		}
		if (isset($filters['favorite'])) {
			$conditions['p.favorite'] = ($filters['favorite']) ? 1 : 0;
		}
		if (isset($filters['key'])) {
			// $conditions['p.favorite'] = ($filters['favorite']) ? 1 : 0;
			// TODO: implement search by key
		}
		$sql = 'SELECT p.id, p.title, p.chapter_id, c.lesson_id, c.title as chapter_title, m.id AS audio_id, m.file AS audio_file, p.subheaders
			FROM '.$this->getTableName('paragraphs').' AS p
			JOIN '.$this->getTableName('chapters').' AS c ON p.chapter_id = c.id
			LEFT JOIN '.$this->getTableName('media').' AS m ON p.id = m.object_id AND m.media_type = "audio" '.
			$this->getSQLWhere($conditions).
			' ORDER BY c.sort_order, p.sort_order';
		return $this->db->get_results($sql, ARRAY_A);
	}

	public function search($q, $lessonID) {
		$sql = 'SELECT p.id, p.title, p.chapter_id, c.lesson_id, c.title as chapter_title, p.content_cached, m.id AS audio_id, m.file AS audio_file
			FROM '.$this->getTableName('paragraphs').' AS p
			JOIN '.$this->getTableName('chapters').' AS c ON p.chapter_id = c.id
			LEFT JOIN '.$this->getTableName('media').' AS m ON p.id = m.object_id AND m.media_type = "audio"
			WHERE lesson_id = '.intval($lessonID).' AND (p.title LIKE "%'.$q.'%" OR p.content_cached LIKE "%'.$q.'%")';
		return $this->db->get_results($sql, ARRAY_A);
	}

	public function getLastVisited($userID, $lessonID = 0, $lEditMode = false) {
		$conditions = array('v.user_id' => $userID);
		if ($lessonID) {
			$conditions['c.lesson_id'] = $lessonID;
		}
		$sql = "SELECT * FROM ".$this->getTableName('lessons')." AS l
LEFT JOIN (SELECT * FROM (
SELECT v.para_id, p.title as para_title, p.chapter_id, c.title as chapter_title, c.lesson_id, v.last_visited
FROM ".$this->getTableName('visited')." AS v
JOIN ".$this->getTableName('paragraphs')." AS p ON p.id = v.para_id
JOIN ".$this->getTableName('chapters')." AS c ON c.id = p.chapter_id
".$this->getSQLWhere($conditions)."
ORDER BY v.last_visited DESC, c.sort_order, p.sort_order
) AS t GROUP BY lesson_id) AS t2 ON t2.lesson_id = l.id";
		if ($lEditMode) {
			$sql.= " ORDER BY id";
		} else {
			$sql.= " ORDER BY last_visited DESC";
		}
		$aRowset = $this->query($sql, ARRAY_A);
		return $aRowset;
	}
}