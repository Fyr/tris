<?
class ParaimageActions extends SnippetActions {

	public function getImage($data) {
		$imgID = $data['id'];
		$images = $this->lessonModel->getImageList(array('post_id' => $imgID), array('shop_thumbnail', 'large'));
		return array('status' => 'OK', 'thumb' => $images[$imgID]['shop_thumbnail'], 'image_src' => $images[$imgID]['large']);
	}

	public function getThumbs($data) {
		$images = $this->lessonModel->getImageList(array(), array('shop_thumbnail'));
		return array('status' => 'OK', 'images' => $images);
	}

	public function getThumbsContent($data) {
		$images = $this->lessonModel->getImageList(array(), array('shop_thumbnail', 'large'));
		$img_src = $data['img_src'];

		ob_start();
		include('view_thumbs.php');
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	public function upload($data) {
		$input = 'image';
		$mediaModel = new LessonModel('media');
		if (isset($_FILES[$input]['type']) && strpos($_FILES[$input]['type'], 'image') !== false) {
			$response = $mediaModel->uploadMedia($input, 'image', $data['lesson_id']);
			if ($response['status'] == 'OK') {
				$response['thumbsHTML'] = $this->getThumbsContent($data);
			}
			return $response;
		}
		return array('status' => 'ERROR', 'errMsg' => 'Неверный формат изображения');
	}

	public function deleteImage($data) {
		$mediaModel = new LessonModel('media');

		$response = $mediaModel->delMediaItem($data['id']);
		if ($response['status'] == 'OK') {
			$response['thumbsHTML'] = $this->getThumbsContent($data);
		}
		return $response;
	}
}
