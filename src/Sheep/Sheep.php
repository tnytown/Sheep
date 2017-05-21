<?php
declare(strict_types = 1);


namespace Sheep;

use React\Promise\Deferred;
use React\Promise\Promise;
use Sheep\Async\AsyncHandler;
use Sheep\Source\Source;
use Sheep\Source\SourceManager;

/**
 * The Sheep API.
 * @package Sheep
 */
class Sheep {
	private static $instance;

	/** @var SourceManager */
	private $sourceManager;
	private $defaultSource;

	public function info(string $plugin, string $version, Source $source = null) : Promise {
		if($source === null) $source = $this->defaultSource;
		return $source->resolve($plugin, $version);
	}

	public function install(string $plugin, string $version, Source $source = null) : Promise {
		$deferred = new Deferred();
		if($source === null) $source = $this->defaultSource;

		$this->info($plugin, $version, $source)
				->then(function($results) use (&$deferred, &$source) {
					if(count($results) !== 1) {
						// < 1: "no plugins", > 1: "too many plugins"
					}

					/** @var Plugin $plugin */
					$plugin = $results[0];
					$source->install($plugin)
						->then(function() use (&$deferred) {
							$deferred->resolve();
						})
						->otherwise(function($error) use (&$deferred) {
							$deferred->reject($error);
						});
				})
				->otherwise(function($error) use (&$deferred) {
					$deferred->reject($error);
				});
		return $deferred->promise();
	}

	public function update() {

	}

	public function uninstall(string $plugin) {

	}

	public function init(AsyncHandler $asyncHandler) {
		$this->sourceManager = new SourceManager($asyncHandler);
		$this->defaultSource = $this->sourceManager->getDefaultSource();
	}

	public static function getInstance() : Sheep {
		if(!self::$instance) {
			self::$instance = new Sheep();
		}
		return self::$instance;
	}

	/**
	 * @return SourceManager
	 */
	public function getSourceManager(): SourceManager {
		return $this->sourceManager;
	}
}