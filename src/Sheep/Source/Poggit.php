<?php


namespace Sheep\Source;

use Sheep\Utils\Error;
use Sheep\Utils\Utils;

class Poggit extends BaseSource {
	const ENDPOINT = "https://poggit.pmmp.io/releases.json";

	public function search(string $query, callable $callback) {
	}

	public function resolve(string $plugin, string $version, callable $callback) {
		Utils::curlGet(self::ENDPOINT . "?name=$plugin" . ($version !== "latest" ? "&version=$version" : ""), function($data, $error) use ($callback) {
			if($error !== "") return $callback(new Error($error, Error::E_CURL_ERROR), null);

			$ret = [];
			$plugins = json_decode($data, true);
			foreach($plugins as $plugin) {
				$ret[] = new PoggitPlugin($plugin);
			}

			$callback(null, $ret);
		});
	}
}