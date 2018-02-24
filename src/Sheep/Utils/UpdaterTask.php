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


namespace Sheep\Utils;


use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginLogger;
use pocketmine\scheduler\PluginTask;
use Sheep\PluginState;
use Sheep\Sheep;
use Sheep\Source\SourceNotFoundException;
use Sheep\Store\Store;

class UpdaterTask extends PluginTask {
	private $logger;
	private $config;

	private $api;
	private $store;

	public function __construct(Plugin $owner, PluginLogger $logger, array $config, Sheep $api, Store $store) {
		parent::__construct($owner);

		$this->logger = $logger;
		$this->config = $config;
		$this->api = $api;
		$this->store = $store;
	}

	public function onRun(int $currentTick) {
		$this->logger->info($this->config["message"]);

		$eligible = $this->store->getByState(PluginState::STATE_INSTALLED);
		$updater = function(callable $endFunc, array &$eligible) use (&$updater): void {
			if(count($eligible) === 0) {
				$endFunc();
				return;
			}

			$plugin = array_pop($eligible);
			$source = null;
			try {
				$source = $this->api->getSourceManager()->get($plugin->getInfo()["source"]);
			} catch(SourceNotFoundException $e) {
				$updater($endFunc, $eligible);
			}

			$this->api->update($plugin->getName(), $source)->always(function() use (&$updater, &$endFunc, &$eligible) {
				$updater($endFunc, $eligible);
			});
		};

		$updater(function(): void {
			echo "i'm a terrible person";
		}, $eligible);
	}
}
