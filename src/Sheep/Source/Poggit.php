<?php
declare(strict_types = 1);


namespace Sheep\Source;

use React\Promise\Deferred;
use React\Promise\Promise;
use Sheep\Utils\Error;
use Sheep\Utils\Utils;

/**
 * Class Poggit
 * @package Sheep\Source
 */
class Poggit extends BaseSource {
	const ENDPOINT = "https://poggit.pmmp.io/releases.json";

	public function search(string $query) : Promise {
	}

	public function resolve(string $plugin, string $version) : Promise {
		$deferred = new Deferred();

		$this->asyncHandler->getURL(self::ENDPOINT . "?name=$plugin" . ($version !== "latest" ? "&version=$version" : ""))
			->then(function($data) use (&$deferred) {
				$ret = [];

				$plugins = json_decode($data, true);
				foreach($plugins as $plugin) {
					$ret[] = new PoggitPlugin($plugin);
				}

				$deferred->resolve($ret);
			})
			->otherwise(function($error) use ($deferred) {
				$deferred->reject(new Error($error, Error::E_CURL_ERROR));
			});
		return $deferred->promise();
	}
}