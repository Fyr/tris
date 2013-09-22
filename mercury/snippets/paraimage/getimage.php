<?
require_once('../../../wp-config.php');

require_once('../../../lesson/request.php');
require_once('../../../lesson/db_adapter.php');
require_once('../../../lesson/model.php');

define('UPLOADS_DIR', '/wp-content/uploads/');

$lessonModel = new LessonModel();
$imgID = $_POST['id'];
$images = $lessonModel->getImageList(array('post_id' => $imgID), array('shop_thumbnail', 'large'));

$response = array('status' => 'OK', 'thumb' => $images[$imgID]['shop_thumbnail'], 'image_src' => $images[$imgID]['large']);
exit(json_encode($response));

function fdebug($data, $logFile = 'tmp.log', $lAppend = true) {
	file_put_contents($logFile, mb_convert_encoding(print_r($data, true), 'cp1251', 'utf8'), ($lAppend) ? FILE_APPEND : null);
}