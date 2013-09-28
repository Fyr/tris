<?
function processPath($aPath, $callbackProcessFn, $recursive = false, $aParams = array()) {
	if (isset($aPath['files'])) {
		foreach($aPath['files'] as $fname) {
			call_user_func($callbackProcessFn, $fname, $aPath['path'], $aParams);
		}
	}
	if ($recursive && isset($aPath['folders'])) {
		foreach($aPath['folders'] as $folder) {	
			processPath(getPathContent($aPath['path'].$folder.'/'), $callbackProcessFn, true, $aParams);
		}
	}
}

function getPathContent($path) {
	if (substr($path, -1, 1) !== '/') {
		$path.= '/';
	}
	$_ret = array('path' => $path);
	$dirs = array();
	$d = dir($path);
	while (false !== ($entry = $d->read())) {
		if ($entry !== '.' && $entry !== '..' && $entry !== '.svn') {
			if (is_dir($path.$entry)) {
				$_ret['folders'][] = $entry;
			} else {
				$_ret['files'][] = $entry;
			}
		}
	}
	$d->close();
	return $_ret;
}
?>
