<?php
class Mime {
	public static $mime_types_path = 'C:\wamp64\bin\apache\apache2.4.54.2\conf\mime.types';
	public static $save_path = __DIR__.'/mime_types.php';
	public static $types;
	static function reset() {
		self::$types = null;
		if (file_exists(self::$save_path)) {
			unlink(self::$save_path);
		}
	}
	static function getTypes() {
		if (isset(self::$types)) {
			return self::$types;
		}
		if (file_exists(self::$save_path)) {
			return self::$types = include self::$save_path;
		}
		# Returns the system MIME type mapping of extensions to MIME types, as defined in /etc/mime.types.
		$out = [];
		$file = fopen(self::$mime_types_path, 'r');
		while (($line = fgets($file)) !== false) {
			$line = trim(preg_replace('/#.*/', '', $line));
			$parts = preg_split('/\s+/', $line);
			if (count($parts) <= 1) {
				continue;
			}
			$type = array_shift($parts);
			foreach ($parts as $part) {
				$out[$part] = $type;
			}
		}
		fclose($file);
		if (!empty(self::$save_path)) {
			file_put_contents(self::$save_path, '<?php return ' . var_export($out, true) . ';');
		}
		self::$types = $out;
		return $out;
	}

	static function fromExtension($file) {
		# Returns the system MIME type (as defined in /etc/mime.types) for the filename specified.
		#
		# $file - the filename to examine
		$types = self::getTypes();
		$ext = pathinfo($file, PATHINFO_EXTENSION);
		if (!$ext) {
			$ext = $file;
		}
		$ext = strtolower($ext);
		return isset($types[$ext]) ? $types[$ext] : null;
	}
	static function extensions($type) {
		# Returns the canonical file extension for the MIME type specified, as defined in /etc/mime.types (considering the first
		# extension listed to be canonical).
		#
		# $type - the MIME type
		$types = self::getTypes();
		$type = strtolower($type);
		$out = array_filter($types, function ($v) use ($type) {
			return $v === $type;
		});
		$out = array_keys($out);
		var_dump($out);
		die;
	}
	static function header($file) {
		# Send the Content-Type header for the file specified, based on its extension.
		#
		# $file - the filename to send the Content-Type header for
		$type = self::fromExtension($file);
		if ($type) {
			header('Content-Type: ' . $type);
		}
	}
}
