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

		// actual install logic
		$installer = function() use (&$deferred, $target, $plugin) {
			// get plugin from remote...
			$this->asyncHandler->getURL($target->getUri())
				// then write file...
				->then(function($content) use (&$deferred, $target, $plugin) {
					$this->asyncHandler->write(\Sheep\PLUGIN_PATH . DIRECTORY_SEPARATOR . $target->getName() . ".phar", $content)
						// then maybe install more plugins...?
						->then(function() use (&$deferred, $plugin) {
							if(count($plugin) > 0) {
								$this->install(...$plugin);
							} else { // or resolve the promise.
								$deferred->resolve();
							}
						});
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

	}
}