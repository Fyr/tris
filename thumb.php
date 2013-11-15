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
@require_once(INCLUDE_DIR.'image.php');

$mediaModel = LessonModel::getModel('media');

$id = Request::GET('id', 0);
$file = Request::GET('file', '');
$aSize['w'] = Request::GET('w', '');
$aSize['h'] = Request::GET('h', '');
$mode = Request::GET('viewmode', '');

$path = $mediaModel->getPath('image', $id);
$aFName = getFileInfo($file, $aSize, $mode);
$fname = $path.$aFName['fname'].'.'.$aFName['ext'];
if (file_exists($fname)) {
	header('Content-type: image/'.$aFName['ext']);
	echo file_get_contents($fname);
	exit;
}

$orig_fname = $path.$aFName['orig_fname'].'.'.$aFName['orig_ext'];
if (!file_exists($orig_fname)) {
	// fix original file name by media ID if it was set incorrectly
	$media = $mediaModel->getItem($id);
	$orig_fname = $path.$media['file'];
}

$image = new Image();
$image->load($orig_fname);
$aPerc = array('ipad' => 80, 'mobile' => 50);
if ($mode && isset($aPerc[$mode])) {
	// decrease image due to view mode
	$aSize['w'] = intval($image->getSizeX() * $aPerc[$mode] / 100);
}
if ($aSize['w'] || $aSize['h']) {
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

function getFileInfo($filename, $aSize = array(), $mode = '') {
	$aFName = explode('.', $filename);
	$_ret = array('orig_fname' => $aFName[0], 'fname' => $aFName[0], 'orig_ext' => $aFName[1]);
	if ($aSize['w'] || $aSize['h']) {
		$_ret['fname'] = $aSize['w'].'x'.$aSize['h'];
	} else if ($mode) {
		$_ret['fname'] = $mode;
	}
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

