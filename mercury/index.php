<?
define('BASE_DIR', '../lesson/');
define('INCLUDE_DIR', BASE_DIR.'includes/');
define('MODEL_DIR', BASE_DIR.'models/');
define('API_DIR', BASE_DIR.'api/');
define('UPLOAD_DIR', BASE_DIR.'files/');

require_once(BASE_DIR.'../wp-config.php');

require_once(INCLUDE_DIR.'request.php');
require_once(INCLUDE_DIR.'db_adapter.php');
require_once(INCLUDE_DIR.'model.php');
require_once(INCLUDE_DIR.'view.php');

require_once('./includes/snippet_actions.php');
require_once('./includes/init_snippets.php');

$type = Request::GET('type', 'snippet');
$snippet = Request::GET('snippet');
$action = Request::GET('action');
$paraID = Request::GET('paraID');

$response = initSnippetResponse($snippet, $action, $paraID, $type);
if (is_array($response)) {
	exit(json_encode($response));
}
echo $response;

function fdebug($data, $logFile = 'tmp.log', $lAppend = true) {
	file_put_contents($logFile, mb_convert_encoding(print_r($data, true), 'cp1251', 'utf8'), ($lAppend) ? FILE_APPEND : null);
}
