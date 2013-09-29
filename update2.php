<?
require_once('wp-config.php');

define('BASE_DIR', '/lesson/');
define('INCLUDE_DIR', BASE_DIR.'includes/');
define('PUBLIC_DIR', './lesson/');
define('UPLOAD_DIR', './lesson/files/');

define('UPLOADS_DIR', '/wp-content/uploads/');

require_once(INCLUDE_DIR.'request.php');
require_once(INCLUDE_DIR.'db_adapter.php');
require_once(INCLUDE_DIR.'model.php');
require_once(INCLUDE_DIR.'view.php');
require_once(INCLUDE_DIR.'lib_text.php');

require_once(BASE_DIR.'actions.php');

require_once('/mercury/snippets/snippet_actions.php');
require_once('/mercury/snippets/init_snippets.php');

$paraModel = new LessonModel('paragraphs');
$aRowset = $paraModel->findAll();
foreach($aRowset as $row) {
	$content_cached = processContent($row['content_cached']);
	$paraModel->save(array('id' => $row['id'], 'content_cached' => $content_cached));
}
echo 'Done';

function processContent($html) {
	return str_replace(array('/css/', '/img/'), array('/assets/css/', '/assets/img/'), $html);
}

function fdebug($data, $logFile = 'tmp.log', $lAppend = true) {
	file_put_contents($logFile, mb_convert_encoding(print_r($data, true), 'cp1251', 'utf8'), ($lAppend) ? FILE_APPEND : null);
}