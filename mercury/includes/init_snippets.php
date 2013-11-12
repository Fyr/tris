<?
function getSnippetPath($snippet, $type = 'snippet') {
	return BASE_DIR.'../mercury/'.$type.'s/'.strtolower($snippet).'/';
}

function getSnippetClassName($snippet, $type = 'snippet') {
	$className = ($type == 'component') ? 'Component' : 'Actions';
	$class = getSnippetPath($snippet, $type).strtolower($className).'.php';
	if (file_exists($class)) {
		$className = ucfirst($snippet.$className);
		require_once($class);
	} else {
		$className = 'SnippetActions'; // by default - use parent class
	}
	return $className;
}

function getSnippetClass($snippet, $paraID, $type = 'snippet') {
	$className = getSnippetClassName($snippet, $type);
	$snippetInfo = array(
		'snippet' => $snippet,
		'action' => $action,
		'path' => getSnippetPath($snippet, $type), // used to render views
		'assetsPath' => '/mercury/snippets/'.$snippet.'/assets/' // used inside views to get public resources (for ex. preview)
	);
	return new $className($snippetInfo, $paraID);
}

function initSnippetResponse($snippet, $action, $paraID, $type = 'snippet') {
	$snippetActions = getSnippetClass($snippet, $paraID, $type);
	$response = $snippetActions->$action($_POST);
	return $response;
}