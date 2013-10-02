<?
require_once('path.php');

class LessonModel {

	const TABLE_PREFIX = 'ls_';
	const WP_TABLE_PREFIX = 'wp_';

	protected $db;
	protected $useTable;

	public function __construct($tableName = '') {
		$this->db = LessonDBAdapter::getInstance()->getDBAdapter();
		if ($tableName) {
			$this->setTableName($tableName);
		}
	}

	public function setTableName($tableName) {
		return $this->useTable = self::TABLE_PREFIX.$tableName;
	}

	public function getTableName($tableName = '') {
		if ($tableName) {
			return self::TABLE_PREFIX.$tableName;
		}
		return $this->useTable;
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
						$val = ' IN ('.implode(',', $val).')';
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

	public function deleteSnippetOptions($paraID) {
		$this->db->query(
			$this->db->prepare('DELETE FROM '.$this->getTableName('snippet_options').'
				WHERE snippet_id IN (SELECT id FROM '.$this->getTableName('snippets').' AS s WHERE s.paragraph_id = %d)', $paraID)
		);
	}

	public function getSnippetOptions($paraID) {
		return $this->db->get_results(
			$this->db->prepare('SELECT so.*, s.* FROM ls_snippets AS s
LEFT JOIN ls_snippet_options AS so ON so.snippet_id = s.id
WHERE s.paragraph_id = %d', $paraID),
			ARRAY_A
		);
	}

	public function getFirstParagraph($lessonID) {
		$sql = $this->db->prepare('SELECT p.* FROM '.$this->getTableName('chapters').' AS c
			LEFT JOIN '.$this->getTableName('paragraphs').' AS p ON p.chapter_id = c.id
			WHERE c.lesson_id = %d
			ORDER BY c.sort_order, p.sort_order', $lessonID
		);
		$_ret = $this->db->get_row($sql, ARRAY_A);
		return $_ret;
	}

	public function sortMove($id, $dir, $conditions) {
		$row = $this->getItem($id); // get sort order of record to move

		$sql = $this->db->prepare('SELECT id, sort_order FROM '.$this->getTableName().'
			WHERE sort_order '.(($dir == 'up') ? '<' : '>').'%d AND '.$conditions.'
			ORDER BY sort_order '.(($dir == 'up') ? 'DESC' : '').'
			LIMIT 1',
			$row['sort_order']
		);
		$ret = $this->db->get_row($sql, ARRAY_A); // get sort order of next record to first one

		// Exchange sort orders of 2 recs
		$this->save(array('id' => $id, 'sort_order' => $ret['sort_order']));
		$this->save(array('id' => $ret['id'], 'sort_order' => $row['sort_order']));
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
		$sql = 'SELECT p.id, p.title, p.chapter_id, c.lesson_id, c.title as chapter_title, p.content_cached, m.id AS audio_id, m.file AS audio_file FROM '.$this->getTableName('paragraphs').' AS p
			JOIN '.$this->getTableName('chapters').' AS c ON p.chapter_id = c.id
			LEFT JOIN '.$this->getTableName('media').' AS m ON p.id = m.object_id AND m.media_type = "audio"
			WHERE lesson_id = '.intval($lessonID).' AND (p.title LIKE "%'.$q.'%" OR p.content_cached LIKE "%'.$q.'%")';
		return $this->db->get_results($sql, ARRAY_A);
	}

	public function getPath($type, $id) {
		return $this->getPagePath($type, $id).'/'.$id.'/';
    }

    public function getPagePath($type, $id) {
    	$page = floor($id/100);
		$path = UPLOAD_DIR.strtolower($type).'/'.$page;
		return $path;
    }

    public function getMediaURL($type, $id, $file, $params = '') {
    	$page = floor($id/100);
    	if ($params) {
    		return '/thumb.php?id='.$id.'&file='.$file.$params;
    	}
    	return '/lesson/files/'.$type.'/'.$page.'/'.$id.'/'.rawurlencode($file);
    }

    public function getMediaFile($type, $id, $file, $params = '') {
    	return $this->getPath($type, $id).$file;
    }

    public function uploadMedia($inputName, $mediaType, $objectID) {
		$id = $this->save(array('media_type' => $mediaType, 'object_id' => $objectID, 'file' => ''));
		$path = $this->getPath($mediaType, $id);
		if (!file_exists($path)) {
			if (!file_exists($this->getPagePath($mediaType, $id))) {
				mkdir($this->getPagePath($mediaType, $id), 0755);
			}
			mkdir($path, 0755);
		}

    	if ($fileName = $this->uploadFile($inputName, $path, $mediaType)) {
			$this->save(array('id' => $id, 'file' => $fileName));
			return array('status' => 'OK', 'file' => $this->getMediaURL($mediaType, $id, $fileName));
		} else {
			$this->delete($id); // delete record for non-uploaded file
		}
		return array('status' => 'ERROR', 'errMsg' => 'Невозможно загрузить файл');
    }

	public function uploadFile($inputName, $uploadDir, $newFName = '', $newFExt = '') {
		$path = pathinfo($_FILES[$inputName]['name']);

		$newFExt = ($newFExt) ? $newFExt : '.'.$path['extension'];
		$newFName = ($newFName) ? $newFName : $path['filename'];

		$FName = $newFName.$newFExt;
		if (!@move_uploaded_file($_FILES[$inputName]['tmp_name'], $uploadDir.$FName)) {
		//if (!@copy($_FILES[$inputName]['tmp_name'], $uploadDir.$FName)) {
			// trigger_error('Could not upload image '.$_FILES[$inputName]['tmp_name'].' to '.$uploadDir.$FName);
			// exit;
			fdebug(array($_FILES[$inputName]['tmp_name'], $uploadDir.$FName), 'upload.log');
			return false;
		}
		chmod($uploadDir.$FName, 0644);
		return $FName;
	}

	public function getMediaItem($media_type = '', $object_type = '', $object_id = 0) {
		return $this->findOne(array('media_type' => $media_type, 'object_type' => $object_type, 'object_id' => $object_id));
	}

	public function getMediaItemList($media_type = '', $object_type = '', $object_id = 0) {
		$conditions = array();
		if ($media_type) {
			$conditions['media_type'] = $media_type;
		}
		if ($object_type) {
			$conditions['object_type'] = $object_type;
		}
		if ($object_id) {
			$conditions['object_id'] = $object_id;
		}
		$items = $this->findAll($conditions, 'media_type, id');
		$aMediaList = array();
		foreach($items as $item) {
			$aMediaList[$item['media_type']][] = $item;
		}
		return $aMediaList;
	}

	public function delMediaItem($mediaID) {
		$media = $this->getItem($mediaID);
		if (!$media) {
			return array('status' => 'ERROR', 'errMsg' => 'Неверный media ID');
		}

		$path = $this->getPath($media['media_type'], $mediaID);
		if ($media['media_type'] == 'image') {
			// Check if this image is already used for snippets
			$snippetOptsModel = new LessonModel('snippet_options');
			$img_src = $this->getMediaURL('image', $mediaID, $media['file']);
			$snippet = $snippetOptsModel->findOne(array('option_key' => 'img_src', 'value' => $img_src));
			if ($snippet) {
				return array('status' => 'ERROR', 'errMsg' => 'Это изображение уже используется для просмотра уроков');
			}

			$files = getPathContent($path);
			if (isset($files['files'])) {
				foreach($files['files'] as $file) {
					@unlink($path.$file);
				}
			}
		} else {
			// Any other object does not create other files
			$file = $this->getMediaFile($media['media_type'], $mediaID, $media['file']);
			@unlink($file);
		}
		rmdir($path);
		$this->db->query(
			$this->db->prepare('DELETE FROM '.$this->getTableName('media').' WHERE id = %d', $mediaID)
		);
		return array('status' => 'OK');
	}

	public function getPosts($paraID) {
		$sql = $this->db->prepare('SELECT p.*, u.user_nicename AS username FROM '.$this->getTableName('posts').' AS p
			JOIN '.$this->getWPTableName('users').' AS u ON u.ID = p.user_id
			WHERE para_id = %d
			ORDER BY p.created',
			$paraID
		);
		return $this->db->get_results($sql, ARRAY_A);
	}

	public function getNotes($userID) {
		$sql = $this->db->prepare('SELECT n.*, p.title AS para_title FROM '.$this->getTableName('notes').' AS n
			JOIN '.$this->getTableName('paragraphs').' AS p ON p.id = n.para_id
			WHERE user_id = %d
			ORDER BY n.created',
			$userID
		);
		return $this->db->get_results($sql, ARRAY_A);
	}

	public function getThumbsList($conditions = array()) {
		$sql = "SELECT p.*, mpm.* FROM ".$this->getWPTableName('postmeta')." AS pm
JOIN ".$this->getWPTableName('posts')." AS p ON p.ID = pm.post_id
JOIN ".$this->getWPTableName('postmeta')." AS mpm ON mpm.`post_id` = pm.meta_value AND mpm.meta_key = '_wp_attachment_metadata'";
		$where = array('p.post_type' => 'lesson', 'p.post_status' => 'publish', 'pm.meta_key' => '_thumbnail_id');
		$sql.= $this->getSQLWhere(array_merge($where, $conditions));
		$aRowset = $this->db->get_results($sql, ARRAY_A);
		$aThumbs = array();
		foreach($aRowset as $row) {
			$a = unserialize($row['meta_value']);
			list($dir, $subdir) = explode('/', $a['file']);
			$aThumbs[$row['ID']] = array('post_id' => $row['ID'], 'post_title' => $row['post_title'], 'thumb' => UPLOADS_DIR.$dir.'/'.$subdir.'/'.$a['sizes']['shop_thumbnail']['file']);
		}
		return $aThumbs;
	}

	public function getCoursesList($conditions = array()) {
		$sql = "SELECT p.ID AS lesson_id, p.post_title AS lesson_title, p2.ID as course_id, p2.post_title AS course_name,
			(SELECT id FROM ".$this->getTableName('chapters')." WHERE lesson_id = p.ID ORDER BY sort_order LIMIT 1) AS chapter_id
FROM ".$this->getWPTableName('posts')." AS p
JOIN ".$this->getWPTableName('postmeta')." AS pm ON pm.post_id = p.ID AND pm.meta_key = '_lesson_course'
JOIN ".$this->getWPTableName('posts')." AS p2 ON pm.meta_value = p2.ID";
		$where = array('p.post_type' => 'lesson');
		$sql.= $this->getSQLWhere(array_merge($where, $conditions));
		$sql.= ' ORDER BY p2.ID';
		$aRowset = $this->db->get_results($sql, ARRAY_A);
		$aCourses = array();
		$aLessonID = array();
		foreach($aRowset as $row) {
			$aCourses[$row['course_id']][] = $row;
			$aLessonID[] = $row['lesson_id'];
		}
		$aThumbs = $this->getThumbsList(array('pm.post_id' => $aLessonID));
		return array('Course' => $aCourses, 'Thumb' => $aThumbs);
	}

	public function getLessonInfo($lessonID) {
		$res = $this->getCoursesList(array('p.ID' => $lessonID));
		if (isset($res['Course']) && $res['Course']) {
			list($lesson) = array_values($res['Course']);
			return array('Lesson' => $lesson[0], 'Thumb' => $res['Thumb'][$lessonID]);
		}
		return array('Lesson' => array(), 'Thumb' => array());
	}

	public function getLastVisited($userID, $lessonID = 0) {
		$conditions = array('v.user_id' => $userID);
		if ($lessonID) {
			$conditions['c.lesson_id'] = $lessonID;
		}
		$sql = $this->db->prepare("SELECT * FROM (
SELECT v.para_id, p.title as para_title, p.chapter_id, c.title as chapter_title, c.lesson_id, v.last_visited
FROM ".$this->getTableName('visited')." AS v
JOIN ".$this->getTableName('paragraphs')." AS p ON p.id = v.para_id
JOIN ".$this->getTableName('chapters')." AS c ON c.id = p.chapter_id
".$this->getSQLWhere($conditions)."
ORDER BY v.last_visited DESC, c.sort_order, p.sort_order
) AS t GROUP BY lesson_id ORDER BY last_visited DESC
LIMIT ".LAST_VISITED, $userID);
		$aRowset = $this->db->get_results($sql, ARRAY_A);
		return $aRowset;
	}

	public function getUsersList($conditions = array()) {
		$sql = $this->db->prepare('SELECT * FROM '.$this->getWPTableName('users').' AS u '.
			$this->getSQLWhere($conditions).' ORDER BY u.ID'
		);
		$aRowset = $this->query($sql);
		$aUsers = array();
		foreach($aRowset as $row) {
			$aUsers[$row['ID']] = $row;
		}
		return $aUsers;
	}

	public function getImageList($conditions = array(), $image_type = 'shop_thumbnail') {
		if (!is_array($image_type)) {
			$image_type = array($image_type);
		}
		$mediaModel = new LessonModel('media');
		$conditions = array_merge(array('media_type' => 'image'), $conditions);
		$aRowset = $mediaModel->findAll($conditions, 'id DESC');
		$aImages = array();
		foreach($aRowset as $row) {
			foreach($image_type as $type) {
				$file = $this->getMediaURL('image', $row['id'], $row['file']);
				if ($type == 'shop_thumbnail') {
					$file = $this->getMediaURL('image', $row['id'], $row['file'], '&w=90&h=90');
					// $file = '/thumb.php?id='.$row['id'].'&file='.$row['file'].'&w=90&h=90';
				}
				$aImages[$row['id']][$type] = $file;
			}
		}
		return $aImages;
	}
}