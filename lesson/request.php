<?php
class Request {

	public static function GET($param, $default = NULL) {
		if (isset($_GET[$param])) {
			return $_GET[$param];
		} else {
			return $default;
		}
	}

	public static function POST($param, $default = NULL) {
		if (isset($_POST[$param])) {
			return $_POST[$param];
		} else {
			return $default;
		}
	}

	public static function SESSION($_param) {
		if (isset($_SESSION[$_param])) {
			return $_SESSION[$_param];
		} else {
			return '';
		}
	}

	public static function COOKIE($_param, $default = NULL) {
		if (isset($_COOKIE[$_param])) {
			return $_COOKIE[$_param];
		} else {
			return $default;
		}
	}

	public static function redirect($url = '') {
		if ($url == '') {
			$url = $_SERVER["REQUEST_URI"];
		}
		header('Location:'.$url);
	}
}
