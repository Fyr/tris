<?
require_once('snippet_component.php');

class SnippetActions extends SnippetComponent {

	protected $View;
	protected $paraID, $snippetInfo;

	protected $uses = array();
	protected $components = array();

	public function __construct($snippetInfo, $paraID) {
		parent::__construct($snippetInfo, $paraID);
		foreach($this->uses as $snippet) {
			$this->components[$snippet] = $this->_loadSnippet($snippet);
		}
	}

	protected function _loadSnippet($snippet) {
		$class = getSnippetClass($snippet, $this->snippetInfo['basePath'], $this->paraID, 'component');
		$this->View->setObj($snippet, $class);
		return $class;
	}

	protected function getComponent($snippet) {
		return $this->components[$snippet];
	}

}