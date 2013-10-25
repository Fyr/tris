<?
require_once('path.php');
require_once('db_model.php');

class LessonModel extends DBModel {

	public static function getModel($tableName) {
		$className = '';
		foreach(explode('_', $tableName) as $part) {
			$className.= ucfirst($part);
		}

		$module = $tableName;
		if (substr($className, -1, 1) == 's') {
			$className = substr($className, 0, strlen($className) - 1);
			$module = substr($tableName, 0, strlen($tableName) - 1);
		}

		$module = MODEL_DIR.strtolower($module).'.php';
		if (file_exists($module)) {
			require_once($module);
			$className = $className.'Model';
			return new $className($tableName);
		}
		return new LessonModel($tableName);
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

    public function uploadMedia($inputName, $mediaType, $objectType = 'Lesson', $objectID) {
		$id = $this->save(array('media_type' => $mediaType, 'object_type' =>$objectType, 'object_id' => $objectID, 'file' => ''));
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

	public function getMediaItem($media_type = '', $object_type = '', $object_id = 0, $params = '') {
		$media = $this->findOne(array('media_type' => $media_type, 'object_type' => $object_type, 'object_id' => $object_id));
		return ($media) ? $this->getMediaURL($media_type, $media['id'], $media['file'], $params) : '';
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
		/*
		$sql = $this->db->prepare('SELECT p.*, u.user_nicename AS username FROM '.$this->getTableName('posts').' AS p
			JOIN '.$this->getWPTableName('users').' AS u ON u.ID = p.user_id
			WHERE para_id = %d
			ORDER BY p.created',
			$paraID
		);
		*/
		$aPosts = $this->findAll(array('para_id' => $paraID), 'created');
		$aID = array();
		foreach($aPosts as $post) {
			$aID[] = $post['user_id'];
		}
		$aUsers = $this->getUsersList(array('u.ID' => $aID));
		return array('Post' => $aPosts, 'User' => $aUsers);
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
		$aRowset = $this->findAll(array_merge(array('object_type' => 'LessonThumb'), $conditions));
		$aThumbs = array();
		foreach($aRowset as $row) {
			$aThumbs[$row['object_id']] = $this->getMediaURL('image', $row['id'], $row['file'], '&w=90&h=90');
		}
		return $aThumbs;
	}

	public function getLessonInfo($lessonID) {
		$mediaModel = new LessonModel('media');
		return array('Lesson' => $this->getItem($lessonID), 'Thumb' => $mediaModel->getMediaItem('image', 'LessonThumb', $lessonID, '&w=150&h=150'));
	}

	public function getUsersList($conditions = array()) {
		$apiModel = new LessonAPI();
		return $apiModel->getUsersList($conditions);
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

	public function checkUserAccess($lessonID, $userID) {
		$apiModel = new LessonAPI();
		return $apiModel->checkUserAccess($lessonID, $userID);
	}
}