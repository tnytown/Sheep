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

use pocketmine\scheduler\PluginTask;
use pocketmine\Server;
use Sheep\PluginState;
use Sheep\SheepPlugin;

class RestartTask extends PluginTask {
	/** @var Server */
	private $server;
	/** @var SheepPlugin */
	private $plugin;

	private $restartTick;
	private $config;

	public function __construct(SheepPlugin $owner) {
		parent::__construct($owner);

		$this->server = $this->plugin->getServer();
		$this->plugin = $owner;
		$this->config = $this->plugin->getConfig()->getNested("updater.restart");

		$this->restartTick = $this->config["delay"];
	}

	public function onRun(int $currentTick) {
		if($this->config["player-threshold"] !== 0 &&
			count($this->server->getOnlinePlayers()) >= $this->config["player-threshold"]) {
			$this->restartTick = $this->config["delay"];
			return;
		}

		if($currentTick < $this->restartTick) {
			$this->message($this->config["warning"],
				count($this->plugin->getStore()->getByState(PluginState::STATE_UPDATING)),
				($this->restartTick - $currentTick) / 20);
			return;
		}

		if($currentTick >= $this->restartTick) {
			$this->message($this->config["alert"],
				count($this->plugin->getStore()->getByState(PluginState::STATE_UPDATING)));
			$this->server->shutdown();
		}
	}

	private function message(array $messages, ...$args) {
		$this->plugin->getLogger()->warning(sprintf($messages["console"], ...$args));
		$this->server->broadcastMessage(sprintf($messages["broadcast"], ...$args));
	}
}
