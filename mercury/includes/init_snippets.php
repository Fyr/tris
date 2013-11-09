<?
function getSnippetPath($snippet, $basePath, $type = 'snippet') {
	return $basePath.$type.'s/'.strtolower($snippet).'/';
}

function getSnippetClassName($snippet, $basePath, $type = 'snippet') {
	$className = ($type == 'component') ? 'Component' : 'Actions';
	$class = getSnippetPath($snippet, $basePath, $type).strtolower($className).'.php';
	if (file_exists($class)) {
		$className = ucfirst($snippet.$className);
		require_once($class);
	} else {
		$className = 'SnippetActions'; // by default - use parent class
	}
	return $className;
}

function getSnippetClass($snippet, $basePath, $paraID, $type = 'snippet') {
	$className = getSnippetClassName($snippet, $basePath, $type);
	$snippetInfo = array(
		'snippet' => $snippet,
		'action' => $action,
		'basePath' => $basePath,
		'path' => getSnippetPath($snippet, $basePath, $type), // used to render views
		'assetsPath' => '/mercury/snippets/'.$snippet.'/assets/' // used inside views to get public resources (for ex. preview)
	);
	return new $className($snippetInfo, $paraID);
}

function initSnippetResponse($snippet, $action, $basePath, $paraID) {
	$snippetActions = getSnippetClass($snippet, $basePath, $paraID);
	$response = $snippetActions->$action($_POST);
	return $response;
}