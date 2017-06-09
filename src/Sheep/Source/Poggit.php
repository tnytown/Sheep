<?php
declare(strict_types = 1);


namespace Sheep\Source;

use React\Promise\Deferred;
use React\Promise\Promise;
use Sheep\Plugin;
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

		$this->asyncHandler->getURL(self::ENDPOINT . "?name=$plugin" . ($version !== "latest" ? "&version=$version" : "&latest-only"))
			->then(function($data) use (&$deferred) {
				$plugins = json_decode($data, true);
				if(count($plugins) === 1) {
					$deferred->resolve(new PoggitPlugin($plugins[0]));
				} else {
					$deferred->reject($plugins === 0 ?
						new Error("Plugin/version not found", Error::E_PLUGIN_NO_CANDIDATES) :
						new Error("Too many plugins/versions found", Error::E_PLUGIN_MULTIPLE_CANDIDATES));
				}
			})
			->otherwise(function($error) use ($deferred) {
				$deferred->reject(new Error($error, Error::E_CURL_ERROR));
			});
		return $deferred->promise();
	}
}