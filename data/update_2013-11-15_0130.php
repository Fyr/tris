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

$paraModel = LessonModel::getModel('paragraphs');
$aRowset = $paraModel->findAll();
foreach($aRowset as $row) {
	$content_cached = processContent($row['content_cached']);
	$paraModel->save(array('id' => $row['id'], 'content_cached' => $content_cached));
}

/*
$mediaModel = LessonModel::getModel('media');
$aRowset = $mediaModel->findAll();
foreach($aRowset as $row) {
	$image = $row['file'];
	if ($row['object_type'] == 'Lesson' || $row['object_type'] == 'LessonThumb') {
		echo '!';
		$image = preg_replace('/image_[0-9a-z]+\.([a-zA-Z]+)/', 'image.$1', $image);
		echo $image."<br>";
		$mediaModel->save(array('id' => $row['id'], 'file' => $image));
	}
	
}
*/
echo 'Done';

function processContent($html) {
	return preg_replace('/src="\/lesson\/files\/image\/(\d+)\/(\d+)\/image([_a-z0-9]+)\.([a-zA-Z]+)"/', 'src="/thumb.php?id=$2&file=image$3.$4&viewmode=desktop"', $html);
}


function fdebug($data, $logFile = 'tmp.log', $lAppend = true) {
	file_put_contents($logFile, mb_convert_encoding(print_r($data, true), 'cp1251', 'utf8'), ($lAppend) ? FILE_APPEND : null);
}