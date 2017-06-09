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

	public function update(Plugin $plugin) : Promise {
		$deferred = new Deferred();
		$current = $plugin->getVersion();
		$this->resolve($plugin->getName(), "latest")
			->then(function(Plugin $plugin) use (&$deferred, $current) {
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
			})
			->otherwise(function(Error $error) use (&$deferred) {
				$deferred->reject($error);
			});


		return $deferred->promise();
	}


	protected function download(Plugin $plugin, string $location) : Promise {
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