<?php
declare(strict_types=1);


namespace Sheep\Store;


use Sheep\Plugin;

class MemoryStore implements Store {
	protected $plugins;

	public function add(Plugin $plugin) {
		$this->plugins[strtolower($plugin->getName())] = $plugin->jsonSerialize();
	}

	public function update(Plugin $plugin) {
		$this->add($plugin);
	}

	public function remove(string $plugin) {
		unset($this->plugins[strtolower($plugin)]);
	}

	public function get(string $plugin) {
		if(!$this->exists($plugin)) return null;
		return Plugin::fromArray($this->plugins[strtolower($plugin)]);
	}

	public function getAll(): array {
		return $this->plugins;
	}

	public function exists(string $plugin) : bool {
		return isset($this->plugins[strtolower($plugin)]);
	}

	public function persist() {
	}
}