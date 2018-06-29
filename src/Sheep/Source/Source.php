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


namespace Sheep\Source;


use React\Promise\Promise;
use Sheep\Plugin;

interface Source {

	/**
	 * Searches for a plugin.
	 *
	 * @param string $query The name of the plugin.
	 * @return Promise
	 */
	public function search(string $query): Promise;

	/**
	 * Resolves a single version of a plugin.
	 *
	 * @param string $plugin
	 * @param string $version
	 * @return Promise
	 */
	public function resolve(string $plugin, string $version): Promise;

	/**
	 * Installs a given Plugin.
	 *
	 * @param Plugin ...$plugin The plugin(s) in question.
	 * @return Promise
	 */
	public function install(Plugin... $plugin): Promise;

	/**
	 * Updates a given Plugin. Implement this function to gain custom
	 * functionality or to work around repository quirks.
	 *
	 * @param Plugin $plugin The plugin in question.
	 * @return Promise
	 */
	public function update(Plugin $plugin): Promise;
}
