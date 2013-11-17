<?
class MediaLibComponent extends SnippetComponent {

	public function selectThumbOptions($lMultiSelect = false) {
		$this->set('images', $this->lessonModel->getImageList(array('object_type' => 'Lesson'), array('shop_thumbnail', 'large')));
		$this->set('lMultiSelect', $lMultiSelect);
		return $this->render('select_thumb_options');
	}

	public function getThumbsContent() {
		$this->set('images', $this->lessonModel->getImageList(array('object_type' => 'Lesson'), array('shop_thumbnail', 'large')));
		return $this->render('view_thumbs');
	}

	public function upload($data) {
		$input = 'image';
		$mediaModel = new LessonModel('media');
		if (isset($_FILES[$input]['type']) && strpos($_FILES[$input]['type'], 'image') !== false) {
			$response = $mediaModel->uploadMedia($input, 'image', 'Lesson', $data['lesson_id']);
			if ($response['status'] == 'OK') {
				$response['thumbsHTML'] = $this->getThumbsContent();
			}
			return $response;
		}
		return array('status' => 'ERROR', 'errMsg' => 'Неверный формат изображения');
	}

	public function deleteImage($data) {
		$mediaModel = new LessonModel('media');
		foreach(explode(',', $data['img_ids']) as $img_id) {
		$response = $mediaModel->delMediaItem($img_id);
			if ($response['status'] == 'ERROR') {
				return array('status' => 'ERROR', 'errMsg' => 'Ошибка удаления изображения');
			}
		}
		$response['thumbsHTML'] = $this->getThumbsContent();
		return $response;
	}
}
