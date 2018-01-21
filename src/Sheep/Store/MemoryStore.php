<?php
declare(strict_types=1);


namespace Sheep\Store;


use Sheep\Plugin;
use Sheep\Source\PluginNotFoundException;

class MemoryStore implements Store {
	protected $plugins = [];

	public function add(Plugin $plugin) {
		$this->plugins[strtolower($plugin->getName())] = $plugin->jsonSerialize();
	}

	public function update(Plugin $plugin) {
		$this->add($plugin);
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
		if (!$this->exists($plugin)) throw new PluginNotFoundException($plugin);
		return Plugin::fromArray($this->plugins[strtolower($plugin)]);
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
