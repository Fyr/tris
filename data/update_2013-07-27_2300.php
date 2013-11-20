<?
echo 'Replace move_uploaded_file() on copy()<br>';

require_once('wp-config.php');

define('INCLUDE_DIR', './lesson/');
define('PUBLIC_DIR', './lesson/');
define('AUDIO_DIR', './lesson/audio/');
define('UPLOAD_DIR', './lesson/files/');
define('UPLOADS_DIR', '/wp-content/uploads/');
define('LAST_VISITED', 3);

require_once(INCLUDE_DIR.'db_adapter.php');
require_once(INCLUDE_DIR.'model.php');
require_once(INCLUDE_DIR.'actions.php');
require_once(INCLUDE_DIR.'lib_text.php');

// echo file_exists('/lesson/files/image/0/29/image.jpg');
$snipOptsModel = new LessonModel('snippet_options');
$mediaModel = new LessonModel('media');
$sql = "SELECT so.id, so.value, lesson_id
FROM ls_snippet_options AS so
JOIN ls_snippets AS s ON s.id = so.snippet_id
JOIN ls_paragraphs AS p ON p.id = s.paragraph_id
JOIN ls_chapters AS c ON c.id = p.chapter_id
WHERE so.option_key = 'img_src'";
$aOptions = $mediaModel->query($sql);
// fdebug($aOptions);
$count = 0;
$aImages = array();
foreach($aOptions as $option) {
	echo $option['value'].'...';

	if (isset($aImages[$option['value']])) {
		echo 'EXISTS '.$aImages[$option['value']].'<br/>';
		$snipOptsModel->save(array('id' => $option['id'], 'value' => $aImages[$option['value']]));
		continue;
	}

	$_FILES['image']['name'] = $_FILES['image']['tmp_name'] = '.'.$option['value'];

	$res = $mediaModel->uploadMedia('image', 'image', $option['lesson_id']);
	echo $res['status'];
	if ($res['status'] == 'ERROR') {
		echo ' '.mb_convert_encoding($res['errMsg'], 'cp1251', 'utf8');
	} else {
		echo ' '.$res['file'];
		$snipOptsModel->save(array('id' => $option['id'], 'value' => $res['file']));
		$aImages[$option['value']] = $res['file'];
	}
	echo '<br/>';
}

function fdebug($data, $logFile = 'tmp.log', $lAppend = true) {
	file_put_contents($logFile, mb_convert_encoding(print_r($data, true), 'cp1251', 'utf8'), ($lAppend) ? FILE_APPEND : null);
}