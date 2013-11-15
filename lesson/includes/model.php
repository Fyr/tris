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

	public function getLessonInfo($lessonID) {
		$mediaModel = new LessonModel('media');
		return array('Lesson' => $this->getItem($lessonID), 'Thumb' => $mediaModel->getMediaItemURL('image', 'LessonThumb', $lessonID, '&w=150&h=150'));
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

	public function getPath($type, $id) {
		return $this->getPagePath($type, $id).'/'.$id.'/';
    }

    public function getPagePath($type, $id) {
    	$page = floor($id/100);
		$path = UPLOAD_DIR.strtolower($type).'/'.$page;
		return $path;
    }

    public function getMediaURL($type, $id, $file, $params = '') {
    	if ($params) {
    		return '/thumb.php?id='.$id.'&file='.$file.$params;
    	}
    	$page = floor($id/100);
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

	public function getMediaItem($media_type = '', $object_type = '', $object_id = 0) {
		return $this->findOne(array('media_type' => $media_type, 'object_type' => $object_type, 'object_id' => $object_id));
	}

	public function getMediaItemURL($media_type = '', $object_type = '', $object_id = 0, $params = '') {
		$media = $this->getMediaItem($media_type, $object_type, $object_id);
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
		// Check if this image is already used for snippets
		if ($media['media_type'] == 'image') {
			if ($media['object_type'] == 'Lesson') {
				$snippetOptsModel = new LessonModel('snippet_options');
				$img_src = $this->getMediaURL('image', $mediaID, $media['file']);
				$snippet = $snippetOptsModel->findOne(array('option_key' => 'img_src', 'value' => $img_src));
				if ($snippet) {
					return array('status' => 'ERROR', 'errMsg' => 'Это изображение уже используется для просмотра уроков');
				}
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

	public function getThumbsList($conditions = array()) {
		$aRowset = $this->findAll(array_merge(array('object_type' => 'LessonThumb'), $conditions));
		$aThumbs = array();
		foreach($aRowset as $row) {
			$aThumbs[$row['object_id']] = $this->getMediaURL('image', $row['id'], $row['file'], '&w=90&h=90');
		}
		return $aThumbs;
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
				} else if (in_array($type, array('desktop', 'ipad', 'mobile'))) {
					$file = $this->getMediaURL('image', $row['id'], $row['file'], '&viewmode='.$type);
				}
				$aImages[$row['id']][$type] = $file;
			}
		}
		return $aImages;
	}

	private function getMediaFileInfo($filename, $aSize = array(), $mode = '') {
		$aFName = explode('.', $filename);
		$_ret = array('orig_fname' => $aFName[0], 'fname' => $aFName[0], 'orig_ext' => $aFName[1]);
		if ($aSize['w'] || $aSize['h']) {
			$_ret['fname'] = $aSize['w'].'x'.$aSize['h'];
		} else if ($mode) {
			$_ret['fname'] = $mode;
		}
		if (isset($aFName[2]) && $aFName[2]) {
			$_ret['ext'] = $aFName[2];
		} else {
			$_ret['ext'] = $aFName[1];
		}
		return $_ret;
	}

	public function genImage($id, $file, $aSize = array(), $mode = '', $lOutput = true) {
		@require_once(INCLUDE_DIR.'image.php');

		$path = $this->getPath('image', $id);
		$aFName = $this->getMediaFileInfo($file, $aSize, $mode);
		$fname = $path.$aFName['fname'].'.'.$aFName['ext'];
		if (file_exists($fname)) {
			// image already exists
			if ($lOutput) {
				header('Content-type: image/'.$aFName['ext']);
				echo file_get_contents($fname);
				exit;
			} else {
				return;
			}
		}

		$orig_fname = $path.$aFName['orig_fname'].'.'.$aFName['orig_ext'];
		if (!file_exists($orig_fname)) {
			// fix original file name by media ID if it was set incorrectly
			$media = $this->getItem($id);
			$orig_fname = $path.$media['file'];
		}

		$image = new Image();
		$image->load($orig_fname);
		$aPerc = array('ipad' => 80, 'mobile' => 50);
		if ($mode && isset($aPerc[$mode])) {
			// decrease image due to view mode
			$aSize['w'] = intval($image->getSizeX() * $aPerc[$mode] / 100);
		}
		if ($aSize['w'] || $aSize['h']) {
			$image->resize($aSize['w'], $aSize['h']); // 'f6f6f6'
		}

		if (!in_array($aFName['ext'], array('jpg', 'png'))) {
			$aFName['ext'] = 'gif';
		}

		$method = 'output'.ucfirst($aFName['ext']);
		$image->$method($fname);
		if ($lOutput) {
			$image->$method();
			exit;
		}
	}

	public function checkUserAccess($lessonID, $userID) {
		$apiModel = new LessonAPI();
		return $apiModel->checkUserAccess($lessonID, $userID);
	}

	public function getUsersList($conditions = array()) {
		$apiModel = new LessonAPI();
		return $apiModel->getUsersList($conditions);
	}

}