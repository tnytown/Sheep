<?php
/**
 * Copyright (c) 2017, 2018 KnownUnown
 *
 * This file is part of Sheep.
 *
 * Sheep is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Sheep is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);


namespace Sheep\Source;

use React\Promise\Deferred;
use React\Promise\Promise;
use Sheep\Plugin;
use Sheep\Utils\Error;

/**
 * Class Poggit
 * @package Sheep\Source
 */
class PoggitSource extends BaseSource {
	const ENDPOINT = "https://poggit.pmmp.io/releases.json";

	public function search(string $query): Promise {
	}

	public function install(Plugin... $plugin): Promise {
		$deferred = new Deferred();

		$target = array_shift($plugin);
		$depends = $target->getDependencies();

		$rejector = function(Error $error) use (&$deferred) {
			$deferred->reject($error);
		};

		$resolver = function(array $dependencies, array &$resolved = []) use (&$resolver, &$rejector) : Promise {
			$deferred = new Deferred();

			if(count($dependencies) === 0) {
				$deferred->resolve($resolved); // punt result up the stack
				return $deferred->promise();
			}

			$current = array_shift($dependencies);
			// skip non-hard dependencies
			if(!$current["isHard"]) {
				return $resolver($dependencies, $resolved);
			}

			$this->resolve($current["name"], $current["version"])
				->then(function(Plugin $plugin) use (&$deferred, &$dependencies, &$resolved, &$resolver, &$rejector) {
					$resolved[] = $plugin;
					$resolver($dependencies, $resolved)
						->then(function($resolved) use (&$deferred) {
							$deferred->resolve($resolved);
						})
						->otherwise($rejector);
				})
				->otherwise($rejector);

			return $deferred->promise();
		};

		$installer = function() use (&$deferred, $target, $plugin, $rejector) {
			$this->download($target, \Sheep\PLUGIN_PATH . DIRECTORY_SEPARATOR . $target->getName() . ".phar")
				->then(function() use (&$deferred, $plugin) {
					if(count($plugin) > 0) {
						$this->install(...$plugin);
					} else { // or resolve the promise.
						$deferred->resolve();
					}
				})
				->otherwise($rejector);
		};

		// resolution of dependencies
		$resolver($depends)
			->then(function(array $resolved) use (&$installer) {
				// install dependencies
				if(count($resolved) > 0) {
					$this->install(...$resolved)
						->then($installer);
				} else {
					$installer();
				}
			})
			->otherwise($rejector);

		return $deferred->promise();
	}

	public function resolve(string $plugin, string $version): Promise {
		$deferred = new Deferred();

		$this->asyncHandler->getURL(self::ENDPOINT . "?name=$plugin" . ($version !== "latest" ? "&version=$version" : "&latest-only"))
			->then(function($data) use (&$deferred) {
				$plugins = json_decode($data, true);
				if($plugins === null) $deferred->reject(new Error("Invalid data received from Poggit API", Error::E_CURL_ERROR));
				else if(count($plugins) === 1) {
					$deferred->resolve(new PoggitPlugin($plugins[0]));
				} else {
					$deferred->reject(count($plugins) === 0 ?
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
