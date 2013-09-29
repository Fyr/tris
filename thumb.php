<?
define('BASE_DIR', './lesson/');
define('INCLUDE_DIR', BASE_DIR.'includes/');
define('UPLOAD_DIR', './lesson/files/');

require_once(INCLUDE_DIR.'request.php');
require_once(INCLUDE_DIR.'db_adapter.php');
require_once(INCLUDE_DIR.'model.php');
@require_once(INCLUDE_DIR.'image.php');

$mediaModel = new LessonModel('media');

$id = Request::GET('id', 0);
$file = Request::GET('file', '');
$aSize['w'] = Request::GET('w', '');
$aSize['h'] = Request::GET('h', '');
$path = $mediaModel->getPath('image', $id);
$aFName = getFileInfo($file);
$fname = $path.$aSize['w'].'x'.$aSize['h'].'.'.$aFName['ext'];
// echo $fname.'.'.$aFName['ext'].'<br/>';
if (file_exists($fname)) {
	header('Content-type: image/'.$aFName['ext']);
	echo file_get_contents($fname);
	exit;
}

$image = new Image();
$image->load($path.$aFName['fname'].'.'.$aFName['orig_ext']);
if ($aSize) {
	$image->resize($aSize['w'], $aSize['h']); // 'f6f6f6'
}
if ($aFName['ext'] == 'jpg') {
	$image->outputJpg($fname);
	$image->outputJpg();
} elseif ($aFName['ext'] == 'png') {
	$image->outputPng($fname);
	$image->outputPng();
} else {
	$image->outputGif($fname);
	$image->outputGif();
}

exit;

function getFileInfo($filename) {
	$aFName = explode('.', $filename);
	$_ret = array('fname' => $aFName[0], 'orig_ext' => $aFName[1]);
	if (isset($aFName[2]) && $aFName[2]) {
		$_ret['ext'] = $aFName[2];
	} else {
		$_ret['ext'] = $aFName[1];
	}
	return $_ret;
}

function fdebug($data, $logFile = 'tmp.log', $lAppend = true) {
	file_put_contents($logFile, mb_convert_encoding(print_r($data, true), 'cp1251', 'utf8'), ($lAppend) ? FILE_APPEND : null);
}

