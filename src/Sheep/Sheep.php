<?php
declare(strict_types = 1);


namespace Sheep;

use React\Promise\Deferred;
use React\Promise\Promise;
use Sheep\Async\AsyncHandler;
use Sheep\Source\Source;
use Sheep\Source\SourceManager;
use Sheep\Utils\Error;
use Sheep\Utils\Lockfile;

/**
 * The Sheep API.
 * @package Sheep
 */
class Sheep {
	private static $instance;

	/** @var SourceManager */
	private $sourceManager;
	private $defaultSource;
	/** @var Lockfile */
	private $lockfile;

	public function info(string $plugin, string $version, Source $source = null) : Promise {
		if($source === null) $source = $this->defaultSource;
		return $source->resolve($plugin, $version);
	}

	public function install(string $plugin, string $version, Source $source = null) : Promise {
		$deferred = new Deferred();
		if($source === null) $source = $this->defaultSource;
		if($this->lockfile->exists($plugin)) {
			$deferred->reject(new Error("Plugin already installed.", Error::E_PLUGIN_ALREADY_INSTALLED));
			goto end;
		}

		$this->info($plugin, $version, $source)
				->then(function($results) use (&$deferred, &$source) {
					if(($num = count($results)) !== 1) {
						// < 1: "no plugins", > 1: "too many plugins"
						$deferred->reject($num === 0 ?
							new Error("No plugins with that name found", Error::E_PLUGIN_NO_CANDIDATES) :
							new Error("Too many plugins found", Error::E_PLUGIN_MULTIPLE_CANDIDATES));
						return;
					}

					/** @var Plugin $plugin */
					$plugin = $results[0];
					$source->install($plugin)
						->then(function() use (&$deferred, $plugin) {
							$this->lockfile->addPlugin($plugin->jsonSerialize());
							$deferred->resolve();
						})
						->otherwise(function($error) use (&$deferred) {
							$deferred->reject($error);
						});
				})
				->otherwise(function($error) use (&$deferred) {
					$deferred->reject($error);
				});
		end:
		return $deferred->promise();
	}

	public function update(string $plugin) : Promise {

	}

	public function uninstall(string $plugin) {

	}

	public function init(AsyncHandler $asyncHandler, Lockfile $lockfile) {
		$this->sourceManager = new SourceManager($asyncHandler);
		$this->defaultSource = $this->sourceManager->getDefaultSource();
		$this->lockfile = $lockfile;
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