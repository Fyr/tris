<?
function initSnippetResponse($snippet, $action, $basePath, $paraID) {
	if (file_exists($basePath.'actions.php')) {
		$className = ucfirst($snippet.'Actions');
		require_once($basePath.'actions.php');
	} else {
		$className = 'SnippetActions'; // by default - use parent class
	}
	$snippetInfo = array(
		'snippet' => $snippet,
		'action' => $action,
		'basePath' => $basePath,
		'assetsPath' => '/mercury/snippets/'.$snippet.'/assets/'
	);
	$snippetActions = new $className($snippetInfo, $paraID);
	$response = $snippetActions->$action($_POST);
	return $response;
}