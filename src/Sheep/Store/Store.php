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

	/**
	 * @param int $state
	 * @return array
	 */
	public function getByState(int $state): array;

	public function exists(string $plugin): bool;

	public function persist();
}
