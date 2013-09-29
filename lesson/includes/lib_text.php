<?
/**
 * Return user-friendly date
 *
 * @param string $sqlDate
 * @return string
 */
function userDate($sqlDate) {
	$date = date('d.m.Y', strtotime($sqlDate));
	if ($date == date('d.m.Y')) {
		$date = 'Сегодня '.date('H:i', strtotime($sqlDate));
	}
	return $date;
}

/**
 * Highlights a keyword in a text ($body)
 *
 * @param string $body - text to search in
 * @param string $keyword - a keyword to hightlight
 * @return string
 */
function highlight($body, $keyword) {
	if ($body && $keyword) {
		return preg_replace('/'.$keyword.'/iu', '<span class="highlight">'.$keyword.'</span>', $body);
	}
	return $body;
}

/**
 * Returns a chunk of text with highlighted keyword
 *
 * @param string $body - text to search keyword in
 * @param string $keyword - keyword to search for
 * @return string
 */
function around($body, $keyword) {
	$body = excerpt($body, $keyword, 150, '...');
	$body = highlight($body, $keyword);
	return $body;
}

/**
 * Return a chunk of text containing a phrase with a text around this phrase
 *
 * @param string $text - text to search phrase in
 * @param string $phrase - phrase to search for
 * @param int $radius - amount of chars that is before the phrase and after it
 * @param string $ending - string that is added if text was cut by radius
 * @return string
 */
function excerpt($text, $phrase, $radius = 100, $ending = "...") {
	$excerpt = $text;
	$pos = mb_strpos(mb_strtolower($excerpt), mb_strtolower($phrase));
	$endingL = '';
	if ($pos > $radius) {
		$excerpt = mb_substr($excerpt, $pos - $radius);
		$pos = $radius;
		$endingL = $ending;
	}
	if (($pos + mb_strlen($phrase) + $radius) < mb_strlen($excerpt)) {
		$excerpt = mb_substr($excerpt, 0, $pos + mb_strlen($phrase) + $radius).$ending;
	}
	return $endingL.$excerpt;
}

/**
 * Correctly strip all tags including <style> tag
 *
 * @param string $body
 * @return string
 */
function strip_all_tags($body) {
	$pos = $pos2 = 0;
	$key = '</style>';
	while (($pos = strpos($body, '<style')) !== false) {
		$pos2 = strpos($body, $key, $pos);
		$body = substr($body, 0, $pos).substr($body, $pos2 + strlen($key));
	}
	$body = strip_tags($body);
	return $body;
}