<?
require_once('wp-config.php');

define('BASE_DIR', './lesson/');
define('INCLUDE_DIR', BASE_DIR.'includes/');
define('MODEL_DIR', BASE_DIR.'models/');
define('API_DIR', BASE_DIR.'api/');
define('PUBLIC_DIR', './lesson/assets/');
define('UPLOAD_DIR', './lesson/files/');
define('LAST_VISITED', 3);

define('UPLOADS_DIR', '/wp-content/uploads/');

require_once(INCLUDE_DIR.'request.php');
require_once(INCLUDE_DIR.'db_adapter.php');
require_once(INCLUDE_DIR.'model.php');
require_once(INCLUDE_DIR.'view.php');
require_once(INCLUDE_DIR.'lib_text.php');
require_once(API_DIR.'load_api.php');

require_once(BASE_DIR.'actions.php');

$snipOptsModel = LessonModel::getModel('snippet_options');

$aRowset = $snipOptsModel->findAll(array('option_key' => 'img_src'));
foreach($aRowset as $row) {
	$a = explode('/', $row['value']);
	fdebug($a);
	$img_ids = $a[5];
	echo "{$img_ids}<br>";
	$snipOptsModel->save(array('id' => $row['id'], 'option_key' => 'img_ids', 'value' => $img_ids));
}
echo 'Done';

function fdebug($data, $logFile = 'tmp.log', $lAppend = true) {
	file_put_contents($logFile, mb_convert_encoding(print_r($data, true), 'cp1251', 'utf8'), ($lAppend) ? FILE_APPEND : null);
}