<?
if (!session_id()) {
    session_start();
}
require_once('wp-config.php');

// global $user_ID, $user_identity;
if (!$user_ID || !$user_identity) {
	$user_ID = 0;
	$user_identity = 'Гость';
}

define('BASE_DIR', '/lesson/');
define('INCLUDE_DIR', BASE_DIR.'includes/');
define('PUBLIC_DIR', './lesson/assets/');
define('UPLOAD_DIR', './lesson/files/');
define('LAST_VISITED', 3);

define('UPLOADS_DIR', '/wp-content/uploads/');

require_once(INCLUDE_DIR.'request.php');
require_once(INCLUDE_DIR.'db_adapter.php');
require_once(INCLUDE_DIR.'model.php');
require_once(INCLUDE_DIR.'view.php');
require_once(INCLUDE_DIR.'lib_text.php');

require_once(BASE_DIR.'actions.php');

require_once('/mercury/snippets/snippet_actions.php');
require_once('/mercury/snippets/init_snippets.php');

// $lEditMode = Request::GET('edit', false);// ($user_ID && $user_identity);
// Init edit mode via session - this is the only secure way for AJAX-requests
$lEditMode = false;
if (isset($_GET['edit'])) {
	$lEditMode = $_GET['edit'];
	$_SESSION['_lesson_edit_mode'] = $lEditMode;
} else if (isset($_SESSION['_lesson_edit_mode'])) {
	$lEditMode = $_SESSION['_lesson_edit_mode'];
}

$action = Request::GET('action'); // (isset($_GET['action']) && $_GET['action']) ? $_GET['action'] : '';
$lessonID = Request::GET('id', 0); // (isset($_GET['id']) && $_GET['id']) ?$_GET['id'] : 0;
$paraID = Request::GET('p', 0);

$lsActions = new LessonActions($lessonID, $user_ID);
if ($lessonID && $action) {
	$response = $lsActions->$action($_POST);
	if ($response['status'] == 'OK') {
		if ($lEditMode || $action == 'switchPara') {
			$response['html'] = $lsActions->getChaptersContent($_SESSION['_lesson_edit_mode'], $_POST['id']);
		}
	}
	exit(json_encode($response));
}

$paraModel = new LessonModel('paragraphs');

// Init current paragraph
$paragraph = false;
if (!$paraID) {
	$paragraph = $paraModel->getFirstParagraph($lessonID);
	$paraID = ($paragraph['id']) ? $paragraph['id'] : 0;
	if (!$paraID) {
		if ($lEditMode) {
			// create sample chapter with paragraph
			$lsActions->lessonInit();
			$paragraph = $paraModel->getFirstParagraph($lessonID);
			$paraID = ($paragraph['id']) ? $paragraph['id'] : 0;
		} else {
			// page does not exist - redirect user
			header('Location:/');
			exit;
		}
	}
} else {
	$paragraph = $paraModel->getItem($paraID);
}

$aParaInfo = $paraModel->getParagraphList(array('lesson_id' => $lessonID)); // for navigation btw paragraphs (next, prev, slider)

if (!$lEditMode) {
	$lsActions->updateVisited($paraID);
}

// Set variables for layout
// $paraModel->_getLessonInfo($lessonID);
$lessonInfo = $lsActions->getLessonInfo($lessonID);
$thumbHTML = $lsActions->getThumbContent($lEditMode);

$chaptersHTML = $lsActions->getChaptersContent($lEditMode, $paraID); // Content of 'Contents' panel

$aFavoriteInfo = $lsActions->getFavoriteContent(); // Content of 'Favorite' panel
$favoriteHTML = $aFavoriteInfo['html'];
$favoriteCount = $aFavoriteInfo['count'];

$aPostsInfo = $lsActions->getPostsContent($paraID); // Content of 'Posts' panel
$postsHTML = $aPostsInfo['html'];
$postsCount = $aPostsInfo['count'];

$notesInfo = $lsActions->getNotesContent($paraID);
$notesHTML = $notesInfo['html'];
$notesCount = $notesInfo['count'];

// $contentHTML = ($paragraph && $paragraph['content_cached']) ? $paragraph['content_cached'] : '';
if ($lEditMode) {
	$contentHTML = $lsActions->renderContent($paraID, true); // get HTML-content for main text
	$aSnippetOptions = $lsActions->getSnippetOptions($paraID); // get snippet options for main text
} else {
	$contentHTML = ($paragraph && $paragraph['content_cached']) ? $paragraph['content_cached'] : '';
	// $contentHTML = $lsActions->renderContent($paraID, false); // get HTML-content for main text
}

$audioModel = new LessonModel('media');
$audio = $audioModel->getMediaItem('audio', 'Paragraph', $paraID);

require_once(BASE_DIR.'layout.php');

function fdebug($data, $logFile = 'tmp.log', $lAppend = true) {
	file_put_contents($logFile, mb_convert_encoding(print_r($data, true), 'cp1251', 'utf8'), ($lAppend) ? FILE_APPEND : null);
}