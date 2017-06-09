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

	public function install(Plugin... $plugin) : Promise {
		$deferred = new Deferred();

		$target = array_shift($plugin);
		$depends = $target->getDependencies();

		// resolve all dependencies
		$resolver = function(array $dependencies, array &$resolved = []) use (&$resolver) : Promise {
			$deferred = new Deferred();

			if(count($dependencies) === 0) {
				$deferred->resolve($resolved); // punt result up the stack if we haven't exceeded the limit by now :p
				goto end;
			}

			$current = array_shift($dependencies);
			// skip non-hard dependencies
			if(!$current["isHard"]) return $resolver($dependencies, $resolved);
			$this->resolve($current["name"], $current["version"])
				->then(function(Plugin $plugin) use (&$deferred, &$dependencies, &$resolved, &$resolver) {
					$resolved[] = $plugin;
					$resolver($dependencies, $resolved)
						->then(function($resolved) use (&$deferred) {
							$deferred->resolve($resolved);
						});
				});

			end:
			return $deferred->promise();
		};

		$installer = function() use (&$deferred, $target, $plugin) {
			$this->download($target, \Sheep\PLUGIN_PATH . DIRECTORY_SEPARATOR . $target->getName() . ".phar")
				->then(function() use (&$deferred, $plugin) {
					if(count($plugin) > 0) {
						$this->install(...$plugin);
					} else { // or resolve the promise.
						$deferred->resolve();
					}
				});
		};

		$resolver($depends)
			->then(function(array $resolved) use (&$installer) {
				// install dependencies
				if(count($resolved) > 0) {
					$this->install(...$resolved)
						->then($installer);
				} else {
					$installer();
				}
			});

		return $deferred->promise();
	}
}