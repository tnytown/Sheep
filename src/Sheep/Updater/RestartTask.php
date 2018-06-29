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

use pocketmine\plugin\Plugin;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use Sheep\PluginState;
use Sheep\Store\Store;

class RestartTask extends Task {
	/** @var Server */
	private $server;
	private $plugin;

	private $restartTick;
	private $config;
	private $store;

	public function __construct(Plugin $owner, Store $store) {
		$this->server = Server::getInstance();
		$this->plugin = $this->server->getPluginManager()->getPlugin("Sheep");

		$this->config = $this->plugin->getConfig()->getNested("updater.restart");

		$this->restartTick = $this->server->getTick() + $this->config["delay"] * UpdateHandler::MINUTES_TO_TICKS;
		$this->store = $store;
	}

	public function onRun(int $currentTick) {
		if($this->config["player-threshold"] !== 0 &&
			count($this->server->getOnlinePlayers()) >= $this->config["player-threshold"]) {
			$this->restartTick = $this->server->getTick() + (int) ($this->config["delay"] * UpdateHandler::MINUTES_TO_TICKS);
			return;
		}

		if($currentTick < $this->restartTick) {
			$this->message($this->config["warning"],
				count($this->store->getByState(PluginState::STATE_UPDATING)),
				($this->restartTick - $currentTick) / (20 * 60));
			return;
		}

		if($currentTick >= $this->restartTick) {
			$this->message($this->config["alert"],
				count($this->store->getByState(PluginState::STATE_UPDATING)));
			$this->server->shutdown();
		}
	}

	private function message(array $messages, ...$args) {
		$this->plugin->getLogger()->warning(sprintf($messages["console"], ...$args));
		$this->server->broadcastMessage(sprintf($messages["broadcast"], ...$args));
	}
}
