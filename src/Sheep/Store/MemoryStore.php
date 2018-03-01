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


namespace Sheep\Store;


use Sheep\Plugin;

class MemoryStore implements Store {
	protected $plugins = [];

	public function update(Plugin $plugin) {
		$this->add($plugin);
	}

	public function add(Plugin $plugin) {
		$this->plugins[strtolower($plugin->getName())] = $plugin->jsonSerialize();
	}

	public function remove(string $plugin) {
		unset($this->plugins[strtolower($plugin)]);
	}

	/**
	 * @param string $plugin
	 * @return Plugin
	 * @throws PluginNotFoundException
	 */
	public function get(string $plugin) {
		if(!$this->exists($plugin)) {
			throw new PluginNotFoundException($plugin);
		}
		return Plugin::fromArray($this->plugins[strtolower($plugin)]);
	}

	/**
	 * @param int $state
	 * @return array
	 */
	public function getByState(int $state): array {
		$plugins = [];
		foreach($this->plugins as $plugin) {
			if($plugin["state"] === $state) $plugins[] = &$plugin;
		}
		return $plugins;
	}

	public function getAll(): array {
		return $this->plugins;
	}


	public function exists(string $plugin): bool {
		return isset($this->plugins[strtolower($plugin)]);
	}

	public function persist() {
	}
}
