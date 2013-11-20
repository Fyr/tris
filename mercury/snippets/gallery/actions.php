<?
class GalleryActions extends SnippetActions {

	protected $uses = array('MediaLib');

	public function preview() {
		$images = $this->lessonModel->getImageList(
			array('id' => explode(',', Request::POST('img_ids'))),
			array('shop_thumbnail', 'large')
		);
		$this->set('images', $images);
		return parent::preview();
	}

}
