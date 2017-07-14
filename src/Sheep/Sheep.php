<?php
declare(strict_types = 1);


namespace Sheep;

use React\Promise\Deferred;
use React\Promise\Promise;
use Sheep\Async\AsyncHandler;
use Sheep\Source\Source;
use Sheep\Source\SourceManager;
use Sheep\Utils\Error;
use Sheep\Store\Store;

/**
 * The Sheep API.
 * @package Sheep
 */
class Sheep {
	private static $instance;

	/** @var SourceManager */
	private $sourceManager;
	private $defaultSource;
	/** @var Store */
	private $store;

	public function info(string $plugin, string $version, Source $source = null) : Promise {
		if($source === null) $source = $this->defaultSource;
		return $source->resolve($plugin, $version);
	}

	public function install(string $plugin, string $version, Source $source = null) : Promise {
		$deferred = new Deferred();
		if($source === null) $source = $this->defaultSource;
		if($this->store->exists($plugin)) {
			$deferred->reject(new Error("Plugin already installed.", Error::E_PLUGIN_ALREADY_INSTALLED));
			goto end;
		}

		$this->info($plugin, $version, $source)
				->then(function(Plugin $plugin) use (&$deferred, &$source) {
					$source->install($plugin)
						->then(function() use (&$deferred, $plugin) {
							$plugin->setState(PluginState::STATE_INSTALLED);
							$this->store->add($plugin);
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

	public function update(string $plugin, Source $source = null) : Promise {
		$deferred = new Deferred();
		if($source === null) $source = $this->defaultSource;

		if(($current = $this->store->get($plugin)) !== null) {
			$this->info($current["name"], $current["version"])
				->then(function(Plugin $target) use (&$deferred, &$source, $current) {
						$source->update($target)
							->then(function() use (&$deferred, $target) {
								$target->setState(PluginState::STATE_UPDATING);
								$this->store->update($target);
								$deferred->resolve();
							})
							->otherwise(function(Error $error) use (&$deferred) {
								$deferred->reject($error);
							});
				})
				->otherwise(function(Error $error) use (&$deferred) {
					$deferred->reject($error);
				});
		} else {
			$deferred->reject(new Error("Plugin not found in store.", Error::E_PLUGIN_NOT_IN_LOCK));
		}

		return $deferred->promise();
	}

	// should probably not be promised based as it isn't asynchronous at all
	public function uninstall(string $plugin) : Promise {
		$deferred = new Deferred();
		if(($p = $this->store->get($plugin)) === null) { // "Yes, you should throw an exception
														//   Try/catch is too much work?"
			$deferred->reject(new Error("Plugin does not exist.", Error::E_PLUGIN_NOT_IN_LOCK));
			return $deferred->promise();
		}
		$p->setState(PluginState::STATE_NOT_INSTALLED);
		$this->store->update($p);
		$deferred->resolve();

		return $deferred->promise();
	}

	public function init(AsyncHandler $asyncHandler, Store $store) {
		$this->sourceManager = new SourceManager($asyncHandler);
		$this->defaultSource = $this->sourceManager->getDefaultSource();
		$this->store = $store;
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