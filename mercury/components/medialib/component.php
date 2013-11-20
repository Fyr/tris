<?
class MediaLibComponent extends SnippetComponent {
	protected $mediaModel;

	public function __construct($snippetInfo, $paraID) {
		parent::__construct($snippetInfo, $paraID);
		$this->mediaModel = LessonModel::getModel('media');
	}

	private function getImageList() {
		return $this->mediaModel->getImageList(array('object_type' => 'Lesson'), array('shop_thumbnail', 'large'));
	}

	public function selectThumbOptions($lMultiSelect = false) {
		$this->set('images', $this->getImageList());
		$this->set('lMultiSelect', $lMultiSelect);
		return $this->render('select_thumb_options');
	}

	public function getThumbsContent() {
		$this->set('images', $this->getImageList());
		return $this->render('view_thumbs');
	}

	public function upload($data) {
		$input = 'image';
		$mediaModel = new LessonModel('media');
		if (isset($_FILES[$input]['type']) && strpos($_FILES[$input]['type'], 'image') !== false) {
			$response = $this->mediaModel->uploadMedia($input, 'image', 'Lesson', $data['lesson_id']);
			if ($response['status'] == 'OK') {
				$response['thumbsHTML'] = $this->getThumbsContent();
			}
			return $response;
		}
		return array('status' => 'ERROR', 'errMsg' => 'Неверный формат изображения');
	}

	public function deleteImage($data) {
		foreach(explode(',', $data['img_ids']) as $img_id) {
		$response = $this->mediaModel->delMediaItem($img_id);
			if ($response['status'] == 'ERROR') {
				return array('status' => 'ERROR', 'errMsg' => 'Ошибка удаления изображения');
			}
		}
		$response['thumbsHTML'] = $this->getThumbsContent();
		return $response;
	}

	public function saveImageSettings($data) {
		$id = $data['img_ids'];
		$aModes = array('desktop', 'ipad', 'mobile');
		foreach($aModes as $mode) {
			$media = $this->mediaModel->getItem($id);
			$aFName = $this->mediaModel->getMediaFileInfo($media['file'], null, $mode);
			@unlink($this->mediaModel->getMediaFile('image', $id, $aFName['fname'].'.'.$aFName['ext']));

			$size = array('w' => $data['w'], 'h' => $data['h']);
			$this->mediaModel->genImage($id, $media['file'], $size, $mode, false);

			$extras = unserialize($media['extras']);
			$extras['size']['desktop'] = array('w' => $data['w'], 'h' => $data['h']);
			$this->mediaModel->save(array('id' => $id, 'extras' => serialize($extras)));
		}
		return array('status' => 'OK', 'thumbsHTML' => $this->getThumbsContent());
	}
}
