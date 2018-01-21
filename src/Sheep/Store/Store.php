<?php
declare(strict_types=1);


namespace Sheep\Store;


use Sheep\Plugin;
use Sheep\Source\PluginNotFoundException;

interface Store {

	public function add(Plugin $plugin);

	public function update(Plugin $plugin);

	public function remove(string $plugin);

	/**
	 * @param string $plugin
	 * @return Plugin
     * @throws PluginNotFoundException
	 */
	public function get(string $plugin);

	public function getAll(): array;

	public function exists(string $plugin): bool;

	public function persist();
}
