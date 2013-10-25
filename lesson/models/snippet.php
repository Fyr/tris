<?
class SnippetModel extends LessonModel {

	public function deleteSnippetOptions($paraID) {
		$this->db->query(
			$this->db->prepare('DELETE FROM '.$this->getTableName('snippet_options').'
				WHERE snippet_id IN (SELECT id FROM '.$this->getTableName('snippets').' AS s WHERE s.paragraph_id = %d)', $paraID)
		);
	}

	public function getSnippetOptions($paraID) {
		return $this->db->get_results(
			$this->db->prepare('SELECT so.*, s.* FROM ls_snippets AS s
LEFT JOIN ls_snippet_options AS so ON so.snippet_id = s.id
WHERE s.paragraph_id = %d', $paraID),
			ARRAY_A
		);
	}
}