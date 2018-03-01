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


namespace Sheep;

use React\Promise\Deferred;
use React\Promise\Promise;
use Sheep\Async\AsyncHandler;
use Sheep\Store\PluginNotFoundException;
use Sheep\Source\Source;
use Sheep\Source\SourceManager;
use Sheep\Store\Store;
use Sheep\Utils\Error;

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

	public function __construct() {
		define("Sheep\\VERSION", yaml_parse(file_get_contents(\Phar::running() . "/plugin.yml"))["version"]);
	}

	public static function getInstance(): Sheep {
		if(!self::$instance) {
			self::$instance = new Sheep();
		}
		return self::$instance;
	}

	public function install(string $plugin, string $version, ?Source $source = null): Promise {
		$deferred = new Deferred();
		if($source === null) {
			$source = $this->defaultSource;
		}
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

	public function info(string $plugin, string $version, ?Source $source = null): Promise {
		if($source === null) {
			$source = $this->defaultSource;
		}
		return $source->resolve($plugin, $version);
	}

	public function update(string $plugin, ?Source $source = null): Promise {
		$deferred = new Deferred();
		if($source === null) {
			$source = $this->defaultSource;
		}

		try {
			$current = $this->store->get($plugin);
			$this->info($current->getName(), $current->getVersion(), $source)
				->then(function(Plugin $target) use (&$deferred, &$source, $current) {
					$source->update($target)
						->then(function(Plugin $updated) use (&$deferred, $target) {
							$target->setState(PluginState::STATE_UPDATING);
							$target->update = $updated->getVersion();
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
		} catch(PluginNotFoundException $e) {
			$deferred->reject(new Error("Plugin not found in store.", Error::E_PLUGIN_NOT_IN_LOCK));
		}

		return $deferred->promise();
	}

	public function uninstall(string $plugin): Promise {
		$deferred = new Deferred();
		try {
			$p = $this->store->get($plugin);
			$p->setState(PluginState::STATE_NOT_INSTALLED);
			$this->store->update($p);
			$deferred->resolve();
		} catch(PluginNotFoundException $e) {
			$deferred->reject(new Error("Plugin does not exist.", Error::E_PLUGIN_NOT_IN_LOCK));
		}

		return $deferred->promise();
	}

	public function init(AsyncHandler $asyncHandler, Store $store) {
		$this->sourceManager = new SourceManager($asyncHandler);
		$this->defaultSource = $this->sourceManager->getDefaultSource();
		$this->store = $store;
	}

	/**
	 * @return SourceManager
	 */
	public function getSourceManager(): SourceManager {
		return $this->sourceManager;
	}
}
