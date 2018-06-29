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

use pocketmine\Server;
use pocketmine\plugin\Plugin;
use Sheep\Sheep;
use Sheep\Store\Store;

class UpdateHandler {
	const MINUTES_TO_TICKS = 20*60;

	private $server;
	private $plugin;
	private $api;

	private $store;
	private $config;
	private $tasksRegistered;

	public function __construct(Server $server, Plugin $taskOwner, Sheep $api, Store $store, array $config) {
		$this->server = $server;
		$this->plugin = $taskOwner;
		$this->api = $api;
		$this->store = $store;
		$this->config = $config;
	}

	public function handlePluginsUpdated(): void {
		if($this->tasksRegistered) return;
		$this->tasksRegistered = true;
		if($this->config["nag"]["interval"] !== 0 && !$this->isRestartEnabled()) {
			$this->plugin->getScheduler()
					->scheduleRepeatingTask(new NagTask($this->store, $this->config["nag"]["message"]),
						(int) ($this->config["nag"]["interval"] * self::MINUTES_TO_TICKS));
		}

		$restartInterval = (int) ($this->config["restart"]["warning"]["interval"] * self::MINUTES_TO_TICKS);
		if($this->isRestartEnabled())
			$this->plugin->getScheduler()
				->scheduleRepeatingTask(new RestartTask($this->plugin, $this->store),
					$restartInterval !== 0
						? $restartInterval : ($this->config["restart"]["delay"] * self::MINUTES_TO_TICKS));
	}

	public function isRestartEnabled(): bool {
		return $this->config["restart"]["enabled"];
	}
}
