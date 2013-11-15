<?
require_once('wp-config.php');

define('BASE_DIR', './lesson/');
define('INCLUDE_DIR', BASE_DIR.'includes/');
define('MODEL_DIR', BASE_DIR.'models/');
define('API_DIR', BASE_DIR.'api/');
define('UPLOAD_DIR', './lesson/files/');

require_once(INCLUDE_DIR.'request.php');
require_once(INCLUDE_DIR.'db_adapter.php');
require_once(INCLUDE_DIR.'model.php');
// require_once(API_DIR.'load_api.php');

$mediaModel = LessonModel::getModel('media');

$id = Request::GET('id', 0);
$file = Request::GET('file', '');
$aSize['w'] = Request::GET('w', '');
$aSize['h'] = Request::GET('h', '');
$mode = Request::GET('viewmode', '');

$mediaModel->genImage($id, $file, $aSize, $mode);

function fdebug($data, $logFile = 'tmp.log', $lAppend = true) {
	file_put_contents($logFile, mb_convert_encoding(print_r($data, true), 'cp1251', 'utf8'), ($lAppend) ? FILE_APPEND : null);
}

