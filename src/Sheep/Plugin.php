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


class Plugin implements \JsonSerializable {
	/** @internal */
	public $update;
	public $state = PluginState::STATE_NOT_INSTALLED;
	protected $source;
	protected $name;
	protected $version;
	protected $dependencies;
	protected $info = [];
	protected $uri;

	/**
	 * Constructs a plugin from the given data.
	 *
	 * @param array $data
	 * @return Plugin
	 */
	public static function fromArray(array $data): Plugin {
		$plugin = new self();

		$plugin->source = $data["source"];
		$plugin->name = $data["name"];
		$plugin->version = $data["version"];
		$plugin->update = @$data["update"]; // hack - should actually migrate configs in a safe manner
		$plugin->state = $data["state"];

		$plugin->info = [
			"source" => $plugin->source,
			"name" => $plugin->name,
			"version" => $plugin->version
		];

		return $plugin;
	}

	/**
	 * Returns human-readable, formatted info on the plugin.
	 * @return string
	 */
	public function __toString(): string {
		return "@$this->source/$this->name:$this->version";
	}

	public function getName(): string {
		return $this->name;
	}

	public function getVersion(): string {
		return $this->version;
	}

	public function getUpdate(): string {
		return $this->update;
	}

	public function getDependencies(): array {
		return $this->dependencies;
	}

	public function getInfo(): array {
		return $this->info;
	}

	public function getUri(): string {
		return $this->uri;
	}

	public function jsonSerialize() {
		return [
			"source" => $this->source,
			"name" => $this->name,
			"version" => $this->version,
			"update" => $this->update,
			"state" => $this->state,
		];
	}

	/**
	 * @return int
	 */
	public function getState(): int {
		return $this->state;
	}

	/**
	 * @param int $state
	 */
	public function setState(int $state) {
		$this->state = $state;
	}
}
