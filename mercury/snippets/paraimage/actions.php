<?
class ParaimageActions extends SnippetActions {

	protected $uses = array('MediaLib');

	public function preview() {
		$mediaModel = new LessonModel('media');
		$img_id = Request::POST('img_ids');
		$img = $this->lessonModel->getImageList(array('id' => $img_id), 'desktop');
		$this->set('img_src', $img[$img_id]['desktop']);
		return parent::preview();
	}
}
