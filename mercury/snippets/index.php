<?
require_once('../../wp-config.php');

require_once('../../lesson/request.php');
require_once('../../lesson/db_adapter.php');
require_once('../../lesson/model.php');

require_once('snippet_actions.php');

define('UPLOADS_DIR', '/wp-content/uploads/');
define('UPLOAD_DIR', '../../lesson/files/');

$snippet = Request::GET('snippet');
$action = Request::GET('action');
$paraID = Request::GET('paraID');

require_once('./'.$snippet.'/actions.php');

$className = ucfirst($snippet.'Actions');

$snippetActions = new $className($paraID);
$response = $snippetActions->$action($_POST);
exit(json_encode($response));

function fdebug($data, $logFile = 'tmp.log', $lAppend = true) {
	file_put_contents($logFile, mb_convert_encoding(print_r($data, true), 'cp1251', 'utf8'), ($lAppend) ? FILE_APPEND : null);
}