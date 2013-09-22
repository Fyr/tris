<?
class SnippetActions {

	protected $paraID;

	public function __construct($paraID) {
		$this->paraID = $paraID;
		$this->lessonModel = new LessonModel();
	}
}