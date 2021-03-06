<?php
class View {

	private $template = '', $data = array(), $objData = array();

	public function __construct($template = '') {
		$this->setTemplate($template);
	}

	public function setTemplate($template) {
		$this->template = $template;
	}

	public function getTemplate() {
		return $this->template;
	}

	public function set($key, $value) {
		$this->data[$key] = $value;
	}

	public function getData() {
		return $this->data;
	}

	public function setObj($objName, $class) {
		$this->objData[$objName] = $class;
	}

	public function render($template = '', $data = array()) {
		$template = ($template) ? $template : $this->getTemplate();
		$data = ($data) ? $data : $this->getData();

		// Init variables for view from data storage
		foreach($data as $var => $val) {
			$$var = $val;
		}

		foreach($this->objData as $objName => $class) {
			$this->$objName = $class;
		}

		ob_start();
		include($template);
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
