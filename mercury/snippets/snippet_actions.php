<?
class SnippetActions {

	protected $View;
	protected $paraID, $snippetInfo;

	public function __construct($snippetInfo, $paraID) {
		$this->View = new View();

		$this->paraID = $paraID;
		$this->snippetInfo = $snippetInfo;
		$this->lessonModel = new LessonModel();
	}

	protected function set($key, $value) {
		$this->View->set($key, $value);
	}

	protected function render($action) {
		$this->set('assetsPath', $this->snippetInfo['assetsPath']);
		return $this->View->render($this->snippetInfo['basePath'].'view/'.$action.'.php');
	}

	public function options() {
		return $this->render('options');
	}

	public function preview() {
		return $this->render('preview');
	}
}