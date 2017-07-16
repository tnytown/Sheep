<?php
declare(strict_types=1);


namespace Sheep\Async;


trait AsyncMixin {
	public function docURL(
		string $url,
		int $type,
		$timeout = 10,
		$extraHeaders = [],
		$args = [],
		&$err = null
	): string {
		$ch = curl_init($url);

		if ($type === CurlOptions::CURL_POST) {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
		}

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER,
			array_merge(["User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:12.0) Gecko/20100101 Firefox/12.0 Sheep"],
				$extraHeaders));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, (int)$timeout);
		curl_setopt($ch, CURLOPT_TIMEOUT, (int)$timeout);
		$ret = curl_exec($ch);
		$err = curl_error($ch);
		curl_close($ch);

		return $ret;
	}

	public function readFile(string $location): string {
		return @file_get_contents($location);
	}

	public function writeFile(string $location, string $contents) {
		return @file_put_contents($location, $contents);
	}
}