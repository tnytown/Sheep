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


namespace Sheep\Updater;


use pocketmine\scheduler\Task;
use pocketmine\Server;
use Sheep\PluginState;
use Sheep\Sheep;
use Sheep\Source\SourceNotFoundException;
use Sheep\Store\Store;

class UpdaterTask extends Task {
	private $logger;
	private $config;

	private $updateHandler;
	private $api;
	private $store;

	public function __construct(UpdateHandler $handler, Sheep $api, Store $store) {
		$plugin = Server::getInstance()->getPluginManager()->getPlugin("Sheep");

		$this->logger = $plugin->getLogger();
		$this->config = $plugin->getConfig()->getNested("updater");

		$this->updateHandler = $handler;
		$this->api = $api;
		$this->store = $store;
	}

	public function onRun(int $currentTick) {
		$this->logger->info($this->config["message"]);

		$eligible = $this->store->getByState(PluginState::STATE_INSTALLED);
		$updater = function(callable $endFunc, array &$eligible) use (&$updater): void {
			// if there are no remaining plugins to be updated, call the end function
			if(count($eligible) === 0) {
				$endFunc();
				return;
			}

			// take a plugin from the queue
			$plugin = array_pop($eligible);
			$source = null;
			try {
				// try to get the plugin's source
				$source = $this->api->getSourceManager()->get($plugin["source"]);
			} catch(SourceNotFoundException $e) {
				// update the next plugin in the queue
				$updater($endFunc, $eligible);
				return;
			}

			// update the plugin with the source
			$this->api->update($plugin["name"], $source)->always(function() use (&$updater, &$endFunc, &$eligible) {
				// update the next plugin in the queue
				$updater($endFunc, $eligible);
			});
		};

		$updater(function(): void {
			if(count($this->store->getByState(PluginState::STATE_UPDATING)) > 0)
				$this->updateHandler->handlePluginsUpdated();
		}, $eligible);
	}
}
