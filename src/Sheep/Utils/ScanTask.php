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
use pocketmine\scheduler\Task;
use pocketmine\Server;
use Sheep\PluginState;
use Sheep\Sheep;
use Sheep\Store\Store;

class ScanTask extends Task {
	private $store;

	public function __construct(Store $store) {
		$this->store = $store;
	}

	public function onRun(int $currentTick) {
		$server = Server::getInstance();
		$sheep = Sheep::getInstance();

		foreach($server->getPluginManager()->getPlugins() as $plugin) {
			if($this->store->exists($plugin->getName())) {
				continue;
			}

			$sheep->info($plugin->getName(), $plugin->getDescription()->getVersion())// search default source
			->then(function(\Sheep\Plugin $plugin) {
				$plugin->setState(PluginState::STATE_INSTALLED);
				$this->store->add($plugin);
			})
				->otherwise(function(Error $error) use (&$plugin) {
					$this->store->add(\Sheep\Plugin::fromArray([
						"source" => "Local",
						"name" => $plugin->getName(),
						"version" => $plugin->getDescription()->getVersion(),
						"state" => PluginState::STATE_INSTALLED
					]));
				});
		}
	}
}
