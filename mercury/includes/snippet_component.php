<?
class SnippetComponent {

	protected $View;
	protected $paraID, $snippetInfo;
	protected $uses = array();
	protected $components = array();

	public function __construct($snippetInfo, $paraID) {
		$this->View = new View();

		$this->paraID = $paraID;
		$this->snippetInfo = $snippetInfo;
		$this->lessonModel = new LessonModel();
	}

	protected function set($key, $value) {
		$this->View->set($key, $value);
	}

	public function render($action) {
		$this->set('assetsPath', $this->snippetInfo['assetsPath']);
		return $this->View->render($this->snippetInfo['path'].'view/'.$action.'.php');
	}

	public function options() {
		return $this->render('options');
	}

	public function preview() {
		return $this->render('preview');
	}
}