<?php
declare(strict_types = 1);


namespace Sheep\Source;


use React\Promise\Deferred;
use React\Promise\Promise;
use Sheep\Async\AsyncHandler;
use Sheep\Plugin;
use Sheep\Utils\Error;

abstract class BaseSource implements Source {
	protected $asyncHandler;

	public function __construct(AsyncHandler $asyncHandler) {
		$this->asyncHandler = $asyncHandler;
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
				->then(function(array $results) use (&$deferred, &$dependencies, &$resolved, &$resolver) {
					if(count($results) !== 1) {
					}

					$resolved[] = $results[0];
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

	public function update(Plugin $plugin) : Promise {
		$deferred = new Deferred();
		$current = $plugin->getVersion();
		$this->resolve($plugin->getName(), "latest")
			->then(function(array $resolved) use (&$deferred, $current) {
				if(count($resolved) === 1) {
					$plugin = $resolved[0];
					// Poggit's not enforcing semver yet...not sure how else to compare.
					// TODO: maybe source-defined version comparison?
					if($plugin->getVersion() !== $current) {
						$this->download($plugin, \Sheep\PLUGIN_PATH . DIRECTORY_SEPARATOR . $plugin->getName() . ".phar.update")
							->then(function() use (&$deferred) {
								$deferred->resolve();
							})
							->otherwise(function(Error $error) use (&$deferred) {
								$deferred->reject($error);
							});
					} else {
						$deferred->reject(new Error("Plugin is already at it's latest version"));
					}
				}
			})
			->otherwise(function(Error $error) use (&$deferred) {
				$deferred->reject($error);
			});


		return $deferred->promise();
	}


	private function download(Plugin $plugin, string $location) : Promise {
		$deferred = new Deferred();

		$this->asyncHandler->getURL($plugin->getUri())
			// then write file...
			->then(function($content) use (&$deferred, $location) {
				$this->asyncHandler->write($location, $content)
					->then(function() use (&$deferred) {
						$deferred->resolve();
					});
			})
			->otherwise(function(string $error) use (&$deferred) {
				$deferred->reject(new Error($error, Error::E_CURL_ERROR));
			});

		return $deferred->promise();
	}
}