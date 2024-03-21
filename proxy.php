<?php
class Proxy {
	static public $allowed_users = [];
	static public $base_url = 'https://raw.githubusercontent.com/';
	static public function getUrl($raw=null) {
		if ($raw === null) {
			$raw = $_SERVER['REQUEST_URI'];
		}
		if (substr($raw, 0, 8) === '/github/') {
			$raw = substr($raw, 8);
		}
		while ($raw[0] === '/') {
			$raw = substr($raw, 1);
		}
		$user = substr($raw, 0, strpos($raw, '/'));
		if (!empty(self::$allowed_users) && !in_array($user, self::$allowed_users)) {
			return false;
		}
		return self::$base_url . $raw;
	}
	static public function call() {
		$url = self::getUrl($_SERVER['REQUEST_URI']);
		if ($url === false) {
			header("HTTP/1.1 404 Not Found");
			die;
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$data = curl_exec($ch);
		if(curl_errno($ch)){
			echo 'Curl error: ' . curl_error($ch);
			header("HTTP/1.1 404 Not Found");
			die;
		}
		curl_close($ch);
		
		Mime::header($url);
		// CORS
		header('Access-Control-Allow-Origin: *');
		echo $data;
		die;
	}
}